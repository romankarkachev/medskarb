<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Deals */
/* @var $dpDocumentsUnattached \yii\data\ActiveDataProvider */
/* @var $dpDocumentsRecepit \yii\data\ActiveDataProvider */
/* @var $dpDocumentsExpense \yii\data\ActiveDataProvider */
/* @var $dpDocumentsBrokerRu \yii\data\ActiveDataProvider */
/* @var $dpDocumentsBrokerLnr \yii\data\ActiveDataProvider */
?>

<div class="deals-documents">
    <div class="card">
        <div class="card-header card-header-primary card-header-inverse">Документы, связанные со сделкой</div>
        <div class="card-block">
            <div class="row">
                <div class="col-md-6">
                    <p>Приходные накладные</p>
                    <?php Pjax::begin(['id' => 'pjax-docs_receipt', 'enablePushState' => false, 'timeout' => 5000]); ?>

                    <?= $this->render('_gw_receipts', [
                        'model' => $model,
                        'dpDocumentsRecepit' => $dpDocumentsRecepit,
                    ]) ?>

                    <?php Pjax::end(); ?>

                </div>
                <div class="col-md-6">
                    <p>Расходные накладные</p>
                    <?php Pjax::begin(['id' => 'pjax-docs_expense', 'enablePushState' => false, 'timeout' => 5000]); ?>

                    <?= $this->render('_gw_expenses', [
                        'model' => $model,
                        'dpDocumentsExpense' => $dpDocumentsExpense,
                    ]) ?>

                    <?php Pjax::end(); ?>

                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>Акты брокера РФ</p>
                    <?php Pjax::begin(['id' => 'pjax-docs_broker_ru', 'enablePushState' => false, 'timeout' => 5000]); ?>

                    <?= $this->render('_gw_docs_broker_ru', [
                        'model' => $model,
                        'dpDocumentsBrokerRu' => $dpDocumentsBrokerRu,
                    ]) ?>

                    <?php Pjax::end(); ?>

                </div>
                <div class="col-md-6">
                    <p>Акты брокера ЛНР</p>
                    <?php Pjax::begin(['id' => 'pjax-docs_broker_lnr', 'enablePushState' => false, 'timeout' => 5000]); ?>

                    <?= $this->render('_gw_docs_broker_lnr', [
                        'model' => $model,
                        'dpDocumentsBrokerLnr' => $dpDocumentsBrokerLnr,
                    ]) ?>

                    <?php Pjax::end(); ?>

                </div>
            </div>
            <div class="deals-add_document">
                <?php Pjax::begin(['id' => 'pjax-docs_unattached', 'enablePushState' => false, 'timeout' => 5000]); ?>

                <h5 class="font-weight-bold mb-3">
                    Не привязанные документы
                    <?= Html::button('<i class="fa fa-plus"></i> Добавить документы в сделку', [
                        'id' => 'btnAddDocsToDeal',
                        'class' => 'btn btn-outline-success pull-right',
                        'data-id' => $model->id,
                        'data-loading-text' => 'Подождите...',
                        // чтобы работало без перезагрузки страницы, необходимо добавить этот параметр
                        // умышленно ломаю (мне нужна перезагрузка страницы):
                        //'data-method' => 'post',
                        // и тут должно быть true
                        'data-pjax' => '0',
                    ]) ?>

                    <?= Html::button('<i class="fa fa-refresh"></i>', [
                        'id' => 'btnReloadUnattached',
                        'class' => 'btn btn-outline-info pull-right mr-1',
                        'data-method' => 'post',
                        'data-pjax' => true,
                    ]) ?>

                </h5>

                <?= $this->render('_gw_unattached_documents', [
                    'dpDocumentsUnattached' => $dpDocumentsUnattached
                ]) ?>

                <?php Pjax::end(); ?>

            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
$("input").iCheck({
    checkboxClass: 'icheckbox_square-green',
});
JS
, \yii\web\View::POS_READY);
?>
