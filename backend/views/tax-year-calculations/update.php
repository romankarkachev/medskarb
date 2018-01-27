<?php

use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\TaxYearCalculations */

$this->title = $model->year . HtmlPurifier::process(' &mdash; Расчет годового платежа по УСН и ПФ | ') . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Расчеты по налогу', 'url' => ['/tax-quarter-calculations']];
$this->params['breadcrumbs'][] = $model->year;

$this->params['breadcrumbsRight'][] = ['label' => '', 'icon' => 'fa fa-info-circle', 'url' => '#mw_td', 'data-target' => '#mw_td', 'data-toggle' => 'modal'];
?>
<div class="tax-year-calculations-update">
    <?= $this->render('_form', ['model' => $model]) ?>

    <?= $this->render('_record_details', ['model' => $model]) ?>

</div>
