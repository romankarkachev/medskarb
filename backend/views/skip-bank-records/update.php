<?php

use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\SkipBankRecords */

$this->title = $model->substring . HtmlPurifier::process(' &mdash; Пропускаемые записи | ') . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Пропускаемые записи', 'url' => ['/skip-bank-records']];
$this->params['breadcrumbs'][] = $model->substring;
?>
<div class="skip-bank-records-update">
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
