<?php

/* @var $this yii\web\View */
/* @var $model common\models\Counteragents */

$this->title = 'Новый контрагент | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Контрагенты', 'url' => ['/counteragents']];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="counteragents-create">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
