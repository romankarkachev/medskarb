<?php

use yii\helpers\HtmlPurifier;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $content string */

$this->title = $user->username . HtmlPurifier::process(' &mdash; ' . Yii::t('user', 'Update user account')) . ' | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['/users']];
$this->params['breadcrumbs'][] = $user->username;

$items = [
    [
        'label' => Yii::t('user', 'Account details'),
        'url' => ['/users/update', 'id' => $user->id]
    ],
    [
        'label' => Yii::t('user', 'Profile details'),
        'url' => ['/users/update-profile', 'id' => $user->id]
    ],
    ['label' => Yii::t('user', 'Information'), 'url' => ['/users/info', 'id' => $user->id]],
    '<hr>',
    [
        'label' => Yii::t('user', 'Confirm'),
        'url'   => ['/users/confirm', 'id' => $user->id],
        'visible' => !$user->isConfirmed,
        'linkOptions' => [
            'class' => 'text-success',
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
        ],
    ],
    [
        'label' => Yii::t('user', 'Block'),
        'url'   => ['/users/block', 'id' => $user->id],
        'visible' => !$user->isBlocked,
        'linkOptions' => [
            'class' => 'text-danger',
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
        ],
    ],
    [
        'label' => Yii::t('user', 'Unblock'),
        'url'   => ['/users/block', 'id' => $user->id],
        'visible' => $user->isBlocked,
        'linkOptions' => [
            'class' => 'text-success',
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
        ],
    ],
    [
        'label' => Yii::t('user', 'Delete'),
        'url'   => ['/users/delete', 'id' => $user->id],
        'linkOptions' => [
            'class' => 'text-danger',
            'data-method' => 'post',
            'data-confirm' => Yii::t('user', 'Are you sure you want to delete this user?'),
        ],
    ],
];

$current_url = \yii\helpers\Url::current();
foreach ($items as $index => $item)
    if (isset($item['url']))
        if (stripos(\yii\helpers\Url::to($item['url']), $current_url) !== false)
            $items[$index]['linkOptions']['class'] = 'font-weight-bold';
?>

<?= $this->render('/_alert', [
    'module' => Yii::$app->getModule('user'),
]) ?>

<div class="row">
    <div class="col-md-3">
        <div class="card card-accent-primary">
            <div class="card-block">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'list-group',
                    ],
                    'items' => $items,
                ]) ?>

            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?= $content ?>

    </div>
</div>
