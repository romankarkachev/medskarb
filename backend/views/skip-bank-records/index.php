<?php

use yii\helpers\Html;
use backend\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SkipBankRecordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пропускаемые записи при импорте из банковской выписки | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Пропускаемые записи при импорте из банковской выписки';
?>
<div class="skip-bank-records-list">
    <p>
        <?= Html::a('<i class="fa fa-plus-circle"></i> Создать', ['create'], ['class' => 'btn btn-success']) ?>

    </p>
    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-sbr',
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\SkipBankRecords*/
                    $result['data-id'] = $model->id;
                    return $result;
                },
                'columns' => [
                    'substring:ntext',
                    ['class' => 'backend\components\grid\ActionColumn'],
                ],
            ]); ?>

        </div>
    </div>
</div>
<?php
$url = \yii\helpers\Url::to(['/skip-bank-records/update']);
$this->registerJs(<<<JS
$("#table-sbr tbody tr").css("cursor", "pointer");
$("#table-sbr tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    if (e.target == this && id) location.href = "$url?id=" + id;
});
JS
, \yii\web\View::POS_READY);
?>
