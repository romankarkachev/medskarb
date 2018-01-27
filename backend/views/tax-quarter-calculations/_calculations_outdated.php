<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\money\MaskMoney;

/* @var $this yii\web\View */
/* @var $model common\models\TaxQuarterCalculations */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="col-md-6">
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'rate', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon">%</span></div>{error}'])->widget(MaskMoney::className(), [
                            'precision' => 0,
                            'options' => [
                                'readonly' => true,
                                'title' => 'Ставка налога определяется автоматически из настроек',
                            ],
                        ]) ?>

                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'dt', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub" aria-hidden="true"></i></span></div>{error}'])->widget(MaskMoney::className(), [
                            'options' => [
                                'readonly' => true,
                            ],
                        ]) ?>

                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'kt', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub" aria-hidden="true"></i></span></div>{error}'])->widget(MaskMoney::className(), [
                            'options' => [
                                'readonly' => true,
                            ],
                        ]) ?>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'diff', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub" aria-hidden="true"></i></span></div>{error}'])->widget(MaskMoney::className(), [
                            'options' => [
                                'readonly' => true,
                            ],
                        ]) ?>

                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'min', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub" aria-hidden="true"></i></span></div>{error}'])->widget(MaskMoney::className(), [
                            'options' => [
                                'readonly' => true,
                                'title' => '1% от дохода',
                            ],
                        ]) ?>

                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'amount', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub" aria-hidden="true"></i></span></div>{error}'])->widget(MaskMoney::className(), [
                            'options' => [
                                'readonly' => true,
                                'title' => 'Сумма налога = MIN(Минимум, (Доходы - Расходы) × Ставка налога ÷ 100)',
                            ],
                        ]) ?>

                    </div>
                </div>
            </div>
