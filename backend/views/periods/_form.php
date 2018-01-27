<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model common\models\Periods */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="periods-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => true, 'placeholder' => 'Введите наименование']) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'temp_start')->widget(DateControl::className(), [
                        'value' => $model->temp_start,
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
                <div class="col-md-2">
                    <?= $form->field($model, 'temp_end')->widget(DateControl::className(), [
                        'value' => $model->temp_end,
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
                                'changeDate' => 'function(e) {periodEndOnChange();}',
                            ],
                        ],
                    ]) ?>

                </div>
                <div class="col-auto">
                    <?= $form->field($model, 'quarter_num')->textInput(['placeholder' => '№', 'title' => 'Введите номер квартала'])->label('№ кв.') ?>

                </div>
                <div class="col-auto">
                    <?= $form->field($model, 'year')->textInput(['placeholder' => '№', 'title' => 'Введите номер года'])->label('№ года') ?>

                </div>
                <div class="col-auto">
                    <?= $form->field($model, 'tax_pay_expired_at')->widget(DateControl::className(), [
                        'value' => $model->tax_pay_expired_at,
                        'type' => DateControl::FORMAT_DATE,
                        'language' => 'ru',
                        'displayFormat' => 'php:d.m.Y',
                        'saveFormat' => 'php:Y-m-d',
                        'widgetOptions' => [
                            'options' => [
                                'placeholder' => 'Оплатить до',
                                'title' => 'Крайний срок оплаты налога по УСН за этот квартал',
                            ],
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
                    ])->label('Срок оплаты') ?>

                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Периоды', ['/periods'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

            <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>
            <?php else: ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php endif; ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs(<<<JS
// Обработчик изменения даты в любом из соответствующих полей.
//
function anyDateOnChange() {
    \$button = $("button[type='submit']");
    \$button.attr("disabled", "disabled");
    text = \$button.html();
    \$button.text("Подождите...");
    setTimeout(function () {
        \$button.removeAttr("disabled");
        \$button.html(text);
    }, 1500);
}

// Обработчик изменения даты в поле "Окончание периода".
//
function periodEndOnChange() {
    periodEnd = $("#periods-temp_end-disp-kvdate").kvDatepicker("getDate");
    periodEnd.setDate(periodEnd.getDate() + 25);
    month = periodEnd.getMonth() + 1;
    if (month < 10) month = "0" + month;
    day = periodEnd.getDate();
    if (day < 10) day = "0" + day;
    dateDots = day + "." + month + "." + periodEnd.getFullYear();
    dateDashes = periodEnd.getFullYear() + "-" + month + "-" + day;
    $("#periods-tax_pay_expired_at").val(dateDashes);
    $("#periods-tax_pay_expired_at-disp-kvdate").kvDatepicker("update", dateDots);

    anyDateOnChange();
} // periodEndOnChange()
JS
, \yii\web\View::POS_BEGIN);
?>
