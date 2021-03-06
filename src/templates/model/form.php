<?php
/**
 * @var yii\web\View                      $this
 * @var yii\widgets\ActiveForm            $form
 * @var yii\gii\generators\form\Generator $generator
 */
echo "    <script src=\"https://code.jquery.com/jquery-1.9.1.min.js\"></script>";
echo "<script type='text/javascript'>
(function( $ ){
    $( document ).ready(function() {
        $(':checkbox').each(function () { this.checked = true });
     });
})( jQuery );

</script>";

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'tablePrefix');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'db');
echo $form->field($generator, 'generateRelations')
          ->dropDownList([
              \yii\gii\generators\model\Generator::RELATIONS_NONE        => Yii::t('yii', 'No relations'),
              \yii\gii\generators\model\Generator::RELATIONS_ALL         => Yii::t('yii', 'All relations'),
              \yii\gii\generators\model\Generator::RELATIONS_ALL_INVERSE => Yii::t('yii', 'All relations with inverse'),
          ]);
echo $form->field($generator, 'generateLabelsFromComments')
          ->checkbox();
echo $form->field($generator, 'generateModelClass')
          ->checkbox();
echo $form->field($generator, 'enableI18N')
          ->checkbox();
echo $form->field($generator, 'singularEntities')
          ->checkbox();
echo $form->field($generator, 'messageCategory');
if ($generator->errors) {
?>
<div class="panel panel-danger">
    <div class="panel-heading">Fix following errors</div>
    <div class="panel-body">
        <ul>
            <?php
            echo $form->errorSummary($generator);
            ?>
        </ul>
    </div>
</div>
<?php
}
?>
<!--<div class="panel panel-default">
    <div class="panel-heading">Translatable Behavior</div>
    <div class="panel-body">
        <?php
echo $form->field($generator, 'useTranslatableBehavior')
          ->checkbox();
echo $form->field($generator, 'languageTableName');
echo $form->field($generator, 'languageCodeColumn');
?>
        <div class="alert alert-warning" role="alert">
            <h4>Attention!</h4>

            <p>
                You must run <code>php composer.phar require 2amigos/yii2-translateable-behavior "*"</code> to
                install this package.
            </p>
        </div>
    </div>
</div>
-->
