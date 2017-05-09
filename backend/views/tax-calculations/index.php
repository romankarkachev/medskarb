<?php

use yii\helpers\Html;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaxCalculationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchApplied bool */

$this->title = 'Расчеты налога | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Расчеты налога';

$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/tax-calculations'], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];
?>
<div class="tax-calculations-list">
    <p>
        <?= Html::a('<i class="fa fa-plus-circle"></i> Создать', ['create'], ['class' => 'btn btn-success']) ?>

    </p>
    <?= $this->render('_search', ['model' => $searchModel, 'searchApplied' => $searchApplied]); ?>

    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-tc',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\TaxCalculations */
                    $result['data-id'] = $model->id;
                    return $result;
                },
                'columns' => [
                    'periodName',
                    [
                        'attribute' => 'dt',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'kt',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'diff',
                        'format' => ['decimal', 'decimals' => 2],
                        'options' => ['width' => '90'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'amount',
                        'label' => 'Σ налога',
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
$url = \yii\helpers\Url::to(['/tax-calculations/update']);
$this->registerJs(<<<JS
$("#table-tc tbody tr").css("cursor", "pointer");
$("#table-tc tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    if (e.target == this && id) location.href = "$url?id=" + id;
});
JS
    , \yii\web\View::POS_READY);
?>
