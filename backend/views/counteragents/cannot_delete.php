<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Counteragents */

$this->title = 'Ошибка удаления контрагента | '.Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Контрагенты', 'url' => ['/counteragents']];
?>
<div class="counteragents-cannot_delete">
    <div class="alert alert-danger" role="alert">
        <h4><i class="fa fa-bolt"></i> Невозможно удалить запись &laquo<?= $model->name ?>&raquo;!</h4>
        <p>Элемент не может быть удален, поскольку используется в других объектах.</p>
        <hr>
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Контрагенты', ['/counteragents'], ['class' => 'btn btn-outline-primary btn-lg', 'title' => 'Вернуться в список']) ?>

    </div>
</div>
