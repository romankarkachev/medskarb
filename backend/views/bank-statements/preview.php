<?php

use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="bank-statements-preview">
    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-statements',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\BankStatements */
                    $result['data-id'] = $model->id;
                    $result['data-docrep'] = 'ПП № ' . $model->bank_doc_num . ' от ' . Yii::$app->formatter->asDate($model->bank_date, 'php:d F Y');
                    if ($model->is_active == 0) $result['class'] = 'text-muted';
                    return $result;
                },
                'columns' => [
                    [
                        'attribute' => 'bank_date',
                        'format' => 'date',
                        'options' => ['width' => '90'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'bank_doc_num',
                        'label' => '№ ПП',
                        'options' => ['width' => '80'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'caName',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $column) {
                            /** @var \common\models\BankStatements $model */
                            /** @var \yii\grid\DataColumn $column */
                            $manual = '';
                            if ($model->type == \common\models\BankStatements::TYPE_MANUAL)
                                $manual = '<i class="fa fa-pencil text-info" aria-hidden="true" title="Добавлено вручную"></i>';

                            $ca_name = '<em>' . $model->bank_description . '</em> ';
                            if ($model->ca_id != null)
                                $ca_name = '<strong>' . $model->caName . '</strong> ';

                            return $ca_name.$manual;
                        },
                    ],
                    [
                        'attribute' => 'periodName',
                        'options' => ['width' => '130'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'label' => 'Сумма',
                        'contentOptions' => function ($model, $key, $index, $column) {
                            /** @var \common\models\BankStatements $model */
                            /** @var \yii\grid\DataColumn $column */
                            if ($model->bank_amount_dt == null || $model->bank_amount_dt == 0)
                                return ['class' => 'text-success text-right'];
                            else
                                return ['class' => 'text-danger text-right'];
                        },
                        'value' => function ($model, $key, $index, $column) {
                            /** @var \common\models\BankStatements $model */
                            /** @var \yii\grid\DataColumn $column */
                            if ($model->bank_amount_dt == null || $model->bank_amount_dt == 0)
                                return '+' . Yii::$app->formatter->asDecimal($model->bank_amount_kt, 2);
                            else
                                return '-' . Yii::$app->formatter->asDecimal($model->bank_amount_dt, 2);
                        },
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>
