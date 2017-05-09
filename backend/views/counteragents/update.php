<?php

use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\Counteragents */
/* @var $dpFiles \yii\data\ActiveDataProvider */

$this->title = $model->name . HtmlPurifier::process(' &mdash; Контрагенты | ') . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Контрагенты', 'url' => ['/counteragents']];
$this->params['breadcrumbs'][] = $model->name;

$label_files = 'Файлы' . ($dpFiles->totalCount > 0 ? ' (<strong>'.$dpFiles->totalCount.'</strong>)' : '');
$this->params['breadcrumbsRight'][] = ['label' => $label_files, 'icon' => 'fa fa-cloud', 'url' => '#frmFiles', 'data-target' => '#frmFiles', 'data-toggle' => 'collapse', 'aria-expanded' => 'false', 'aria-controls' => 'frmFiles'];
$this->params['breadcrumbsRight'][] = ['label' => '', 'icon' => 'fa fa-info-circle', 'url' => '#mw_td', 'data-target' => '#mw_td', 'data-toggle' => 'modal'];
?>
<div class="counteragents-update">
    <?= $this->render('_files', ['model' => $model, 'dpFiles' => $dpFiles]) ?>

    <?= $this->render('_form', ['model' => $model]) ?>

    <?= $this->render('_record_details', ['model' => $model]) ?>

</div>
