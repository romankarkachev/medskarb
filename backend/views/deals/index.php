<?php

use yii\helpers\Html;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DealsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchApplied bool */

$this->title = 'Сделки | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Сделки';

$this->params['breadcrumbsRight'][] = ['label' => 'Создать', 'icon' => 'fa fa-plus-circle fa-lg', 'url' => ['create'], 'class' => 'btn text-success'];
$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/deals'], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];
?>
<div class="deals-list">
    <?= $this->render('_search', ['model' => $searchModel, 'searchApplied' => $searchApplied]); ?>

    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-deals',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\Deals */
                    $result['data-id'] = $model->id;
                    return $result;
                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'options' => ['width' => '60'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'deal_date',
                        'format' => 'date',
                        'options' => ['width' => '90'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    'customerName',
                    'contractRep',
                    'brokerRuName',
                    'brokerLnrName',
                    [
                        'attribute' => 'is_closed',
                        'label' => 'Закрыта',
                        'format' => 'html',
                        'value' => function ($model, $key, $index, $column) {
                            /** @var \common\models\Deals $model */
                            /** @var \yii\grid\DataColumn $column */
                            if ($model->is_closed)
                                return '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>';
                            else
                                return '';
                        },
                        'options' => ['width' => '80'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    ['class' => 'backend\components\grid\ActionColumn'],
                ],
            ]); ?>

        </div>
    </div>
</div>
<?php
$url = \yii\helpers\Url::to(['/deals/update']);
$this->registerJs(<<<JS
$("#table-deals tbody tr").css("cursor", "pointer");
$("#table-deals tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    if (e.target == this && id) location.href = "$url?id=" + id;
});
JS
, \yii\web\View::POS_READY);
?>
