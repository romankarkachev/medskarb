<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\AuthItem;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $user common\models\User */

$wrapper = 'col-sm-9';
?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => $wrapper,
        ],
    ],
]); ?>

<div class="card">
    <div class="card-block">
        <?= $form->field($user, 'email')->textInput(['maxlength' => 255, 'placeholder' => 'Используется для авторизации в системе']) ?>

        <?= $form->field($user, 'username')->textInput(['maxlength' => 255, 'placeholder' => 'Используется для авторизации в системе'])->label('Логин') ?>

        <?= $form->field($user, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Введите имя']) ?>

        <?= $form->field($user, 'role_id')->widget(Select2::className(), [
            'data' => AuthItem::arrayMapForSelect2(),
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => '- выберите роль -'],
            'hideSearch' => true,
        ]); ?>

        <?= $form->field($user, 'password')->passwordInput(['placeholder' => 'Минимум 6 символов']) ?>

        <?= $form->field($user, 'password_confirm')->passwordInput(['placeholder' => 'Подтвердите пароль']) ?>

    </div>
    <div class="card-footer text-muted">
        <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> ' . Yii::t('user', 'Create'), ['class' => 'btn btn-success btn-lg']) ?>

    </div>
</div>
<?php ActiveForm::end(); ?>
