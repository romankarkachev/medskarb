<?php

/* @var $this yii\web\View */
/* @var $model common\models\Periods */

$this->title = 'Новый период | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Периоды', 'url' => ['/periods']];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="periods-create">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
