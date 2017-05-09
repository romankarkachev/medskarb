<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dpDocumentsUnattached \yii\data\ActiveDataProvider */
?>
<?= GridView::widget([
    'dataProvider' => $dpDocumentsUnattached,
    'id' => 'gw-docs_unattached',
    'layout' => '{items}',
    'tableOptions' => ['class' => 'table table-sm'],
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'headerOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
            'contentOptions' => ['class' => 'text-center'],
            'options' => ['width' => '60'],
        ],
        [
            'attribute' => 'typeName',
            'headerOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
            'contentOptions' => ['class' => 'text-center'],
            'options' => ['width' => '150'],
        ],
        [
            'attribute' => 'doc_date',
            'label' => '№ и дата',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                /** @var \common\models\Documents $model */
                /** @var \yii\grid\DataColumn $column */
                return Html::a($model->getDocumentRep(), ['/documents/update', 'id' => $model->id], ['target' => '_blank', 'data-pjax' => 0, 'title' => 'Открыть в новом окне']);
            },
            'headerOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
            'contentOptions' => ['class' => 'text-center'],
            'options' => ['width' => '200'],
        ],
        'caName',
        [
            'attribute' => 'amount',
            'format' => ['decimal', 'decimals' => 2],
            'headerOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
            'contentOptions' => ['class' => 'text-right'],
            'options' => ['width' => '120'],
        ],
    ],
]); ?>

<?php
$this->registerJs(<<<JS
$('input').iCheck({
    checkboxClass: 'icheckbox_square-green',
});
JS
, yii\web\View::POS_READY);
?>

