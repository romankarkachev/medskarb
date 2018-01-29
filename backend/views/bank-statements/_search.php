<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;
use kartik\select2\Select2;
use kartik\datecontrol\DateControl;
use common\models\Periods;
use common\models\BankStatementsSearch;

/* @var $this yii\web\View */
/* @var $model common\models\BankStatementsSearch */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $searchApplied bool */

$directions = BankStatementsSearch::fetchFilterGroupDirections();
$paymentMethods = BankStatementsSearch::fetchFilterGroupPaymentMethods();
$activity = BankStatementsSearch::fetchFilterGroupActive();
?>

<div class="bank-statements-search">
    <?php $form = ActiveForm::begin([
        'action' => ['/bank-statements'],
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
                        'data' => Periods::arrayMapForSelect2(true),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'bank_doc_num')->textInput(['placeholder' => '№ платежки (чека)'])->label('№ ПП') ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'searchDateStart')->widget(DateControl::className(), [
                        'value' => $model->searchDateStart,
                        'type' => DateControl::FORMAT_DATE,
                        'language' => 'ru',
                        'displayFormat' => 'php:d.m.Y',
                        'saveFormat' => 'php:Y-m-d',
                        'widgetOptions' => [
                            'options' => ['placeholder' => 'Начало периода'],
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
                    <?= $form->field($model, 'searchDateEnd')->widget(DateControl::className(), [
                        'value' => $model->searchDateEnd,
                        'type' => DateControl::FORMAT_DATE,
                        'language' => 'ru',
                        'displayFormat' => 'php:d.m.Y',
                        'saveFormat' => 'php:Y-m-d',
                        'widgetOptions' => [
                            'options' => ['placeholder' => 'Конец периода'],
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
                    ]) ?>

                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <?= $form->field($model, 'searchGroupDirection', [
                        'inline' => true,
                    ])->radioList(ArrayHelper::map($directions, 'id', 'name'), [
                        'class' => 'btn-group',
                        'data-toggle' => 'buttons',
                        'unselect' => null,
                        'item' => function ($index, $label, $name, $checked, $value) use ($directions) {
                            $hint = '';
                            $key = array_search($value, array_column($directions, 'id'));
                            if ($key !== false && isset($directions[$key]['hint'])) $hint = ' title="' . $directions[$key]['hint'] . '"';

                            return '<label class="btn btn-success' . ($checked ? ' active' : '') . '"' . $hint . '>' .
                                Html::radio($name, $checked, ['value' => $value, 'class' => 'types-btn']) . $label . '</label>';
                        },
                    ]) ?>

                </div>
                <div class="col-auto">
                    <?= $form->field($model, 'searchGroupPaymentMethod', [
                        'inline' => true,
                    ])->radioList(ArrayHelper::map($paymentMethods, 'id', 'name'), [
                        'class' => 'btn-group',
                        'data-toggle' => 'buttons',
                        'unselect' => null,
                        'item' => function ($index, $label, $name, $checked, $value) use ($paymentMethods) {
                            $hint = '';
                            $key = array_search($value, array_column($paymentMethods, 'id'));
                            if ($key !== false && isset($paymentMethods[$key]['hint'])) $hint = ' title="' . $paymentMethods[$key]['hint'] . '"';

                            return '<label class="btn btn-success' . ($checked ? ' active' : '') . '"' . $hint . '>' .
                                Html::radio($name, $checked, ['value' => $value, 'class' => 'types-btn']) . $label . '</label>';
                        },
                    ]) ?>

                </div>
                <div class="col-auto">
                    <?= $form->field($model, 'searchGroupActive', [
                        'inline' => true,
                    ])->radioList(ArrayHelper::map($activity, 'id', 'name'), [
                        'class' => 'btn-group',
                        'data-toggle' => 'buttons',
                        'unselect' => null,
                        'item' => function ($index, $label, $name, $checked, $value) use ($activity) {
                            $hint = '';
                            $key = array_search($value, array_column($activity, 'id'));
                            if ($key !== false && isset($activity[$key]['hint'])) $hint = ' title="' . $activity[$key]['hint'] . '"';

                            return '<label class="btn btn-success' . ($checked ? ' active' : '') . '"' . $hint . '>' .
                                Html::radio($name, $checked, ['value' => $value, 'class' => 'types-btn']) . $label . '</label>';
                        },
                    ]) ?>

                </div>
            </div>
            <?= $form->field($model, 'bank_description')->textInput(['placeholder' => 'Поиск по назначению платежа']) ?>

            <div class="form-group">
                <?= Html::submitButton('Выполнить', ['class' => 'btn btn-info']) ?>

                <?= Html::a('Отключить отбор', ['/bank-statements'], ['class' => 'btn btn-secondary']) ?>

            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
