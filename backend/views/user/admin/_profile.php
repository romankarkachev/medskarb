<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $profile common\models\Profile */

$wrapper = 'col-sm-9';
$fieldCodeTemplate = '{label}<div class="' . $wrapper . '"><div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub"></i></span></div>{error}</div>';
?>

<?php $this->beginContent('@backend/views/user/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => $wrapper,
        ],
    ],
]); ?>

<div class="card">
    <div class="card-block">
        <?= $form->field($profile, 'name')->textInput(['placeholder' => 'Введите свое имя или наименование организации', 'title' => 'Введите свое имя или наименование организации']) ?>

        <div class="form-group row field-profile-role">
            <label class="control-label col-sm-3" for="profile-role"><?= $profile->user->getAttributeLabel('role_id') ?></label>
            <div class="col-sm-9">
                <input type="text" id="profile-role" class="form-control" value="<?= $profile->user->getRoleDescription() ?>" aria-invalid="false" readonly>
            </div>
        </div>
    </div>
    <div class="card-footer text-muted">
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Пользователи', ['/users'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('user', 'Save'), ['class' => 'btn btn-primary btn-lg']) ?>

    </div>
</div>
<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
