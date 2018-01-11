<?php

use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$message_create_account = Yii::t('user', 'Create a user account');

$this->title = $message_create_account.' | '.Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['/users']];
$this->params['breadcrumbs'][] = 'Новый *';
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
                    'items' => [
                        ['label' => Yii::t('user', 'Account details'), 'url' => ['/users/create']],
                        ['label' => Yii::t('user', 'Profile details'), 'options' => [
                            'onclick' => 'return false;',
                        ], 'linkOptions' => [
                            'class' => 'text-muted',
                        ]],
                        ['label' => Yii::t('user', 'Information'), 'options' => [
                            'onclick' => 'return false;',
                        ], 'linkOptions' => [
                            'class' => 'text-muted',
                        ]],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?= $this->render('_user', ['user' => $user]) ?>

    </div>
</div>
