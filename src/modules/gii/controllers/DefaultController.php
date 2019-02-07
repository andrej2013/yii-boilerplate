<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 1/31/19
 * Time: 1:53 PM
 */
namespace andrej2013\yiiboilerplate\modules\gii\controllers;

use yii\gii\controllers\DefaultController as BaseController;

class DefaultController extends BaseController
{

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->layout = \Yii::$app->layout;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPreview($id, $file)
    {
        $generator = $this->loadGenerator($id);
        if ($generator->validate()) {
            foreach ($generator->generate() as $f) {
                if ($f->id === $file) {
                    $content = $f->preview();
                    if ($content !== false) {
                        return '<div class="content">' . $content . '</div>';
                    }
                    return '<div class="error">Preview is not available for this file type.</div>';
                }
            }
        }
        throw new NotFoundHttpException("Code file not found: $file");
    }

    public function actionDiff($id, $file)
    {
        $generator = $this->loadGenerator($id);
//        if ($generator->validate()) {
            foreach ($generator->generate() as $f) {
                if ($f->id === $file) {
                    return $this->renderPartial('diff', [
                        'diff' => $f->diff(),
                    ]);
                }
            }
//        }
        throw new NotFoundHttpException("Code file not found: $file");
    }

}