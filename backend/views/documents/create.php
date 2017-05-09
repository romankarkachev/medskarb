<?php

/* @var $this yii\web\View */
/* @var $model common\models\Documents */
/* @var $action_id string|null */
/* @var $final_bc string */

$this->title = 'Новый документ | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['/documents']];
if ($action_id != null) $this->params['breadcrumbs'][] = ['label' => $final_bc, 'url' => ['/documents/' . $action_id]];
$this->params['breadcrumbs'][] = 'Новый *';
?>
<div class="documents-create">
    <?= $this->render('_form', [
        'model' => $model,
        'action_id' => $action_id,
        'final_bc' => $final_bc,
    ]) ?>

</div>
