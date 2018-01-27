<?php

/* @var $this yii\web\View */
/* @var $model common\models\TaxYearCalculations */

$this->title = 'Новый расчет годового платежа по УСН и ПФ | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Расчеты по налогу', 'url' => ['/tax-year-calculations']];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="tax-year-calculations-create">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
