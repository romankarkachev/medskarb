<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\TypesCounteragents;

/* @var $this yii\web\View */
/* @var $model common\models\Counteragents */
/* @var $form yii\bootstrap\ActiveForm */

$label_bank_bik = $model->attributeLabels()['bank_bik'];
$label_inn = $model->attributeLabels()['inn'];
$label_ogrn = $model->attributeLabels()['ogrn'];
$label_contract = $model->attributeLabels()['contract_id'].' &nbsp; '.Html::a('<i class="fa fa-share" aria-hidden="true"></i>', '#', ['id' => 'btnOpenContract', 'class' => 'text-primary', 'target' => '_blank', 'title' => 'Открыть договор контрагента (в новом окне)']);
?>

<div class="counteragents-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'type_id')->widget(Select2::className(), [
                        'data' => TypesCounteragents::arrayMapForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                    ]) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'inn')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '999999999999',
                        'clientOptions' => ['placeholder' => ''],
                    ])->textInput(['maxlength' => true, 'placeholder' => 'Введите ИНН'])
                        ->label($label_inn, ['id' => 'label-inn']) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'kpp')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '999999999',
                        'clientOptions' => ['placeholder' => ''],
                    ])->textInput(['maxlength' => true, 'placeholder' => 'Введите КПП']) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'ogrn')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '999999999999999',
                        'clientOptions' => ['placeholder' => ''],
                    ])->textInput(['maxlength' => true, 'placeholder' => 'Введите ОГРН или ОГРНИП'])
                        ->label($label_ogrn, ['id' => 'label-ogrn']) ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => !$model->isNewRecord, 'placeholder' => 'Например, Фирма']) ?>

                </div>
                <div class="col-md-7">
                    <?= $form->field($model, 'name_full')->textInput(['maxlength' => true, 'placeholder' => 'Например, ООО "Фирма"']) ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'address_j')->textInput(['placeholder' => 'Введите юридический адрес']) ?>

                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'address_p')->textInput(['placeholder' => 'Введите фактический адрес']) ?>

                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'address_m')->textInput(['placeholder' => 'Введите почтовый адрес']) ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'bank_bik')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '999999999',
                        'clientOptions' => ['placeholder' => ''],
                    ])->textInput(['placeholder' => 'Введите БИК банка'])
                        ->label($label_bank_bik, ['id' => 'label-bank_bik']) ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'bank_an')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '99999999999999999999',
                        'clientOptions' => ['placeholder' => ''],
                    ])->textInput(['placeholder' => 'Введите номер расчетного счета']) ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'bank_ca')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '99999999999999999999',
                        'clientOptions' => ['placeholder' => ''],
                    ])->textInput(['placeholder' => 'Введите номер корр. счета']) ?>

                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true, 'placeholder' => 'Введите наименование банка']) ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'phones')->textInput(['maxlength' => true, 'placeholder' => 'Введите телефоны']) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'contact_person')->textInput(['maxlength' => true, 'placeholder' => 'Введите имя']) ?>

                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Введите E-mail']) ?>

                </div>
                <?php if (!$model->isNewRecord): ?>
                <div class="col-md-4">
                    <?= $form->field($model, 'contract_id')->widget(Select2::className(), [
                        'data' => $model->arrayMapOfContractsOfThisCounteragentForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                        'pluginOptions' => ['allowClear' => true],
                    ])->label($label_contract) ?>

                </div>
                <?php else: ?>
                <div class="col-md-2">
                    <label for="<?= strtolower($model->formName()) ?>-iscreatenewcontract" class="control-label"><?= $model->attributeLabels()['isCreateNewContract'] ?></label>
                    <?= $form->field($model, 'isCreateNewContract')->checkbox()->label(false) ?>

                </div>
                <?php endif; ?>
            </div>
            <?= $form->field($model, 'comment')->textarea(['rows' => 6, 'placeholder' => 'Введите примечание']) ?>

        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Контрагенты', ['/counteragents'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

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
$url_bank_bik = Url::to(['/counteragents/fetch-bank-by-bik']);
$url_inn_ogrn = Url::to(['/counteragents/fetch-counteragents-info-dadata']);
$url_open_contract = Url::to(['/documents/update']);

