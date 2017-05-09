<?php

/* @var $this yii\web\View */
/* @var $model common\models\TaxCalculations */

$this->title = 'Новый расчет | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Расчеты налога', 'url' => ['/tax-calculations']];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="tax-calculations-create">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
