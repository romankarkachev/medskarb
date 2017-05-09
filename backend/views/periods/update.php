<?php

use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\Periods */

$this->title = $model->name . HtmlPurifier::process(' &mdash; Периоды | ') . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Периоды', 'url' => ['/periods']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="periods-update">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
