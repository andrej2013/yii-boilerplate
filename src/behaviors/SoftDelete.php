<?php
/**
 * Created by PhpStorm.
 * User: ben-g
 * Date: 18.01.2016
 * Time: 14:42
 */

namespace andrej2013\yiiboilerplate\behaviors;


use yii\behaviors\TimestampBehavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\db\Expression;

class SoftDelete extends TimestampBehavior
{
    /**
     * @var string SoftDelete attribute
     */
    public $attribute = "deleted_at";
    public $attribute2 = "deleted_by";

    /**
     * @var bool If true, this behavior will process '$model->delete()' as a soft-delete. Thus, the
     *           only way to truly delete a record is to call '$model->forceDelete()'
     */
    public $safeMode = true;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [ActiveRecord::EVENT_BEFORE_DELETE => 'doDeleteTimestamp'];
    }

    /**
     * Set the attribute with the current timestamp to mark as deleted
     *
     * @param Event $event
     */
    public function doDeleteTimestamp($event)
    {
        // do nothing if safeMode is disabled. this will result in a normal deletion
        if (!$this->safeMode) {
            return;
        }
        // remove and mark as invalid to prevent real deletion
        $this->remove();
        $event->isValid = false;
    }

    /**
     * Remove (aka soft-delete) record
     */
    public function remove()
    {
        // evaluate timestamp and set attribute
        $timestamp = new Expression('NOW()');
        if (\Yii::$app instanceof \yii\web\Application) {
            $user = (\Yii::$app->user->isGuest) ? 0 : \Yii::$app->user->id;
        } else {
            $user = 0;
        }

        $attribute = $this->attribute;
        $attribute2 = $this->attribute2;

        $this->owner->$attribute = $timestamp;
        $this->owner->$attribute2 = $user;
        $this->owner->updated_at = $timestamp;
        $this->owner->updated_by = $user;

        $this->owner->save(false, [$attribute, $attribute2, 'updated_at', 'updated_by']);
    }

    /**
     * Restore soft-deleted record
     */
    public function restore()
    {
        // evaluate timestamp and set attribute
        $timestamp = new Expression('NOW()');
        if (\Yii::$app instanceof \yii\web\Application) {
            $user = (\Yii::$app->user->isGuest) ? 0 : \Yii::$app->user->id;
        } else {
            $user = 0;
        }
        // mark attribute as null
        $attribute = $this->attribute;
        $attribute2 = $this->attribute2;

        $this->owner->$attribute = null;
        $this->owner->$attribute2 = null;
        $this->owner->updated_at = $timestamp;
        $this->owner->updated_by = $user;
        // save record
        $this->owner->save(false, [$attribute, $attribute2, 'updated_at', 'updated_by']);
    }

    /**
     * Delete record from database regardless of the $safeMode attribute
     */
    public function forceDelete()
    {
        // store model so that we can detach the behavior and delete as normal
        $model = $this->owner;
        $this->detach();
        $model->delete();
    }
}
