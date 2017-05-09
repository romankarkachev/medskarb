<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\components\grid\TotalsColumn;

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $dpDocumentsRecepit \yii\data\ActiveDataProvider */
?>
<?= GridView::widget([
    'dataProvider' => $dpDocumentsRecepit,
    'id' => 'gw-docs_receipt',
    'layout' => '{items}',
    'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
    'showFooter' => true,
    'footerRowOptions' => ['class' => 'text-right'],
    'columns' => [
        'documentCaName',
        [
            'attribute' => 'doc_date',
            'label' => '№ и дата',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                /** @var \common\models\DealsDocuments $model */
                /** @var \yii\grid\DataColumn $column */
                return Html::a($model->document->documentRep, ['/documents/update', 'id' => $model->document->id], ['target' => '_blank', 'data-pjax' => 0, 'title' => 'Открыть в новом окне']);
            },
            'footer' => '<strong>Итого:</strong>',
            'footerOptions' => ['class' => 'text-right'],
        ],
        [
            'class' => TotalsColumn::className(),
            'attribute' => 'documentAmount',
            'format' => ['decimal', 'decimals' => 2],
            'headerOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
            'contentOptions' => ['class' => 'text-right'],
            'options' => ['width' => '90'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'visible' => $model->is_closed == false,
            'header' => '<i class="fa fa-bars" aria-hidden="true"></i>',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model) {
                    return Html::a('<i class="fa fa-trash-o"></i>', [
                        '/deals/delete-document',
                        'id' => $model->id,
                        'type_id' => \common\models\TypesDocuments::DOCUMENT_TYPE_ПРИХОДНАЯ_НАКЛАДНАЯ
                    ], [
                        'title' => Yii::t('yii', 'Удалить'),
                        'class' => 'btn btn-sm btn-danger',
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => true
                    ]);
                }
            ],
            'options' => ['width' => '40'],
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
        ],
    ],
]); ?>
