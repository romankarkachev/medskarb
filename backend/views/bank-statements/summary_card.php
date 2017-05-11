<?php

use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\BankStatements */

$caName = '<p class="form-control-static">' . $model->caNameFull . '</p>';
if ($model->ca_id == null)
    $caName = Select2::widget([
        'id' => 'bankstatements-ca_id',
        'name' => 'BankStatements[ca_id]',
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
        'pluginEvents' => [
            'change' => new JsExpression('function() { CounteragentOnChange(); }'),
        ],
    ]);

if ($model->bank_amount_dt == null || $model->bank_amount_dt == 0)
    $amount = '<h5 class="form-control-static text-success font-weight-bold">+' . Yii::$app->formatter->asCurrency($model->bank_amount_kt) . '</h5>';
else
    $amount = '<h5 class="form-control-static text-danger font-weight-bold">-' . Yii::$app->formatter->asCurrency($model->bank_amount_dt) . '</h5>';

$mode= '';
if ($model->type == \common\models\BankStatements::TYPE_MANUAL)
    $mode = '<i class="fa fa-pencil fa-2x text-info" aria-hidden="true" title="Добавлено вручную"></i>';

$label_ca_id = $model->attributeLabels()['ca_id'];
?>
<div class="bank-statements-summary_card">
    <?= \yii\helpers\Html::input('hidden', 'BankStatements[id]', $model->id, ['id' => 'bankstatements-id']) ?>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label font-weight-bold" id="label-ca_id"><?= $label_ca_id ?></label>
        <div class="col-sm-6">
            <?= $caName ?>

        </div>
        <div class="col-sm-1">
            <?= $mode ?>

        </div>
        <div class="col-sm-2 puu">
            <label class="switch switch-left-right">
                <input id="cbActive" class="switch-input" type="checkbox"<?= $model->is_active ? 'checked' : '' ?> />
                <span class="switch-label" data-on="Активно" data-off="Выкл."></span>
                <span class="switch-handle"></span>
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <label class="col-form-label font-weight-bold font-small"><?= $model->attributeLabels()['bank_dt'] ?></label>
            <p class="form-control-static"><?= nl2br($model->bank_dt) ?></p>
        </div>
        <div class="col-sm-4">
            <label class="col-form-label font-weight-bold"><?= $model->attributeLabels()['bank_kt'] ?></label>
            <p class="form-control-static"><?= nl2br($model->bank_kt) ?></p>
        </div>
        <div class="col-sm-3">
            <label class="col-form-label font-weight-bold">Сумма</label>
            <?= $amount ?>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-9">
            <label class="col-form-label font-weight-bold"><?= $model->attributeLabels()['bank_bik_name'] ?></label>
            <p class="form-control-static"><?= nl2br($model->bank_bik_name) ?></p>
        </div>
        <div class="col-sm-3">
            <label class="col-form-label font-weight-bold">Период</label>
            <p class="form-control-static"><?= $model->periodName ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label font-weight-bold"><?= $model->attributeLabels()['bank_description'] ?></label>
        <p class="form-control-static"><?= nl2br($model->bank_description) ?></p>

        <p>
            <small class="text-muted">
                Создано <?= Yii::$app->formatter->asDate($model->created_at, 'php:d F Y в H:i') ?>.

                Автор: <?= $model->createdByName ?>

            </small>
        </p>
    </div>
</div>
<?php
$url_set_ca = Url::to(['/bank-statements/set-counteragent']);
$url_toggle_active = Url::to(['/bank-statements/toggle-active']);
$this->registerJs(<<<JS
// Функция-обработчик изменения значения в поле Контрагент.
//
function CounteragentOnChange() {
    $("#label-ca_id").html("$label_ca_id &nbsp;<i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i>");
    bs_id = $("#bankstatements-id").val();
    ca_id = $("#bankstatements-ca_id").val();
    $.post("$url_set_ca?bs_id=" + bs_id + "&ca_id=" + ca_id, function(result) {
        var icon = "";
        if (result == true)
            icon = "<i class=\"fa fa-check-circle-o text-success\"></i>";
        else
            icon = "<i class=\"fa fa-minus-circle text-danger\" title=\"Контрагент не был изменен вследстие ошибки в переданных параметрах\"></i>";

        $("#label-ca_id").html("$label_ca_id &nbsp;" + icon);
    })
    .fail(function() {
        $("#label-ca_id").html("$label_ca_id &nbsp;<i class=\"fa fa-ban text-danger\" title=\"Не удалось выполнить Ваш запрос!\"></i>");
    });
} // CounteragentOnChange()

// Функция-обработчик изменения состояния галочки активности.
//
function ActiveOnChange() {
    bs_id = $("#bankstatements-id").val();
    $.post("$url_toggle_active?bs_id=" + bs_id);
} // ActiveOnChange()

$(document).on("change", "#cbActive", ActiveOnChange);
JS
, yii\web\View::POS_READY);
?>
