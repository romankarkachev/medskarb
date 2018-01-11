<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;
use kartik\select2\Select2;
use kartik\datecontrol\DateControl;
use common\models\Periods;

/* @var $this yii\web\View */
/* @var $model common\models\BankStatements */
/* @var $form yii\bootstrap\ActiveForm */

$label_ca_id = $model->attributeLabels()['ca_id'] . ' ' . Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> новый', ['/counteragents/create'], ['class' => 'badge badge-success', 'target' => '_blank', 'title' => 'Открыть страницу добавления нового контрагента в новом окне']);
?>

<div class="bank-statements-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'period_id')->widget(Select2::className(), [
                        'data' => Periods::arrayMapForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>

                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'ca_id')->widget(Select2::className(), [
                        'initValueText' => $model->ca_id != null ? $model->caName : '',
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Введите наименование'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 1,
                            'language' => 'ru',
                            'ajax' => [
                                'url' => Url::to(['counteragents/list-for-document']),
                                'delay' => 250,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                            'templateSelection' => new JsExpression('function (result) { return result.text; }'),
                        ],
                    ])->label($label_ca_id) ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'bank_amount_dt', [
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
                    ])->label('Расход') ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'bank_date')->widget(DateControl::className(), [
                        'value' => $model->bank_date,
                        'type' => DateControl::FORMAT_DATE,
                        'language' => 'ru',
                        'displayFormat' => 'php:d.m.Y',
                        'saveFormat' => 'php:Y-m-d',
                        'widgetOptions' => [
                            'options' => ['placeholder' => 'Дата оплаты'],
                            'type' => \kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
                            'layout' => '<div class="input-group">{input}{picker}</div>',
                            'pickerButton' => '<span class="input-group-addon kv-date-calendar" title="Выбрать дату"><i class="fa fa-calendar" aria-hidden="true"></i></span>',
                            // можно и добавить, но верстка ломается:
                            //'removeButton' => '<span class="input-group-addon kv-date-remove" title="Очистить поле"><i class="fa fa-remove" aria-hidden="true"></i></span>',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'weekStart' => 1,
                                'autoclose' => true,
                            ],
                        ],
                    ]) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'bank_doc_num')->textInput(['maxlength' => true, 'placeholder' => '№ чека'])->label('№ документа') ?>

                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'imageFile')->fileInput(['style' => 'display: block;']) ?>

                </div>
            </div>
            <?= $form->field($model, 'bank_description')->textarea(['rows' => 6, 'placeholder' => 'Назначение платежа']) ?>

        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Банковские движения', ['/bank-statements'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

            <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>
            <?php else: ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php endif; ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
