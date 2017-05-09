<?php

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $contracts array */

$this->title = 'Новая сделка | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Сделки', 'url' => ['/deals']];
$this->params['breadcrumbs'][] = 'Новая *';
?>
<div class="deals-create">
    <?= $this->render('_form', ['model' => $model, 'contracts' => $contracts]) ?>

</div>
