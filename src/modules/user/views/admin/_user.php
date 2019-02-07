<?php

echo $form->field($user, 'email', [
        'template' => '{label}<div class="col-sm-9 has-feedback">{input}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span></div>{error}',
    ])
          ->textInput(['maxlength' => 255, 'type' => 'email']);
echo $form->field($user, 'username', [
    'template' => '{label}<div class="col-sm-9 has-feedback">{input}
            <span class="glyphicon glyphicon-user form-control-feedback"></span></div>{error}',
])
          ->textInput(['maxlength' => 255]);
echo $form->field($user, 'password', [
    'template' => '{label}<div class="col-sm-9 has-feedback">{input}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span></div>{error}',
])
          ->passwordInput();
