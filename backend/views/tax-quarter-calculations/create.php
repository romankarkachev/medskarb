<?php

/* @var $this yii\web\View */
/* @var $model common\models\TaxQuarterCalculations */

$this->title = 'Новый расчет авансового платежа по УСН | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Авансы по налогу', 'url' => ['/tax-quarter-calculations']];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="tax-quarter-calculations-create">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
