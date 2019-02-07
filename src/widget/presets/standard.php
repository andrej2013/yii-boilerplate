<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 *
 * standard preset returns the basic toolbar configuration set for CKEditor.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */
return isset(Yii::$app->params['CKEditor']['standard']) ? Yii::$app->params['CKEditor']['standard'] :
[
    'height' => 300,
    'toolbarGroups' => [
        ['name' => 'clipboard', 'groups' => ['mode', 'undo', 'selection', 'clipboard', 'doctools']],
        ['name' => 'editing', 'groups' => ['tools', 'about']],
        '/',
        ['name' => 'paragraph', 'groups' => ['templates', 'list', 'indent', 'align']],
        ['name' => 'insert'],
        '/',
        ['name' => 'styles'],
        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
        ['name' => 'colors'],
        ['name' => 'links'],
        ['name' => 'others'],
    ],
    'removeButtons' => 'Smiley,Iframe'
];
