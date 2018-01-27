<?php

use yii\helpers\Html;
use backend\components\grid\GridView;
use backend\components\grid\TotalsBankColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BankStatementsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchApplied bool */
/* @var $tax_inspection_id integer идентификатор налоговой инспекции */

$this->title = 'Банковские движения | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'Банковские движения';

$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/bank-statements'], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];

$uploadDir = Yii::getAlias('@uploads-bs') . '/';

if (!isset($tax_inspection_id)) $tax_inspection_id = null;
?>
<div class="bank-statements-list">
    <div class="card">
        <div class="card-block">
            <p class="mb-0">
                <?= Html::a('<i class="fa fa-plus"></i> Добавить', ['create'], ['class' => 'btn btn-success']) ?>

                <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> Удалить...', ['clear'], ['class' => 'btn btn-danger pull-right']) ?>

                <?= Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Импорт из Excel', ['import'], ['class' => 'btn btn-secondary pull-right']) ?>

            </p>
        </div>
    </div>
    <?= $this->render('_search', ['model' => $searchModel, 'searchApplied' => $searchApplied]); ?>

    <div class="card">
        <div class="card-block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'table-statements',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    /* @var $model \common\models\BankStatements */
                    /* @var $column \yii\grid\DataColumn */

                    $result['data-id'] = $model->id;
                    $result['data-docrep'] = 'ПП № ' . $model->bank_doc_num . ' от ' . Yii::$app->formatter->asDate($model->bank_date, 'php:d F Y');
                    if ($model->is_active == 0) $result['class'] = 'text-muted';
                    return $result;
                },
                'showFooter' => true,
                'columns' => [
                    [
                        'attribute' => 'bank_date',
                        'format' => 'date',
                        'options' => ['width' => '90'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'bank_doc_num',
                        'label' => '№ ПП',
                        'options' => ['width' => '80'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => TotalsBankColumn::className(),
                        'attribute' => 'caName',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $column) use ($uploadDir, $tax_inspection_id) {
                            /* @var $model \common\models\BankStatements */
                            /* @var $column \yii\grid\DataColumn */

                            $manual = '';
                            if ($model->type == \common\models\BankStatements::TYPE_MANUAL)
                                $manual = '<i class="fa fa-pencil text-info" aria-hidden="true" title="Добавлено вручную"></i>';

                            $ca_name = '<em>' . $model->bank_description . '</em> ';
                            if ($model->ca_id != null) {
                                $ca_name = '<strong>' . $model->caName . '</strong> ';
                                if ($tax_inspection_id != null && $tax_inspection_id == $model->ca_id)
                                    $ca_name .= '<p class="mb-0"><em>' . $model->bank_description . '</em></p>';
                            }

                            $files = explode(\common\models\BankStatementsSearch::FILES_DELIMITER, $model->filesDetails);
                            $filesDetails = '';
                            if ($model->filesCount > 0) {
                                // реально берется только первое изображение
                                // если когда-нибудь будут с этим проблемы, то подумать, как сделать, чтобы по щелчку
                                // по камере отображалось первое изображение в этой группе
                                $filesDetails = ' ' . Html::a('<i class="fa fa-camera" aria-hidden="true"></i>', $uploadDir . $files[0], [
                                    'rel' => 'fancybox',
                                    'title' => 'Чек № ' . $model->bank_doc_num .
                                        ' от ' . Yii::$app->formatter->asDate($model->bank_date, 'php:d.m.Y') .
                                        ' на сумму ' . Yii::$app->formatter->asCurrency($model->bank_amount_dt != null ? $model->bank_amount_dt : $model->bank_amount_kt),
                                ]);
                            }

                            return $ca_name . $manual . $filesDetails;
                        },
                    ],
                    [
                        'attribute' => 'periodName',
                        'options' => ['width' => '130'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'label' => 'Сумма',
                        'contentOptions' => function ($model, $key, $index, $column) {
                            /* @var $model \common\models\BankStatements */
                            /* @var $column \yii\grid\DataColumn */

                            if ($model->bank_amount_dt == null || $model->bank_amount_dt == 0)
                                return ['class' => 'text-success text-right'];
                            else
                                return ['class' => 'text-danger text-right'];
                        },
                        'value' => function ($model, $key, $index, $column) {
                            /** @var \common\models\BankStatements $model */
                            /** @var \yii\grid\DataColumn $column */
                            if ($model->bank_amount_dt == null || $model->bank_amount_dt == 0)
                                return '+' . Yii::$app->formatter->asDecimal($model->bank_amount_kt, 2);
                            else
                                return '-' . Yii::$app->formatter->asDecimal($model->bank_amount_dt, 2);
                        },
                        'options' => ['width' => '120'],
                        'headerOptions' => ['class' => 'text-center'],
                    ],
                ],
            ]); ?>

        </div>
    </div>
    <div id="mw_summary" class="modal fade" tabindex="false" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
<?= \newerton\fancybox\FancyBox::widget([
    'target' => 'a[rel^="fancybox"]',
    'helpers' => true,
    'mouse' => true,
    'config' => [
        'maxWidth' => '90%',
        'maxHeight' => '90%',
        'playSpeed' => 4000,
        'padding' => 0,
        'fitToView' => false,
        'width' => '70%',
        'height' => '70%',
        'autoSize' => false,
        'closeClick' => false,
        'openEffect' => 'elastic',
        'closeEffect' => 'elastic',
        'prevEffect' => 'elastic',
        'nextEffect' => 'elastic',
        'closeBtn' => false,
        'openOpacity' => true,
        'helpers' => [
            'title' => ['type' => 'float'],
            'buttons' => [],
            'thumbs' => ['width' => 68, 'height' => 50],
            'overlay' => [
                'css' => [
                    'background' => 'rgba(0, 0, 0, 0.8)'
                ],
                // ура! гадость наконец исправлена!
                // чтобы не прокручивалось вверх при щелчке по изображению, делаем так:
                'locked' => false,
            ]
        ],
    ]
]); ?>

<?php
$url = \yii\helpers\Url::to(['/bank-statements/summary-card']);
$this->registerJs(<<<JS
$("#table-statements tbody tr").css("cursor", "help");
$("#table-statements tbody td").click(function (e) {
    var id = $(this).closest("tr").data("id");
    var docrep = $(this).closest("tr").data("docrep");
    if (e.target == this && id) {
        $("#modal_title").text(docrep);
        $("#modal_title_right").text("ID: " + id);
        $("#modal_body").html('<p class="text-center"><i class="fa fa-cog fa-spin fa-3x text-info"></i><span class="sr-only">Подождите...</span></p>');
        $("#mw_summary").modal();
        $("#modal_body").load("$url?id=" + id);
    }
});
JS
, \yii\web\View::POS_READY);
?>
