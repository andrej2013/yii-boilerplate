<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 3/1/2017
 * Time: 11:57 AM
 */

/**
 * @var \andrej2013\yiiboilerplate\templates\test\Generator $generator
 * @var \yii\db\ActiveRecord $model ;
 */
//@TODO finish this file
$model = $generator->modelClass;
$model = new $model;
echo "<?php\n";
?>
return [
<?php
for ($i = 1; $i < ($generator->getGeneratedFixtures() + 1); $i++) {
    foreach ($model->attributes as $attribute => $temp) {
        if (in_array($attribute, $generator->avoidColumns())) {
            continue;
        }
        $models[$i][$attribute] = $generator->getProperAttributeValue($attribute, $i);
    }
}
foreach ($models as $i => $model) {
    echo str_repeat(' ', 4) . "'{$generator->getModelShortName()}$i' => [\n";
    foreach ($model as $attribute => $value) {
        echo str_repeat(' ', 8) . "'$attribute' => '$value',\n";
    }
    echo str_repeat(' ', 4) . "],\n";
}
?>
];
