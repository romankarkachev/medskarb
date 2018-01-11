<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model dektrium\user\models\SettingsForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $username string */

$this->title = Yii::t('user', 'Account settings') . ' ' . $username . ' | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = Yii::t('user', 'Account');
?>

<?php $form = ActiveForm::begin([
    'id' => 'account-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
]); ?>

<div class="card">
    <div class="card-block">
        <?= $form->field($model, 'email', [
            'options' => ['class' => 'form-group form-group-default'],
            'template' => '{label}<div class="controls">{input}</div>',
            'labelOptions' => ['class' => null],
            'inputOptions' => ['placeholder' => 'Введите E-mail'],
        ]) ?>

        <?= $form->field($model, 'email', ['template' => '{error}']) ?>

        <?= $form->field($model, 'username', [
            'options' => ['class' => 'form-group form-group-default'],
            'template' => '{label}<div class="controls">{input}</div>',
            'labelOptions' => ['class' => null],
            'inputOptions' => ['placeholder' => 'Введите имя пользователя'],
        ]) ?>

        <?= $form->field($model, 'username', ['template' => '{error}']) ?>

        <?= $form->field($model, 'new_password', [
            'options' => ['class' => 'form-group form-group-default'],
            'template' => '{label}<div class="controls">{input}</div>',
            'labelOptions' => ['class' => null],
            'inputOptions' => ['placeholder' => 'Введите новый пароль, чтобы заменить старый'],
        ])->passwordInput() ?>

        <?= $form->field($model, 'new_password', ['template' => '{error}']) ?>

        <hr/>

        <?= $form->field($model, 'current_password', [
            'options' => ['class' => 'form-group form-group-default'],
            'template' => '{label}<div class="controls">{input}</div>',
            'labelOptions' => ['class' => null],
            'inputOptions' => ['placeholder' => 'Для сохранения любых данных необходимо ввести старый пароль'],
        ])->passwordInput() ?>

        <?= $form->field($model, 'current_password', ['template' => '{error}']) ?>

    </div>
    <div class="card-footer text-muted">
        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('user', 'Save'), ['class' => 'btn btn-info btn-lg']) ?>

        <?= Html::a('Профиль', ['/profile'], ['class' => 'btn btn-lg']) ?>

    </div>
</div>
<?php ActiveForm::end(); ?>
