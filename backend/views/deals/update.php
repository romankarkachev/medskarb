<?php

use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $contracts array */
/* @var $dpFiles \yii\data\ActiveDataProvider */
/* @var $dpDocumentsUnattached \yii\data\ActiveDataProvider */
/* @var $dpDocumentsRecepit \yii\data\ActiveDataProvider */
/* @var $dpDocumentsExpense \yii\data\ActiveDataProvider */
/* @var $dpDocumentsBrokerRu \yii\data\ActiveDataProvider */
/* @var $dpDocumentsBrokerLnr \yii\data\ActiveDataProvider */

$dealRep = '№ ' . $model->id;
if ($model->deal_date != null) $dealRep .= ' от ' . Yii::$app->formatter->asDate($model->deal_date, 'php:d.m.Y');

$this->title = $dealRep . HtmlPurifier::process(' &mdash; Сделки | ') . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Сделки', 'url' => ['/deals']];
$this->params['breadcrumbs'][] = $dealRep;

$label_files = 'Файлы' . ($dpFiles->totalCount > 0 ? ' (<strong>'.$dpFiles->totalCount.'</strong>)' : '');
$this->params['breadcrumbsRight'][] = ['label' => $label_files, 'icon' => 'fa fa-cloud', 'url' => '#frmFiles', 'data-target' => '#frmFiles', 'data-toggle' => 'collapse', 'aria-expanded' => 'false', 'aria-controls' => 'frmFiles'];
$this->params['breadcrumbsRight'][] = ['label' => '', 'icon' => 'fa fa-info-circle', 'url' => '#mw_td', 'data-target' => '#mw_td', 'data-toggle' => 'modal'];
?>
<div class="deals-update">
    <?= $this->render('_files', [
        'model' => $model,
        'dpFiles' => $dpFiles,
    ]) ?>

    <?= $this->render('_form', [
        'model' => $model,
        'contracts' => $contracts,
    ]) ?>

    <?= $this->render('_documents', [
        'model' => $model,
        'dpDocumentsUnattached' => $dpDocumentsUnattached,
        'dpDocumentsRecepit' => $dpDocumentsRecepit,
        'dpDocumentsExpense' => $dpDocumentsExpense,
        'dpDocumentsBrokerRu' => $dpDocumentsBrokerRu,
        'dpDocumentsBrokerLnr' => $dpDocumentsBrokerLnr,
    ]) ?>

    <?= $this->render('_record_details', ['model' => $model]) ?>

</div>