$field_inn = \common\models\Counteragents::API_FIELD_ИНН;
$field_ogrn = \common\models\Counteragents::API_FIELD_ОГРН;
$this->registerJs(<<<JS
$('input').iCheck({
    checkboxClass: 'icheckbox_square-green',
});

// Заполняет реквизиты данными, полученными через механизм API.
//
function fillFields(caInfo) {
    \$field = $("#counteragents-inn");
    if (\$field.val() == "" && caInfo.inn) \$field.val(caInfo.inn);

    \$field = $("#counteragents-kpp");
    if (\$field.val() == "" && caInfo.kpp) \$field.val(caInfo.kpp);

    \$field = $("#counteragents-ogrn");
    if (\$field.val() == "" && caInfo.ogrn) \$field.val(caInfo.ogrn);

    \$field = $("#counteragents-name");
    if (\$field.val() == "" && caInfo.name) \$field.val(caInfo.name);

    \$field = $("#counteragents-name_full");
    if (\$field.val() == "" && caInfo.name_full) \$field.val(caInfo.name_full);

    \$field = $("#counteragents-email");
    if (\$field.val() == "" && caInfo.email) \$field.val(caInfo.email);

    if (caInfo.address) {
        $("#counteragents-address_j").val(caInfo.address);
        $("#counteragents-address_p").val(caInfo.address);
        $("#counteragents-address_m").val(caInfo.address);
    }    
} // fillFields()

// Обработчик изменения значения в поле "Наименование".
//
function nameOnChange() {
    \$name_full = $("#counteragents-name_full");
    if (\$name_full.val() == "")
        \$name_full.val("ООО \"" + $(this).val() + "\"");
} // nameOnChange()

// Обработчик изменения значения в поле "ИНН".
//
function innOnChange() {
    ogrn = $("#counteragents-ogrn").val();
    kpp = $("#counteragents-kpp").val();
    if (ogrn == "" || kpp == "") {
        inn = $("#counteragents-inn").val();
        if (inn != "") {
            \$label = $("#label-inn");
            \$label.html("$label_inn &nbsp;<i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i>");
            $.get("$url_inn_ogrn?query=" + inn, function(response) {
                if (response != false) {
                    fillFields(response);
                    //if (kpp == "" && response.kpp) $("#counteragents-kpp").val(response.kpp);
                    //if (ogrn == "") $("#counteragents-ogrn").val(response.ogrn);
                }
    
            }).always(function() {
                \$label.html("$label_inn");
            });
        }
    }
} // innOnChange()

// Обработчик изменения значения в поле "ОГРН".
//
function ogrnOnChange() {
    inn = $("#counteragents-inn").val();
    kpp = $("#counteragents-kpp").val();
    if (inn == "" || kpp == "") {
        ogrn = $("#counteragents-ogrn").val();
        if (ogrn != "") {
            \$label = $("#label-ogrn");
            \$label.html("$label_ogrn &nbsp;<i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i>");
            $.get("$url_inn_ogrn?query=" + ogrn, function(response) {
                if (response != false) {
                    fillFields(response);
                    //if (kpp == "" && response.kpp) $("#counteragents-kpp").val(response.kpp);
                    //if (inn == "") $("#counteragents-inn").val(response.inn);
                }
    
            }).always(function() {
                \$label.html("$label_ogrn");
            });
        }
    }
} // ogrnOnChange()

// Обработчик изменения значения в поле "БИК банка".
//
function bankBikOnChange() {
    bik = $(this).val();
    if (bik.length == 9) {
        $("#counteragents-bank_ca").val("");
        $("#counteragents-bank_name").val("");
        $("#label-bank_bik").html("$label_bank_bik &nbsp;<i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i>");
        $.get("$url_bank_bik?bik=" + bik, function(response) {
            if (response != false) {
                $("#counteragents-bank_ca").val(response.bank_ca);
                $("#counteragents-bank_name").val(response.bank_name);
            };

        }).always(function() {
            $("#label-bank_bik").html("$label_bank_bik");
        });
    }
} // bankBikOnChange()

// Обработчик щелчка по ссылке "Открыть договор в новом окне".
//
function btnOpenContractOnClick() {
    contract_id = $("#counteragents-contract_id").val();
    if (contract_id != "" && contract_id != undefined && !isNaN(contract_id))
        window.open("$url_open_contract?id=" + contract_id, "_blank");
} // btnOpenContractOnClick()

$(document).on("click", "#btnOpenContract", btnOpenContractOnClick);
$(document).on("change", "#counteragents-name", nameOnChange);
$(document).on("change", "#counteragents-inn", innOnChange);
$(document).on("change", "#counteragents-ogrn", ogrnOnChange);
$(document).on("change", "#counteragents-bank_bik", bankBikOnChange);
JS
, yii\web\View::POS_READY);
?>
