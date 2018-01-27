<?php

use backend\components\grid\TotalsColumn;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaxQuarterCalculationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchApplied bool */

$this->title = 'Расчеты авансовых платежей по УСН | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Расчеты авансовых платежей по УСН';

$this->params['breadcrumbsRight'][] = ['label' => 'Создать', 'icon' => 'fa fa-plus-circle fa-lg', 'url' => ['create'], 'class' => 'btn text-success'];
$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/tax-quarter-calculations'], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];
?>
<div class="tax-quarter-calculations-list">
    <?= $this->render('_search', ['model' => $searchModel, 'searchApplied' => $searchApplied]); ?>

    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-tc',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\TaxQuarterCalculations */
                    $result['data-id'] = $model->id;
                    return $result;
                },
                'showFooter' => true,
                'footerRowOptions' => ['class' => 'text-center font-weight-bold'],
                'columns' => [
                    [
                        'attribute' => 'periodName',
                        'footer' => 'Итого:',
                        'footerOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'class' => TotalsColumn::className(),
                        'attribute' => 'kt',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => TotalsColumn::className(),
                        'attribute' => 'dt',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => TotalsColumn::className(),
                        'attribute' => 'diff',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '90'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => TotalsColumn::className(),
                        'attribute' => 'amount',
                        'label' => 'Σ налога',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '90'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => TotalsColumn::className(),
                        'attribute' => 'amount_fact',
                        'label' => 'Σ оплачено',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '90'],
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
$url = \yii\helpers\Url::to(['/tax-quarter-calculations/update']);
$this->registerJs(<<<JS
$("#table-tc tbody tr").css("cursor", "pointer");
$("#table-tc tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    if (e.target == this && id) location.href = "$url?id=" + id;
});
JS
, \yii\web\View::POS_READY);
?>
