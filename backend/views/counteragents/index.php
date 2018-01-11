<?php

use yii\helpers\Html;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CounteragentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchApplied bool */

$this->title = 'Контрагенты | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Контрагенты';

$this->params['breadcrumbsRight'][] = ['label' => 'Создать', 'icon' => 'fa fa-plus-circle fa-lg', 'url' => ['create'], 'class' => 'btn text-success'];
$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/counteragents'], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];
?>
<div class="counteragents-list">
    <div class="card">
        <div class="card-block">
            <p class="mb-0">
                <?= Html::a('<i class="fa fa-plus-circle"></i> Создать', ['create'], ['class' => 'btn btn-success']) ?>

                <?= Html::a('<i class="fa fa-id-card-o" aria-hidden="true"></i> Единый реестр', ['/counteragents/er'], ['class' => 'btn btn-secondary pull-right']) ?>

            </p>
        </div>
    </div>
    <?= $this->render('_search', ['model' => $searchModel, 'searchApplied' => $searchApplied]); ?>

    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-counteragents',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\Counteragents */
                    $result['data-id'] = $model->id;
                    $result['data-name'] = $model->name;
                    return $result;
                },
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $column) {
                            /** @var \common\models\Counteragents $model */
                            /** @var \yii\grid\DataColumn $column */
                            return Html::a($model->name, ['/counteragents/update', 'id' => $model->id]);
                        },
                    ],
                    'phones',
                    [
                        'attribute' => 'contractRep',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $column) {
                            /** @var \common\models\Counteragents $model */
                            /** @var \yii\grid\DataColumn $column */
                            if ($model->contractRep != '-')
                                return Html::a($model->contractRep, ['/documents/update', 'id' => $model->contract_id], ['target' => '_blank']);
                            else
                                return $model->contractRep;
                        },
                    ],
                    [
                        'attribute' => 'typeName',
                        'options' => ['width' => '90'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    ['class' => 'backend\components\grid\ActionColumn'],
                ],
            ]); ?>

        </div>
    </div>
    <div id="mw_summary" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-info" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal_title" class="modal-title">Modal title</h4>
                    <small id="modal_title_right" class="form-text"></small>
                </div>
                <div id="modal_body" class="modal-body">
                    <p>One fine body…</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$url = \yii\helpers\Url::to(['/counteragents/summary-card']);
$this->registerJs(<<<JS
$("#table-counteragents tbody tr").css("cursor", "help");
$("#table-counteragents tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    var name = $(this).closest("tr").data("name");
    if (e.target == this && id) {
        $("#modal_title").text(name);
        $("#modal_title_right").text("ID: " + id);
        $("#modal_body").html('<p class="text-center"><i class="fa fa-cog fa-spin fa-3x text-info"></i><span class="sr-only">Подождите...</span></p>');
        $("#mw_summary").modal();
        $("#modal_body").load("$url?id=" + id);
    }
});
JS
, \yii\web\View::POS_READY);
?>
