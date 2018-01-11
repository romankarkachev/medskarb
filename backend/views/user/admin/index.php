<?php

use yii\helpers\Html;
use backend\components\grid\GridView;
use dektrium\user\models\UserSearch;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel UserSearch */
/* @var bool $searchApplied */

$table_name = 'table-users';

$this->title = Yii::t('user', 'Manage users').' | '.Yii::$app->name;
$this->params['breadcrumbs'][] = 'Пользователи';

$this->params['breadcrumbsRight'][] = ['label' => 'Создать', 'icon' => 'fa fa-plus-circle fa-lg', 'url' => ['create'], 'class' => 'btn text-success'];
$this->params['breadcrumbsRight'][] = ['label' => 'Отбор', 'icon' => 'fa fa-filter', 'url' => '#frmSearch', 'data-target' => '#frmSearch', 'data-toggle' => 'collapse', 'aria-expanded' => $searchApplied === true ? 'true' : 'false', 'aria-controls' => 'frmSearch'];
$this->params['breadcrumbsRight'][] = ['icon' => 'fa fa-sort-amount-asc', 'url' => ['/users'], 'title' => 'Сбросить отбор и применить сортировку по-умолчанию'];
?>

<?= $this->render('_search', ['model' => $searchModel, 'searchApplied' => $searchApplied]); ?>

<div class="card">
    <div class="card-block">
        <?= GridView::widget([
            'dataProvider' 	=> $dataProvider,
            'id' => $table_name,
            'rowOptions'   => function ($model, $key, $index, $grid) {
                /* @var $model \common\models\User */
                /* @var $column \yii\grid\DataColumn */

                $result['data-id'] = $model->id;
                return $result;
            },
            'layout' => '{items}{pager}',
            'tableOptions' => ['class' => 'table table-striped table-hover table-responsive'],
            'columns' => [
                [
                    'attribute' => 'profileName',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        /* @var $model \common\models\User */
                        /* @var $column \yii\grid\DataColumn */

                        $addon = '';
                        if (!$model->isConfirmed) $addon = ' <em class="text-muted">(не активирован)</em>';
                        return $model->{$column->attribute} . $addon;
                    },
                ],
                'username',
                'email:email',
                'roleName',
                [
                    'header' => Yii::t('user', 'Block status'),
                    'value' => function ($model, $key, $index, $column) {
                        /* @var $model \common\models\User */
                        /* @var $column \yii\grid\DataColumn */

                        if ($model->isBlocked) {
                            return Html::a(Yii::t('user', 'Unblock'), ['/users/block', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-success btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                            ]);
                        } else {
                            return Html::a(Yii::t('user', 'Block'), ['/users/block', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-danger btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                            ]);
                        }
                    },
                    'format' => 'raw',
                    'options' => ['width' => 130, 'text-align' => 'center'],
                ],
                [
                    'class' => 'backend\components\grid\ActionColumn',
                    'template' => '{update} {info} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<i class="fa fa-user"></i>', ['/users/update-profile', 'id' => $model->id], ['title' => 'Профиль пользователя', 'class' => 'btn btn-sm btn-secondary text-primary']);
                        },

                        'delete' => function ($url, $model) {
                            return Html::a('<i class="fa fa-trash-o"></i>', ['/users/delete', 'id' => $model->id], ['title' => Yii::t('yii', 'Удалить'), 'class' => 'btn btn-sm btn-danger', 'aria-label' => Yii::t('yii', 'Delete'), 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'), 'data-method' => 'post', 'data-pjax' => '0',]);
                        },
                    ],
                    'options' => ['width' => '120'],
                ],
            ],
        ]); ?>

    </div>
</div>
