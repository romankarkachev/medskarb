<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<?php $this->beginContent('@backend/views/user/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9',
        ],
    ],
]); ?>

<div class="card">
    <div class="card-block">
        <?= $this->render('_user_ex', ['form' => $form, 'user' => $user]) ?>

    </div>
    <div class="card-footer text-muted">
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Пользователи', ['/users'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('user', 'Save'), ['class' => 'btn btn-primary btn-lg']) ?>

    </div>
</div>
<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
