<?php

use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\TaxQuarterCalculations */

$this->title = $model->periodName . HtmlPurifier::process(' &mdash; Расчет авансового платежа по УСН | ') . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Авансы по налогу', 'url' => ['/tax-quarter-calculations']];
$this->params['breadcrumbs'][] = $model->periodName;

$this->params['breadcrumbsRight'][] = ['label' => '', 'icon' => 'fa fa-info-circle', 'url' => '#mw_td', 'data-target' => '#mw_td', 'data-toggle' => 'modal'];
?>
<div class="tax-quarter-calculations-update">
    <?= $this->render('_form', ['model' => $model]) ?>

    <?= $this->render('_record_details', ['model' => $model]) ?>

</div>
