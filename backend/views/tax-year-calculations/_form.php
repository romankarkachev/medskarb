<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\TaxYearCalculations */
/* @var $form yii\bootstrap\ActiveForm */

$formNameId = strtolower($model->formName());
?>

<div class="tax-year-calculations-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-auto">
                    <?= $form->field($model, 'year')->widget(MaskedInput::className(), [
                        'clientOptions' => ['alias' =>  'numeric'],
                    ])->textInput([
                        'maxlength' => true,
                        'placeholder' => '0',
                    ]) ?>

                </div>
            </div>
            <div class="form-group" id="block-calculations">
                <?= $this->render('_calculations', ['model' => $model, 'form' => $form]) ?>

            </div>
            <?= $form->field($model, 'comment')->textarea(['rows' => 3, 'placeholder' => 'Введите примечание к расчету']) ?>

            <p class="text-muted">Суммы доходов, расходов, базы и налога в декларации представлены нарастающим итогом.</p>
            <?php if (!$model->isNewRecord): ?>
            <p class="text-muted font-italic">Расчет выполнен <?= Yii::$app->formatter->asDate($model->calculated_at, 'php:d F Y в H:i') ?> пользователем <?= $model->calculatedByProfileName ?>.</p>
            <?php endif; ?>
        </div>
        <div class="card-footer text-muted">
            <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Годовые расчеты', ['/tax-year-calculations'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

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

$url_reload_calculations = Url::to(['/tax-year-calculations/render-calculations']);
$this->registerJs(<<<JS
function CalculateTaxAmount() {
    var year = $("#$formNameId-year").val();
    if (year != "") {
        $("#block-calculations").html("<label class=\control-label\"><i class=\"fa fa-spinner fa-pulse fa-fw text-primary\"></i> Пожалуйста, подождите...</label>");
        $("#block-calculations").load("$url_reload_calculations?year=" + year);
    }
} // CalculateTaxAmount()

// Обработчик щелчка по кнопке "Выполнить расчет налогов".
//
function btnReloadCalculationsOnClick() {
    CalculateTaxAmount();
} // btnReloadCalculationsOnClick()

$(document).on("click", "#btnReloadCalculations", btnReloadCalculationsOnClick);
JS
, yii\web\View::POS_READY);
?>
