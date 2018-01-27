<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\TaxYearCalculationsSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchApplied bool */
?>

<div class="tax-year-calculations-search">

    <?php $form = ActiveForm::begin([
        'action' => ['/tax-year-calculations'],
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
            <div class="row">
                <div class="col-auto">
                    <?= $form->field($model, 'year')->widget(MaskedInput::className(), [
                        'clientOptions' => ['alias' =>  'numeric'],
                    ])->textInput([
                        'maxlength' => true,
                        'placeholder' => '0',
                    ]) ?>

                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Выполнить', ['class' => 'btn btn-info']) ?>

                <?= Html::a('Отключить отбор', ['/tax-year-calculations'], ['class' => 'btn btn-secondary']) ?>

            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
