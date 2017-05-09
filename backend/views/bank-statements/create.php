<?php

/* @var $this yii\web\View */
/* @var $model common\models\BankStatements */

$this->title = 'Новый расход | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Банковские движения', 'url' => ['/bank-statements']];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="bank-statements-create">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
