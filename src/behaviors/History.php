<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 19/04/2016
 * Time: 16:56
 */

namespace andrej2013\yiiboilerplate\behaviors;

use bupy7\activerecord\history\behaviors\History as BaseHistory;
use yii\web\Application;

class History extends BaseHistory
{
    protected function getCreatedBy()
    {
        if (!empty($this->module->user) && \Yii::$app instanceof Application) {
            return $this->module->user->id;
        }

        return 0;
    }

    /**
     * @inhertidoc
     */
    public function init()
    {
        $this->module = new \stdClass();
        $this->module->db = \Yii::$app->getDb();
        if (php_sapi_name() != "cli") {
            $this->module->user = \Yii::$app->getUser();
        } else {
            $this->module->user = null;
        }
        $this->module->storage = 'bupy7\activerecord\history\storages\Database';
        $this->skipAttributes = array_fill_keys($this->skipAttributes, true);
    }
}
