<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\datecontrol\DateControl;
use kartik\money\MaskMoney;
use common\models\Deals;
use common\models\TypesDocuments;
use common\models\TypesCounteragents;

/* @var $this yii\web\View */
/* @var $model common\models\Documents */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $action_id string|null */
/* @var $final_bc string */

// если указан контрагент, и контрагент - покупатель, то покажем, сколько израсходовано средств по договору
$isBuyer = false;
if ($model->ca != null)
    if ($model->ca->type_id == TypesCounteragents::COUNTERAGENT_TYPE_ПОКУПАТЕЛЬ)
        $isBuyer = true;
?>

<div class="documents-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <?php if ($model->isNewRecord): ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'type_id')->widget(Select2::className(), [
                        'data' => TypesDocuments::arrayMapForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                    ]) ?>

                </div>
                <?php endif; ?>
                <div class="col-md-4">
                    <?= $form->field($model, 'ca_id')->widget(Select2::className(), [
                        'initValueText' => $model->ca_id != null ? $model->caName : '',
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Введите наименование'],
                        'pluginOptions' => [
                            'minimumInputLength' => 1,
                            'language' => 'ru',
                            'ajax' => [
                                'url' => Url::to(['counteragents/list-for-document']),
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
                <?php if ($model->isNewRecord): ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'includeInDeal_id')->widget(Select2::className(), [
                        'data' => Deals::arrayMapOfAvailableDealsForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'pluginOptions' => ['allowClear' => true],
                        'hideSearch' => true,
                    ]) ?>

                </div>
                <?php elseif ($model->type_id != TypesDocuments::DOCUMENT_TYPE_ДОГОВОР): ?>
                <div class="col-md-4">
                    <label class="control-label" for="documents-deals">Сделки</label>
                    <p id="documents-deals">
                    <?php
                    $links = '';
                    foreach($model->getDealDocumentsArray() as $deal) {
                        /* @var $deal \common\models\Deals */
                        $links .= Html::a($deal['name'], ['/deals/update', 'id' => $deal['id']], [
                            'target' => '_blank',
                            'title' => 'Открыть в новом окне'
                        ]) . ', ';
                    }
                    echo trim(trim($links), ',');
                    ?>
                    </p>
                </div>
                <?php elseif ($model->type_id == TypesDocuments::DOCUMENT_TYPE_ДОГОВОР && $isBuyer): ?>
                <div class="col-md-4">
                    <label class="control-label" for="documents-amount_used">Использовано средств по договору *</label>
                    <p>
                        <strong><?= Yii::$app->formatter->asDecimal($model->amountUsed, 2) ?></strong> из <strong><?= Yii::$app->formatter->asDecimal(floatval($model->amount), 2) ?></strong>.

                    </p>
                </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'doc_num')->textInput(['maxlength' => true, 'placeholder' => 'Введите номер']) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'doc_date')->widget(DateControl::className(), [
                        'value' => $model->doc_date,
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
                        ],
                    ]) ?>

                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'amount', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon"><i class="fa fa-rub" aria-hidden="true"></i></span></div>{error}'])->widget(MaskMoney::className(), [
                        'options' => ['title' => 'Введите сумму вместе с копейками'],
                    ]) ?>

                </div>
            </div>
            <?= $form->field($model, 'comment')->textarea(['rows' => 6, 'placeholder' => 'Введите примечание']) ?>

            <p class="text-muted">* Обратите внимание, что расходные накладные, не привязанные ни к одной сделке, не влияют на сумму использованных средств договора.</p>
        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> ' . $final_bc, ['/documents' . ($action_id == null ? '' : '/' . $action_id)], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

            <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>
            <?php else: ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php endif; ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
