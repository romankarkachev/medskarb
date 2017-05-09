<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $form yii\widgets\ActiveForm */
/* @var $contracts array */

$label_contract = $model->attributeLabels()['contract_id'].' &nbsp; '.Html::a('<i class="fa fa-share" aria-hidden="true"></i>', '#', ['id' => 'btnOpenContract', 'class' => 'text-primary', 'target' => '_blank', 'title' => 'Открыть договор контрагента (в новом окне)']);
$block_visible = ' collapse';
if ($model->contract != null) $block_visible = '';
?>
<div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'contract_id')->widget(Select2::className(), [
                                'data' => $contracts,
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => ['placeholder' => '- выберите -'],
                                'disabled' => $model->is_closed,
                                'hideSearch' => true,
                                'pluginEvents' => [
                                    'change' => new JsExpression('function() { ContractOnChange(); }'),
                                ],
                            ])->label($label_contract) ?>

                        </div>
                        <div class="col-md-8<?= $block_visible ?>" id="block-amount_used">
                            <?= $this->render('_contract_amount_used', ['contract' => $model->contract]) ?>
                        </div>
                    </div>
