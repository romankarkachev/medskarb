<?php
/* @var $this yii\web\View */
/* @var $model common\models\TaxCalculations */
?>
<div id="mw_td" class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Подробности</h4>
                <small class="form-text">ID: <?= $model->id ?></small>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label font-weight-bold">Рассчитано</label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= Yii::$app->formatter->asDate($model->calculated_at, 'php:d.m.Y в H:i') ?></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label font-weight-bold"><?= $model->attributeLabels()['calculated_by'] ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= $model->getCalculatedByName() ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
