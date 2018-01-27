<?php

use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaxYearCalculationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchApplied bool */

$this->title = 'Расчеты годовых платежей по УСН | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Расчеты годовых платежей по УСН';

$this->params['breadcrumbsRight'][] = ['label' => 'Создать', 'icon' => 'fa fa-plus-circle fa-lg', 'url' => ['create'], 'class' => 'btn text-success'];
$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/tax-quarter-calculations'], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];
?>
<div class="tax-year-calculations-list">
    <?= $this->render('_search', ['model' => $searchModel, 'searchApplied' => $searchApplied]); ?>

    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-tyc',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\TaxQuarterCalculations */
                    $result['data-id'] = $model->id;
                    return $result;
                },
                'columns' => [
                    'year',
                    [
                        'attribute' => 'kt',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'dt',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'base',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'rate',
                        'value' => function ($model, $key, $index, $column) {
                            /* @var $model \common\models\TaxYearCalculations */
                            /* @var $column \yii\grid\DataColumn */

                            return Yii::$app->formatter->asInteger($model->{$column->attribute}) . ' %';
                        },
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'amount',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'amount_fact',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'amount_to_pay',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'pf_base',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'pf_limit',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'pf_rate',
                        'value' => function ($model, $key, $index, $column) {
                            /* @var $model \common\models\TaxYearCalculations */
                            /* @var $column \yii\grid\DataColumn */

                            return Yii::$app->formatter->asInteger($model->{$column->attribute}) . ' %';
                        },
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'pf_amount',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    // 'calculation_details:ntext',
                    // 'comment:ntext',
                ],
            ]); ?>

        </div>
    </div>
</div>
<?php
$url = \yii\helpers\Url::to(['/tax-year-calculations/update']);
$this->registerJs(<<<JS
$("#table-tyc tbody tr").css("cursor", "pointer");
$("#table-tyc tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    if (e.target == this && id) location.href = "$url?id=" + id;
});
JS
, \yii\web\View::POS_READY);
?>
