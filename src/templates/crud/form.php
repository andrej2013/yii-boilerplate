<?php
/**
 * @var yii\web\View                                        $this
 * @var yii\widgets\ActiveForm                              $form
 * @var \andrej2013\yiiboilerplate\templates\crud\Generator $generator
 */
echo "    <script src=\"https://code.jquery.com/jquery-1.9.1.min.js\"></script>";
echo "<script type='text/javascript'>
(function( $ ){
    $( document ).ready(function() {
        $('#generator-modelclass').blur(function(){
            var modelClass = $('#generator-modelclass').val();
            $('#generator-searchmodelclass').val(modelClass.replace(/models/gi, 'models\\\search'));
            $('#generator-controllerclass').val(modelClass.replace(/models/gi,'controllers') + 'Controller');
            $('#generator-viewpath').val(modelClass.replace(/models?/gi,'views').replace(/\\\[^\\\]*$/,'').replace(/\\\/gi,'/').replace(/app/gi,'@app') );
        });
        $('#check-all').click(function(){
            var checked = this.checked;
            // iterate .default-view-files
//            $(':checkbox').each(function () { this.checked = checked });
             $('.default-view-files')
               .find(\"input[type='checkbox']\")
               .prop('checked', this.checked);
        });
         $('.default-view-files')
           .find(\"input[type='checkbox']\")
           .prop('checked', true).attr('checked', true)

//        $(':checkbox').each(function () { this.checked = true });
     });
})( jQuery );

</script>";
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'controllerApiModule');
echo $form->field($generator, 'overwriteRestControllerClass')
          ->checkbox();
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'pathPrefix');
echo $form->field($generator, 'enableI18N')
          ->checkbox();
echo $form->field($generator, 'singularEntities')
          ->checkbox();
echo $form->field($generator, 'indexWidgetType')
          ->dropDownList([
                  'grid' => 'GridView',
                  'list' => 'ListView',
              ]);
echo $form->field($generator, 'formLayout')
          ->dropDownList([
                  /* Form Types */
                  'vertical'   => 'vertical',
                  'horizontal' => 'horizontal',
                  'inline'     => 'inline',
              ]);
echo $form->field($generator, 'twoColumnsForm')
          ->dropDownList([
                  /* Form Types */
                  false => 'No',
                  true  => 'Yes',
              ]);
echo $form->field($generator, 'actionButtonClass')
          ->dropDownList([
                  '\\kartik\\grid\\ActionColumn::class' => 'Default',
              ]);
echo $form->field($generator, 'providerList')
          ->checkboxList($generator->generateProviderCheckboxListData());
?>
<div class="col-sm-6">
    <div class="panel panel-default">
        <div class="panel-heading">Index page</div>
        <div class="panel-body">
            <?php
            echo $form->field($generator, 'generateExportButton')->checkbox();
            echo $form->field($generator, 'generateExtendedSearch')->checkbox();
            echo $form->field($generator, 'generateGridConfig')->checkbox();
            ?>
        </div>
    </div>
</div>
<div class="col-sm-6">
    <div class="panel panel-default">
        <div class="panel-heading">View page</div>
        <div class="panel-body">
            <?php
            echo $form->field($generator, 'generateCopyButton')->checkbox();
            echo $form->field($generator, 'generateViewActionButtons')->checkbox();
            ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-sm-6">
    <div class="panel panel-default">
        <div class="panel-heading">Create and Update page</div>
        <div class="panel-body">
            <?php
            echo $form->field($generator, 'generateSaveAndNew')->checkbox();
            echo $form->field($generator, 'generateSaveAndClose')->checkbox();
            ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>
