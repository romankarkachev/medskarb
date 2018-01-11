<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Profile */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $username string */

$this->title = Yii::t('user', 'Profile settings') . ' ' . $username . ' | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = Yii::t('user', 'Profile');
?>

<?php $form = ActiveForm::begin([
    'id' => 'profile-form',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
    'validateOnBlur'         => false,
]); ?>

<div class="card">
    <div class="card-block">
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'name')->textInput(['placeholder' => 'Введите свое имя или наименование организации', 'title' => 'Введите свое имя или наименование организации']) ?>

            </div>
            <div class="col-md-4">
                <label class="control-label" for="profile-role"><?= $model->user->getAttributeLabel('role_id') ?></label>
                <input type="text" id="profile-role" class="form-control" value="<?= $model->user->getRoleDescription() ?>" aria-invalid="false" readonly>
            </div>
        </div>
    </div>
    <div class="card-footer text-muted">
        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('user', 'Save'), ['class' => 'btn btn-info btn-lg']) ?>

        <?= Html::a('Изменить пароль', ['/account'], ['class' => 'btn btn-lg']) ?>

    </div>
</div>
<?php ActiveForm::end(); ?>
