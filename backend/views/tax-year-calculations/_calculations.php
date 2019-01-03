<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model common\models\TaxYearCalculations */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?= $this->render('_source_table', ['model' => $model]) ?>
<?= $this->render('_declaration_measurements', ['model' => $model]) ?>
            <div class="form-group">
                <h3>Упрощенная система налогообложения</h3>
            </div>
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
                        <label class="control-label"><?= $model->attributeLabels()['base'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asDecimal($model->base, 2), [
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
                        <label class="control-label"><?= $model->attributeLabels()['min'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asInteger($model->min), ['class' => 'form-control', 'disabled' => true]) ?>

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
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['amount_fact'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asInteger($model->amount_fact), ['class' => 'form-control', 'disabled' => true]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['amount_to_pay'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asInteger($model->amount_to_pay), ['class' => 'form-control', 'disabled' => true]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <?php if ($model->tax_pay_expired_at != null): ?>
                <div class="col-auto">
                    <label class="control-label">Крайний срок оплаты</label>
                    <p class="form-control font-weight-bold text-danger"><?= Yii::$app->formatter->asDate($model->tax_pay_expired_at, 'php:d F Y г.') ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-auto">
                    <?= $form->field($model, 'declared_at')->widget(DateControl::className(), [
                        'value' => $model->declared_at,
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
                <div class="col-auto">
                    <?= $form->field($model, 'paid_fact', [
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
            </div>
            <div class="form-group">
                <h3>Взнос в Пенсионный Фонд</h3>
            </div>
            <div class="row">
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['pf_base'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asDecimal($model->pf_base, 2), [
                                'class' => 'form-control',
                                'disabled' => true,
                                'title' => 'База налогообложения = Доходы',
                            ]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['pf_limit'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asDecimal($model->pf_limit, 2), [
                                'class' => 'form-control',
                                'disabled' => true,
                                'title' => 'Лимит задается в настройках системы',
                            ]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['pf_rate'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asInteger($model->pf_rate), ['class' => 'form-control', 'disabled' => true]) ?>

                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label class="control-label"><?= $model->attributeLabels()['pf_amount'] ?></label>
                        <div class="input-group">
                            <?= Html::input('text', null, Yii::$app->formatter->asInteger($model->pf_amount), ['class' => 'form-control', 'disabled' => true]) ?>

                            <span class="input-group-addon"><i class="fa fa-rub"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <?= $form->field($model, 'pf_paid_at')->widget(DateControl::className(), [
                        'value' => $model->pf_paid_at,
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
            </div>


<?= $form->field($model, 'kt')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'dt')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'base')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'rate')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'min')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount_fact')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount_to_pay')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'pf_base')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'pf_limit')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'pf_rate')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'pf_amount')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'calculation_details')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdm')->hiddenInput()->label(false) ?>

<?= $form->field($model, 'tdr020')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr040')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr070')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr100')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr210')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr211')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr212')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr213')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr220')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr221')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr222')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr223')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr240')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr241')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr242')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr243')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr270')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr271')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr272')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'tdr273')->hiddenInput()->label(false) ?>
