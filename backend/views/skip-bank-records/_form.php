<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SkipBankRecords */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="skip-bank-records-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <?= $form->field($model, 'substring')->textarea(['rows' => 3, 'autofocus' => true, 'placeholder' => 'Введите искомую подстроку']) ?>

        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Игнор для банка', ['/skip-bank-records'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

            <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>
            <?php else: ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php endif; ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
