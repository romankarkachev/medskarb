<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $searchApplied bool */
?>

<div class="users-search">
    <?php $form = ActiveForm::begin([
        'action' => ['/users'],
        'method' => 'get',
        'options' => [
            'id' => 'frmSearch',
            'class' => $searchApplied === true ? 'collapse in' : 'collapse',
            'aria-expanded' => $searchApplied === true ? 'true' : 'false'
        ],
    ]); ?>

    <div class="card">
        <div class="card-header card-header-info card-header-inverse"><i class="fa fa-filter"></i> Форма отбора</div>
        <div class="card-block">
            <div class="form-group">
                <?= $form->field($model, 'searchEntire')->textInput(['placeholder' => 'Введите значение для поиска']) ?>

            </div>
            <div class="form-group">
                <?= Html::submitButton('Выполнить', ['class' => 'btn btn-primary']) ?>

                <?= Html::a('Сброс', ['/users'], ['class' => 'btn btn-secondary']) ?>

            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
