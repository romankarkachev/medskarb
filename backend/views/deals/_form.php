<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $form yii\widgets\ActiveForm */
/* @var $contracts array */
/* @var $docs_receipt \yii\data\ActiveDataProvider приходные накладные */
/* @var $docs_expense \yii\data\ActiveDataProvider расходные накладные */
/* @var $docs_broker_ru \yii\data\ActiveDataProvider акты выполненных работ брокера по России */
/* @var $docs_broker_lnr \yii\data\ActiveDataProvider акты выполненных работ брокера ЛНР */

$label_customer_id = $model->attributeLabels()['customer_id'];
$label_contract = $model->attributeLabels()['contract_id'].' &nbsp; '.Html::a('<i class="fa fa-share" aria-hidden="true"></i>', '#', ['id' => 'btnOpenContract', 'class' => 'text-primary', 'target' => '_blank', 'title' => 'Открыть договор контрагента (в новом окне)']);

$cb_is_closed_options = [];
if ($model->is_closed) $cb_is_closed_options = ['disabled' => $model->is_closed,];
?>

<div class="deals-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'deal_date')->widget(DateControl::className(), [
                        'value' => $model->deal_date,
                        'type' => DateControl::FORMAT_DATE,
                        'language' => 'ru',
                        'displayFormat' => 'php:d.m.Y',
                        'saveFormat' => 'php:Y-m-d',
                        'disabled' => $model->is_closed,
                        'widgetOptions' => [
                            'options' => ['placeholder' => 'выберите'],
                            'type' => \kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
                            'layout' => '<div class="input-group">{input}' . ($model->is_closed ? '' : '{picker}') . '</div>',
                            'pickerButton' => '<span class="input-group-addon kv-date-calendar" title="Выбрать дату"><i class="fa fa-calendar" aria-hidden="true"></i></span>',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'weekStart' => 1,
                                'autoclose' => true,
                            ],
                        ],
                    ]) ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'broker_ru_id')->widget(Select2::className(), [
                        'initValueText' => $model->broker_ru_id != null ? $model->brokerRuName : '',
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Введите наименование'],
                        'disabled' => $model->is_closed,
                        'pluginOptions' => [
                            'minimumInputLength' => 1,
                            'language' => 'ru',
                            'ajax' => [
                                'url' => Url::to(['counteragents/list-of-brokers-ru']),
                                'delay' => 250,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'pluginEvents' => [
                            'change' => new JsExpression('function() {}'),
                        ]
                    ]) ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'broker_lnr_id')->widget(Select2::className(), [
                        'initValueText' => $model->broker_lnr_id != null ? $model->brokerLnrName : '',
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Введите наименование'],
                        'disabled' => $model->is_closed,
                        'pluginOptions' => [
                            'minimumInputLength' => 1,
                            'language' => 'ru',
                            'ajax' => [
                                'url' => Url::to(['counteragents/list-of-brokers-lnr']),
                                'delay' => 250,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'pluginEvents' => [
                            'change' => new JsExpression('function() {}'),
                        ]
                    ]) ?>

                </div>
                <div class="col-md-2">
                    <label for="<?= strtolower($model->formName()) ?>-is_closed" class="control-label"><?= $model->attributeLabels()['is_closed'] ?></label>
                    <?= $form->field($model, 'is_closed')->checkbox($cb_is_closed_options)->label(false) ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'customer_id')->widget(Select2::className(), [
                        'initValueText' => $model->customer_id != null ? $model->customerName : '',
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'language' => 'ru',
                        'options' => [
                            'data-id' => $model->id,
                            'placeholder' => 'Введите наименование'
                        ],
                        'disabled' => $model->is_closed,
                        'pluginOptions' => [
                            'minimumInputLength' => 1,
                            'language' => 'ru',
                            'ajax' => [
                                'url' => Url::to(['counteragents/list-of-customers']),
                                'delay' => 250,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(result) { return result.text; }'),
                            'templateSelection' => new JsExpression('function (result) { return result.text; }'),
                        ],
                        'pluginEvents' => [
                            'change' => new JsExpression('function() { CounteragentOnChange(); }'),
                        ],
                    ])->label($label_customer_id, ['id' => 'label-customer_id']) ?>

                </div>
                <div class="col-md-8<?= $model->customer_id == null ? ' collapse' : '' ?>" id="block-contract">
                    <?= $this->render('_contract', [
                        'model' => $model,
                        'form' => $form,
                        'contracts' => $contracts,
                    ]) ?>
                </div>
            </div>
            <p class="text-muted">* Расходная накладная, не привязанная ни к одной сделке, не использует средства договора.</p>
        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Сделки', ['/deals'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

            <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>
            <?php elseif (!$model->is_closed): ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php endif; ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$url_update = Url::to(['/deals/update', 'id' => $model->id]);
$url_reload_unattached = Url::to(['/deals/render-unattached-documents']);
$url_open_contract = Url::to(['/documents/update']);
$url_ca_contracts = Url::to(['/deals/compose-contract-field']);
$url_contract_amount_used = Url::to(['deals/compose-amount-used-fields']);
$url_add_documents = Url::to(['deals/add-documents-through-select']);
$this->registerJs(<<<JS
$.pjax.defaults.scrollTo = false;

$(".select-on-check-all").on('ifChanged', function(event){
    if ($(this).prop("checked"))
        $("input[name='selection[]']").iCheck("check");
    else
        $("input[name='selection[]']").iCheck("uncheck");
});

// Функция-обработчик изменения значения в поле Контрагент.
//
function CounteragentOnChange() {
    $("#label-customer_id").html("$label_customer_id &nbsp;<i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i>");
    $("#block-contract").html("");
    customer_id = $("#deals-customer_id").val();
    if (customer_id != "" && customer_id != undefined) {
        $("#block-contract").show();
        $("#block-contract").load("$url_ca_contracts?customer_id=" + customer_id, function() {
            $("#label-customer_id").html("$label_customer_id");
        });
    }
}; // CounteragentOnChange()

// Функция-обработчик изменения значения в поле Договор контрагента.
//
function ContractOnChange() {
    $("#block-amount_used").html("<i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i>");
    contract_id = $("#deals-contract_id").val();
    if (contract_id != "" && contract_id != undefined) {
        $("#block-amount_used").show();
        $("#block-amount_used").load("$url_contract_amount_used?contract_id=" + contract_id);
    }
} // ContractOnChange()

// Обработчик щелчка по ссылке "Открыть договор в новом окне".
//
function btnOpenContractOnClick() {
    contract_id = $("#deals-contract_id").val();
    if (contract_id != "" && contract_id != undefined && !isNaN(contract_id))
        window.open("$url_open_contract?id=" + contract_id, "_blank");
} // btnOpenContractOnClick()

// Обработчик щелчка по кнопке "Добавить документы в сделку".
//
function btnAddDocsToDealOnClick() {
    var ids = $("#gw-docs_unattached").yiiGridView("getSelectedRows");
    if (ids != "") {
        var \$btn = $(this).button("loading");
        $.post("$url_add_documents", {deal_id: $(this).attr("data-id"), ids: ids})
        .always(function() {
            \$btn.button("reset");
            $.pjax.reload({container: "#pjax-docs_unattached"});
        });
    }
} // btnAddDocsToDealOnClick()

// Обработчик щелчка по кнопке "Обновить список не привязанных документов".
//
function btnReloadUnattachedOnClick() {
    $.get("$url_reload_unattached", function() {
        $.pjax.reload({container: "#pjax-docs_unattached"});
    });
} // btnReloadUnattachedOnClick()

$(document).on("click", "#btnOpenContract", btnOpenContractOnClick);
$(document).on("click", "#btnAddDocsToDeal", btnAddDocsToDealOnClick);
$(document).on("click", "#btnReloadUnattached", btnReloadUnattachedOnClick);
JS
, yii\web\View::POS_READY);
?>
