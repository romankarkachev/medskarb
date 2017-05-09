<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\TypesCounteragents;
use common\models\Counteragents;

/* @var $this yii\web\View */
/* @var $model \common\models\Counteragents */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $details array */
?>
<div class="card">
    <div class="card-block">
        <h3 class="card-title"><?= Counteragents::api_uppercaseFirstLetters($details['person']['fullName']) ?></h3>

        <?php if (isset($details['closeInfo'])): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Закрыто <?= date('d.m.Y', strtotime($details['closeInfo']['date'])) ?></strong> <?= isset($details['closeInfo']['closeReason']['name']) ? $details['closeInfo']['closeReason']['name'] : '' ?>

            </div>
        <?php endif; ?>

        <p class="card-text">
            <strong>ИНН</strong> <?= $details['person']['inn'] ?>
            <strong>ОГРН</strong> <?= $details['ogrn'] ?>
            <strong>Дата регистрации</strong>: <?= date('d.m.Y', strtotime($details['ogrnDate'])) ?>

        </p>
        <?php if (isset($details['email'])): ?>
        <p class="card-text"><strong>E-mail</strong>: <?= strtolower($details['email']) ?></p>
        <?php endif; ?>

        <?php if (isset($details['fns'])): ?>
        <p class="card-text">
            <strong>ФНС</strong>: <?= $details['fns']['name'] ?>
            <?php if (isset($details['fns']['address'])): ?>
            , <strong>адрес</strong>: <?= $details['fns']['address'] ?>
            <?php endif; ?>

        </p>
        <?php endif; ?>

        <?php if (isset($details['pfrRegistration'])): ?>
        <p class="card-text">
            <strong>Номер ПФ</strong>: <?= $details['pfrRegistration']['number'] ?>,
            дата регистрации: <?= date('d.m.Y', strtotime($details['pfrRegistration']['registrationDate'])) ?>,
            орган: <?= $details['pfrRegistration']['pfr']['name'] ?>

        </p>
        <?php endif; ?>

        <?php if (isset($details['fssRegistration'])): ?>
        <p class="card-text">
            <strong>Номер ФСС</strong>: <?= $details['fssRegistration']['number'] ?>,
            дата регистрации: <?= date('d.m.Y', strtotime($details['fssRegistration']['registrationDate'])) ?>,
            орган: <?= $details['fssRegistration']['fss']['name'] ?>

        </p>
        <?php endif; ?>

        <?php if (isset($details['citizenship'])) if (isset($details['citizenship']['oksm'])): ?>
        <p><strong>Гражданство</strong>: <?= $details['citizenship']['oksm']['name'] ?></p>
        <?php endif; ?>

        <?php if (isset($details['okved2'])): if (count($details['okved2']) > 0): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Код</th>
                    <th>Наименование</th>
                </tr>
            </thead>
            <tbody>
            <?php if (isset($details['mainOkved2'])): ?>
                <tr>
                    <td><strong><?= $details['mainOkved2']['code'] ?></strong></td>
                    <td><strong><?= $details['mainOkved2']['name'] ?></strong></td>
                </tr>
            <?php endif; ?>
        <?php foreach($details['okved2'] as $okved): ?>
                <tr>
                    <td><?= $okved['code'] ?></td>
                    <td><?= $okved['name'] ?></td>
                </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; endif; ?>

        <p class="card-text"><small class="text-muted">Информация по состоянию на <?= date('d.m.Y', strtotime($details['lastUpdateDate'])) ?></small></p>
    </div>
    <?php if ($model != null): $form = ActiveForm::begin(['action' => \yii\helpers\Url::to(['/counteragents/create'])]); ?>

    <div class="card-footer text-muted">
        <?= Html::input('hidden', 'redirect', true) ?>

        <?= $form->field($model, 'name')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'name_full')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'inn')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'ogrn')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'address_j')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'email')->hiddenInput()->label(false) ?>

        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'type_id')->widget(Select2::className(), [
                    'data' => TypesCounteragents::arrayMapForSelect2(),
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => '- выберите -'],
                    'hideSearch' => true,
                ]) ?>

            </div>
            <div class="col-md-2">
                <label for="<?= strtolower($model->formName()) ?>-iscreatenewcontract" class="control-label"><?= $model->attributeLabels()['isCreateNewContract'] ?></label>
                <?= $form->field($model, 'isCreateNewContract')->checkbox()->label(false) ?>

            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-plus-circle" aria-hidden="true"></i> Создать', ['class' => 'btn btn-success btn-lg']) ?>

        </div>
    </div>
    <?php ActiveForm::end(); endif; ?>
</div>
<?php
$this->registerJs(<<<JS
$('input').iCheck({
    checkboxClass: 'icheckbox_square-green',
});
JS
, yii\web\View::POS_READY);
?>