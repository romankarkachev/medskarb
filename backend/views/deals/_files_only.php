<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\file\FileInput;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $dpFiles \yii\data\ActiveDataProvider */
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
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                    ],
                    [
                        'label' => 'Скачать',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a('<i class="fa fa-cloud-download text-info" style="font-size: 18pt;"></i>', ['/deals/download', 'id' => $data->id], ['title' => ($data->ofn != ''?$data->ofn.', ':'').Yii::$app->formatter->asShortSize($data->size, false), 'target' => '_blank', 'data-pjax' => 0]);
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
                                return Html::a('<i class="fa fa-trash-o"></i>', ['/deals/delete-file', 'id' => $model->id], ['title' => Yii::t('yii', 'Удалить'), 'class' => 'btn btn-sm btn-danger', 'aria-label' => Yii::t('yii', 'Delete'), 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'), 'data-method' => 'post', 'data-pjax' => '0',]);
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
<?php
$this->registerJs(<<<JS
$("#new_files").on("filebatchuploadsuccess", function(event, data, previewId, index) {
    $.pjax.reload({container:"#afs"});
});
JS
, \yii\web\View::POS_READY);
?>
