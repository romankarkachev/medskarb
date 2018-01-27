<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\Periods;

/* @var $this yii\web\View */
/* @var $model common\models\TaxQuarterCalculationsSearch */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $searchApplied bool */
?>

<div class="tax-quarter-calculations-search">
    <?php $form = ActiveForm::begin([
        'action' => ['/tax-quarter-calculations'],
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
                <div class="col-md-2">
                    <?= $form->field($model, 'searchPeriod')->widget(Select2::className(), [
                        'data' => Periods::arrayMapForSelect2(true, 'quarter_num <> 4'),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>

                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Выполнить', ['class' => 'btn btn-info']) ?>

                <?= Html::a('Отключить отбор', ['/tax-quarter-calculations'], ['class' => 'btn btn-secondary']) ?>

            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
