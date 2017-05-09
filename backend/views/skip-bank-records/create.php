<?php

/* @var $this yii\web\View */
/* @var $model common\models\SkipBankRecords */

$this->title = 'Новый элемент | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Пропускаемые записи', 'url' => ['/skip-bank-records']];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="skip-bank-records-create">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
