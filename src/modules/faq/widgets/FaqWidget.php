<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\faq\widgets;

use andrej2013\yiiboilerplate\models\Language;
use andrej2013\yiiboilerplate\modules\faq\models\Faq;
use yii\bootstrap\Widget;

class FaqWidget extends Widget
{
    /**
     * @var bool|int if id defined then this FAQ will be opened
     */
    public $id = false;//faq_id open by default

    /**
     * @var bool|string title for FAQ page
     */
    public $title = false;

    /**
     * @var bool|string breadcrumbs for FAQ page
     */
    public $breadcrumbs = false;

    /**
     * @var string path to your view
     */
    public $viewPath = 'faq_list';


    /**
     * @param int $limit
     * @return string
     */
    public function run()
    {
        $models = Faq::find()
            ->joinWith('language')
            ->andWhere(['place' => Faq::PLACE_BACKEND])
            ->andWhere([Language::tableName() . '.language' => \Yii::$app->language])
            ->orderBy('order ASC')
            ->all();

        $faqs = [];
        foreach ($models as $faq) {
            // Set table
            if (!isset($faqs[$faq->level])) {
                $faqs[$faq->level] = [];
            }

            $faqs[$faq->level][] = $faq;
        }

        return $this->render($this->viewPath, [
            'faqs' => $faqs,
            'id' => $this->id,
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
        ]);
    }
}