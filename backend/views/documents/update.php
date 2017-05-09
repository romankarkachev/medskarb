<?php

use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\Documents */
/* @var $dpFiles \yii\data\ActiveDataProvider */
/* @var $action_id string|null */
/* @var $final_bc string */

$this->title = $model->documentRep . HtmlPurifier::process(' &mdash; Документы | ') . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['/documents']];
if ($action_id != null) $this->params['breadcrumbs'][] = ['label' => $final_bc, 'url' => ['/documents/' . $action_id]];
$this->params['breadcrumbs'][] = $model->documentRep;

$label_files = 'Файлы' . ($dpFiles->totalCount > 0 ? ' (<strong>'.$dpFiles->totalCount.'</strong>)' : '');
$this->params['breadcrumbsRight'][] = ['label' => $label_files, 'icon' => 'fa fa-cloud', 'url' => '#frmFiles', 'data-target' => '#frmFiles', 'data-toggle' => 'collapse', 'aria-expanded' => 'false', 'aria-controls' => 'frmFiles'];
$this->params['breadcrumbsRight'][] = ['label' => '', 'icon' => 'fa fa-info-circle', 'url' => '#mw_td', 'data-target' => '#mw_td', 'data-toggle' => 'modal'];
?>
<div class="documents-update">
    <?= $this->render('_files', ['model' => $model, 'dpFiles' => $dpFiles]) ?>

    <?= $this->render('_form', [
        'model' => $model,
        'action_id' => $action_id,
        'final_bc' => $final_bc,
    ]) ?>

    <?= $this->render('_record_details', ['model' => $model]) ?>

</div>
