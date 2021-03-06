<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model common\models\TaxQuarterCalculations */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="row">
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['kt'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asDecimal($model->kt, 2), ['class' => 'form-control', 'disabled' => true]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['dt'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asDecimal($model->dt, 2), ['class' => 'form-control', 'disabled' => true]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['diff'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asDecimal($model->diff, 2), [
                                'class' => 'form-control',
                                'disabled' => true,
                                'title' => 'База налогообложения = Доходы - Расходы',
                            ]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['rate'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asInteger($model->rate), ['class' => 'form-control', 'disabled' => true]) ?>

                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['amount'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asDecimal($model->amount, 2), [
                                'class' => 'form-control',
                                'disabled' => true,
                                'title' => 'Сумма налога = (Доходы - Расходы) × Ставка налога ÷ 100',
                            ]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <?= $form->field($model, 'amount_fact', [
                        'template' => '{label}<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub"></i></span></div>{error}'
                    ])->widget(MaskedInput::className(), [
                        'clientOptions' => [
                            'alias' =>  'numeric',
                            'groupSeparator' => ' ',
                            'autoUnmask' => true,
                            'autoGroup' => true,
                            'removeMaskOnSubmit' => true,
                        ],
                    ])->textInput([
                        'maxlength' => true,
                        'placeholder' => '0',
                    ]) ?>

                </div>
                <div class="col-auto">
                    <?= $form->field($model, 'paid_at')->widget(DateControl::className(), [
                        'value' => $model->paid_at,
                        'type' => DateControl::FORMAT_DATE,
                        'language' => 'ru',
                        'displayFormat' => 'php:d.m.Y',
                        'saveFormat' => 'php:Y-m-d',
                        'widgetOptions' => [
                            'options' => ['placeholder' => 'выберите'],
                            'type' => \kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
                            'layout' => '<div class="input-group">{input}{picker}</div>',
                            'pickerButton' => '<span class="input-group-addon kv-date-calendar" title="Выбрать дату"><i class="fa fa-calendar" aria-hidden="true"></i></span>',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'weekStart' => 1,
                                'autoclose' => true,
                            ],
                            'pluginEvents' => [
                                'changeDate' => 'function(e) {anyDateOnChange();}',
                            ],
                        ],
                    ]) ?>

                </div>
                <?php if ($model->period != null && $model->period->tax_pay_expired_at != null): ?>
                <div class="col-auto">
                    <label class="control-label">Крайний срок оплаты</label>
                    <p class="form-control font-weight-bold text-danger"><?= Yii::$app->formatter->asDate($model->period->tax_pay_expired_at, 'php:d F Y г.') ?></p>
                </div>
                <?php endif; ?>
            </div>

<?= $form->field($model, 'rate')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'dt')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'kt')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'diff')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount')->hiddenInput()->label(false) ?>
