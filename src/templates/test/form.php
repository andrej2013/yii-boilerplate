<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var andrej2013\yiiboilerplate\templates\test\Generator $generator
 */
?>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
(function( $ ){
    $( document ).ready(function() {
        $(':checkbox').each(function () { this.checked = true });
     });
})( jQuery );
</script>

<?php
echo $form->field($generator, 'models')
    ->listBox(\andrej2013\yiiboilerplate\templates\test\Generator::getModels(), ['multiple' => true, 'size' => 15]);
echo $form->field($generator, 'testPath');
