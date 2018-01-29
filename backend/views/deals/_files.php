<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\file\FileInput;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $dpFiles \yii\data\ArrayDataProvider */
?>

<div class="deals-files collapse" id="frmFiles" aria-expanded="false">
    <div class="card">
        <div class="card-header card-header-default"><i class="fa fa-cloud"></i> Прикрепленные файлы</div>
        <div class="card-block">
            <?php Pjax::begin(['id' => 'afs']); ?>

            <?= GridView::widget([
                'dataProvider' => $dpFiles,
                'id' => 'gw-files',
                'layout' => '{items}',
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'columns' => [
                    [
                        'attribute' => 'ofn',
                        'label' => 'Имя файла',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $column) {
                            /* @var $model \common\models\DealsFiles|\common\models\CounteragentsFiles|\common\models\DocumentsFiles */
                            /** @var $column \yii\grid\DataColumn */

                            $rep = '';
                            if ($model['type'] > 0) {
                                $rep = ' <small class="text-muted">' . $model['rep'] . '</small>';
                                if (isset($model['doc_id'])) $rep .= ' ' . Html::a(
                                    '<i class="fa fa-share-square-o" aria-hidden="true"></i>',
                                    ['/documents/update', 'id' => $model['doc_id']],
                                    [
                                        'title' => 'Открыть документ в новом окне',
                                        'data-pjax' => '0',
                                        'target' => '_blank',
                                    ]
                                );
                            }

                            $url = '';
                            switch ($model['sort']) {
                                case 0:
                                    $url = Url::to(['/deals/preview-file']);
                                    break;
                                case 1:
                                    $url = Url::to(['/counteragents/preview-file']);
                                    break;
                                case 2:
                                case 3:
                                    $url = Url::to(['/documents/preview-file']);
                                    break;
                            }

                            return Html::a($model[$column->attribute], '#', [
                                'class' => 'link-ajax',
                                'id' => 'previewFile-' . $model['guid'],
                                'data-id' => $model['guid'],
                                'data-url' => $url,
                                'title' => 'Предварительный просмотр',
                                'data-pjax' => 0,
                            ]) . $rep;
                        },
                    ],
                    [
                        'label' => 'Скачать',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            /* @var $model \common\models\DealsFiles|\common\models\CounteragentsFiles|\common\models\DocumentsFiles */
                            return Html::a('<i class="fa fa-cloud-download text-info" style="font-size: 18pt;"></i>',
                                $model['url'], [
                                    'title' => ($model['ofn'] != '' ? $model['ofn'] . ', ' : '') . Yii::$app->formatter->asShortSize($model['size'], false),
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]);
                        },
                        'options' => ['width' => '60'],
                    ],
                    [
                        'attribute' => 'uploaded_at',
                        'label' => 'Загружен',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
                        'format' =>  ['date', 'dd.MM.Y HH:mm'],
                        'options' => ['width' => '130']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Действия',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                if ($model['type'] == 0)
                                    return Html::a('<i class="fa fa-trash-o"></i>', ['/deals/delete-file', 'id' => $model['id']], ['title' => Yii::t('yii', 'Удалить'), 'class' => 'btn btn-sm btn-danger', 'aria-label' => Yii::t('yii', 'Delete'), 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'), 'data-method' => 'post', 'data-pjax' => '0',]);
                            }
                        ],
                        'options' => ['width' => '40'],
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>

            <?= FileInput::widget([
                'id' => 'new_files',
                'name' => 'files[]',
                'options' => ['multiple' => true],
                'pluginOptions' => [
                    'maxFileCount' => 10,
                    'uploadAsync' => false,
                    'uploadUrl' => Url::to(['/deals/upload-files']),
                    'uploadExtraData' => [
                        'owner_id' => $model->id,
                    ],
                    'fileActionSettings' => [
                        'uploadIcon' => '<i class="fa fa-cloud-upload"></i>',
                        'uploadClass' => 'btn btn-sm btn-success',

                        'removeIcon' => '<i class="fa fa-remove"></i>',
                        'removeClass' => 'btn btn-sm btn-danger',

                        'showZoom' => false,
                        //'zoomIcon' => '<i class="fa fa-search-plus"></i>',
                        //'zoomClass' => 'btn btn-sm btn-secondary',

                        'showDrag' => false,
                        //'dragIcon' => '<i class="fa fa-bars"></i>',

                        'indicatorNew' => '<i class="fa fa-asterisk text-warning"></i>',
                        'indicatorLoading' => '<i class="fa fa-spinner fa-pulse fa-fw text-muted"></i>',
                    ],

                    'uploadClass' => 'btn btn-secondary',

                    'removeIcon' => '<i class="fa fa-trash"></i> ',
                    'cancelIcon' => '<i class="fa fa-ban"></i> ',
                    'uploadIcon' => '<i class="fa fa-cloud-upload"></i> ',
                    'browseIcon' => '<i class="fa fa-folder-open"></i> ',
                ]
            ]) ?>

        </div>
    </div>
</div>
<div id="mw_preview" class="modal fade" tabindex="false" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="modal_title" class="modal-title">Предпросмотр файла</h4>
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
<?php
$this->registerJs(<<<JS
$("#new_files").on("filebatchuploadsuccess", function(event, data, previewId, index) {
    $.pjax.reload({container:"#afs"});
});

// Обработчик щелчка по ссылкам в колонке "Наименование" в таблице файлов.
//
function previewFileOnClick() {
    id = $(this).attr("data-id");
    url = $(this).attr("data-url");
    if (id != "" && url != "") {
        $("#modal_body").html('<p class="text-center"><i class="fa fa-cog fa-spin fa-3x text-info"></i><span class="sr-only">Подождите...</span></p>');
        $("#mw_preview").modal();
        $("#modal_body").load(url + "?guid=" + id);
    }

    return false;
} // previewFileOnClick()

$(document).on("click", "a[id ^= 'previewFile']", previewFileOnClick);
JS
, \yii\web\View::POS_READY);
?>
