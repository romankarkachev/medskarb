<?php

/* @var $form yii\bootstrap\ActiveForm */
/* @var $user common\models\User */
?>

<div class="form-group row field-user-isblocked">
    <label class="form-control-label col-sm-3" for="user-isblocked">Активность</label>
    <div class="col-sm-9">
        <p class="<?= $user->blocked_at === null ? 'text-success"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Активен' : 'text-danger"><i class="fa fa-times-circle" aria-hidden="true"></i> Доступ запрещен' ?> <small class="text-muted">Для управления активностью используйте пункт &laquo;Блокировать&raquo; / &laquo;Разблокировать&raquo; в меню слева.</small></p>
    </div>
</div>

<?= $form->field($user, 'username')->textInput(['maxlength' => 255, 'placeholder' => 'Используется для авторизации в системе']) ?>

<?= $form->field($user, 'email')->textInput(['maxlength' => 255, 'placeholder' => 'Используется для авторизации в системе']) ?>

<?= $form->field($user, 'password')->passwordInput(['placeholder' => 'Минимум 6 символов']) ?>
