<?php

use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaxCalculations */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="col-md-6">
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="taxcalculations-rate"><?= $model->attributeLabels()['rate'] ?></label>
                        <p class="form-control"><?= Yii::$app->formatter->asInteger($model->rate) ?> %</p>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="taxcalculations-dt"><?= $model->attributeLabels()['dt'] ?></label>
                        <p class="form-control"><?= Yii::$app->formatter->asDecimal($model->dt, 2) ?> <i class="fa fa-rub" aria-hidden="true"></i></p>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="taxcalculations-kt"><?= $model->attributeLabels()['kt'] ?></label>
                        <p class="form-control"><?= Yii::$app->formatter->asDecimal($model->kt, 2) ?> <i class="fa fa-rub" aria-hidden="true"></i></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label" for="taxcalculations-diff"><?= $model->attributeLabels()['diff'] ?></label>
                        <p class="form-control"><?= Yii::$app->formatter->asDecimal($model->diff, 2) ?> <i class="fa fa-rub" aria-hidden="true"></i></p>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="taxcalculations-min"><?= $model->attributeLabels()['min'] ?></label>
                        <p class="form-control"><?= Yii::$app->formatter->asDecimal($model->min, 2) ?> <i class="fa fa-rub" aria-hidden="true"></i></p>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="taxcalculations-amount"><?= $model->attributeLabels()['amount'] ?></label>
                        <p class="form-control" title="Сумма налога = MIN(Минимум, (Доходы - Расходы) × Ставка налога ÷ 100)"><?= Yii::$app->formatter->asDecimal($model->amount, 2) ?> <i class="fa fa-rub" aria-hidden="true"></i></p>
                    </div>
                </div>
            </div>
<?= $form->field($model, 'rate')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'dt')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'kt')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'diff')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'min')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount')->hiddenInput()->label(false) ?>
