<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Settings */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $sms_balance float */

$this->title = 'Настройки системы | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Настройки системы';
?>
<div class="settings-update">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'default_buyer_id')->widget(Select2::className(), [
                        'initValueText' => $model->default_buyer_id != null ? $model->defaultBuyerName : '',
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
                <div class="col-md-3">
                    <?= $form->field($model, 'default_broker_ru')->widget(Select2::className(), [
                        'initValueText' => $model->default_broker_ru != null ? $model->defaultBrokerRuName : '',
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
                <div class="col-md-3">
                    <?= $form->field($model, 'default_broker_lnr')->widget(Select2::className(), [
                        'initValueText' => $model->default_broker_lnr != null ? $model->defaultBrokerLnrName : '',
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
            </div>
        </div>
        <div class="card-footer text-muted">
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
