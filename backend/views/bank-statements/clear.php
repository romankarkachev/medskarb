<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\Periods;

/* @var $this yii\web\View */
/* @var $model backend\models\BankStatementsClear */

$this->title = 'Удаление движений по банку за период | '.Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Банковские движения', 'url' => ['/bank-statements']];
$this->params['breadcrumbs'][] = 'Очистка';
?>
<div class="bank-statements-clear">
    <div class="card">
        <div class="card-block">
            <p>Движения, введенные вручную, не удаляются.</p>
            <?php $form = ActiveForm::begin([
                'options' => [
                    'id' => 'frmClear',
                ],
            ]) ?>

            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'period_id')->widget(Select2::className(), [
                        'data' => Periods::arrayMapForSelect2(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => '- выберите -'],
                        'hideSearch' => true,
                    ]) ?>

                </div>
            </div>
            <div class="form-group">
                <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Банковские движения', ['/bank-statements'], ['class' => 'btn btn-lg', 'title' => 'Вернуться в список. Изменения не будут сохранены']) ?>

                <?= Html::submitButton('<i class="fa fa-trash-o" aria-hidden="true"></i> Удалить', ['class' => 'btn btn-danger btn-lg']) ?>

            </div>
            <?php ActiveForm::end() ?>

        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
// Функция перехватывает отправку формы на сервер и просит пользователя подтвердить ее.
//
function formSubmit() {
    return confirm("Вы действительно хотите удалить все банковские движения?");
} // formSubmit()

$(document).on("submit", "#frmClear", formSubmit);
JS
, \yii\web\View::POS_READY);
?>