<?php

use yii\helpers\Html;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PeriodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Периоды | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Периоды';
?>
<div class="periods-list">
    <p>
        <?= Html::a('<i class="fa fa-plus-circle"></i> Создать', ['create'], ['class' => 'btn btn-success']) ?>

    </p>
    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-periods',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\Periods */
                    $result['data-id'] = $model->id;
                    return $result;
                },
                'columns' => [
                    'name',
                    [
                        'attribute' => 'start',
                        'format' => ['date'],
                        'options' => ['width' => '150'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'end',
                        'format' => ['date'],
                        'options' => ['width' => '150'],
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
$url = \yii\helpers\Url::to(['/periods/update']);
$this->registerJs(<<<JS
$("#table-periods tbody tr").css("cursor", "pointer");
$("#table-periods tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    if (e.target == this && id) location.href = "$url?id=" + id;
});
JS
, \yii\web\View::POS_READY);
?>
