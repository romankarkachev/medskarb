<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\Periods;

/* @var $this yii\web\View */
/* @var $model common\models\TaxQuarterCalculations */
/* @var $form yii\bootstrap\ActiveForm */

$formNameId = strtolower($model->formName());
?>

<div class="tax-quarter-calculations-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'period_id')->widget(Select2::className(), [
                        'data' => Periods::arrayMapForSelect2(false, 'quarter_num <> 4'),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                        'pluginEvents' => [
                            'change' => "function() { CalculateTaxAmount(); }",
                        ],
                    ]) ?>

                </div>
            </div>
            <div class="form-group" id="block-calculations">
                <?= $this->render('_calculations', ['model' => $model, 'form' => $form]) ?>

            </div>
            <?= $form->field($model, 'comment')->textarea(['rows' => 3, 'placeholder' => 'Введите примечание к расчету']) ?>

            <?php if (!$model->isNewRecord): ?>
            <p class="text-muted font-italic">Расчет выполнен <?= Yii::$app->formatter->asDate($model->calculated_at, 'php:d F Y в H:i') ?> пользователем <?= $model->calculatedByProfileName ?>.</p>
            <?php endif; ?>
        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Авансовые платежи', ['/tax-quarter-calculations'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

            <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>
            <?php else: ?>
            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php endif; ?>

            <?= Html::button('<i class="fa fa-calculator"></i> Выполнить расчет', [
                'id' => 'btnReloadCalculations',
                'class' => 'btn btn-info btn-lg',
            ]) ?>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs(<<<JS
// Функция-обработчик изменения даты в любом из соответствующих полей.
//
function anyDateOnChange() {
    \$button = $("button[type='submit']");
    \$button.attr("disabled", "disabled");
    text = \$button.html();
    \$button.text("Подождите...");
    setTimeout(function () {
        \$button.removeAttr("disabled");
        \$button.html(text);
    }, 1500);
}
JS
, \yii\web\View::POS_BEGIN);

$url_reload_calculations = Url::to(['/tax-quarter-calculations/render-calculations']);
$this->registerJs(<<<JS
function CalculateTaxAmount() {
    var period_id = $("#$formNameId-period_id").val();
    if (period_id != "") {
        $("#block-calculations").html("<label class=\control-label\"><i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i> Пожалуйста, подождите...</label>");
        $("#block-calculations").load("$url_reload_calculations?period_id=" + period_id);
    }
} // CalculateTaxAmount()

// Обработчик щелчка по кнопке "Выполнить расчет налога".
//
function btnReloadCalculationsOnClick() {
    CalculateTaxAmount();
} // btnReloadCalculationsOnClick()

$(document).on("click", "#btnReloadCalculations", btnReloadCalculationsOnClick);
JS
, yii\web\View::POS_READY);
?>
