<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use kartik\money\MaskMoney;
use common\models\Periods;

/* @var $this yii\web\View */
/* @var $model common\models\TaxCalculations */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="tax-calculations-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'period_id')->widget(Select2::className(), [
                        'data' => Periods::arrayMapForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                        'pluginEvents' => [
                            'change' => "function() { CalculateTaxAmount(); }",
                        ],
                    ]) ?>

                </div>
                <div class="col-md-2">
                    <label>Рассчитать</label>
                    <?= Html::button('<i class="fa fa-refresh"></i> Выполнить', [
                        'id' => 'btnReloadCalculations',
                        'class' => 'btn btn-outline-info',
                    ]) ?>

                </div>
            </div>
            <div class="row" id="block-calculations">
                <?= $this->render('_calculations', ['model' => $model, 'form' => $form]) ?>

            </div>
            <?= $form->field($model, 'comment')->textarea(['rows' => 3, 'placeholder' => 'Введите примечание к расчету']) ?>

        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Расчеты налога', ['/tax-calculations'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

            <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>
            <?php else: ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php endif; ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$url_reload_calculations = Url::to(['/tax-calculations/render-calculations']);
$this->registerJs(<<<JS
function CalculateTaxAmount() {
    var period_id = $("#taxcalculations-period_id").val();
    if (period_id != "") {
        $("#block-calculations").html("<div class=\"col-md-6\"><label class=\control-label\"><i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i> Пожалуйста, подождите...</label></div>");
        $("#block-calculations").load("$url_reload_calculations?period_id=" + period_id);
    }
} // CalculateTaxAmount()

// Обработчик щелчка по кнопке "Добавить документы в сделку".
//
function btnReloadCalculationsOnClick() {
    CalculateTaxAmount();
} // btnReloadCalculationsOnClick()

$(document).on("click", "#btnReloadCalculations", btnReloadCalculationsOnClick);
JS
, yii\web\View::POS_READY);
?>
