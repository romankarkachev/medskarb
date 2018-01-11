<?php

use yii\helpers\Html;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DocumentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchApplied bool */
/* @var $action_id string (для управления видимостью колонки "Тип документа", сброса сортировки) */
/* @var $type_id integer тип документа (используется для кнопки создания) */
/* @var $final_bc string (для формирования хлебных крошек, заголовка страницы) */

if ($final_bc == null) {
    $this->title = 'Документы | ' . Yii::$app->name;
    $this->params['breadcrumbs'][] = 'Документы';
}
else {
    $this->title = $final_bc . ' | ' . Yii::$app->name;
    $this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['/documents']];
    $this->params['breadcrumbs'][] = $final_bc;
}

$this->params['breadcrumbsRight'][] = ['label' => 'Создать', 'icon' => 'fa fa-plus-circle fa-lg', 'url' => ['create'], 'class' => 'btn text-success', 'data-params' => ['type_id' => $type_id, 'action_id' => $action_id], 'data-method' => 'post'];
$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/documents/' . $action_id], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];
?>
<div class="documents-list">
    <?= $this->render('_search', [
        'model' => $searchModel,
        'searchApplied' => $searchApplied,
        'action_id' => $action_id,
    ]); ?>

    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-documents',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\Documents */
                    $result['data-id'] = $model->id;
                    return $result;
                },
                'columns' => [
                    'caName',
                    [
                        'attribute' => 'typeName',
                        'visible' => $action_id == null,
                    ],
                    'documentRep',
                    [
                        'attribute' => 'amount',
                        'format' => ['decimal', 'decimals' => 2],
                    ],
                    [
                        'attribute' => 'filesCount',
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
$url = \yii\helpers\Url::to(['/documents/update']);
$this->registerJs(<<<JS
$("#table-documents tbody tr").css("cursor", "pointer");
$("#table-documents tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    if (e.target == this && id) location.href = "$url?id=" + id;
});
JS
, \yii\web\View::POS_READY);
?>

