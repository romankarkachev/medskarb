<?php

/* @var $this yii\web\View */
/* @var $model common\models\Counteragents */

$requisites = '';
if ($model->inn != null && $model->inn != '') $requisites .= 'ИНН <strong>' . $model->inn . '</strong>';
if ($model->kpp != null && $model->kpp != '') $requisites .= ' КПП <strong>' . $model->kpp . '</strong>';
$requisites = trim($requisites);
if ($model->ogrn != null && $model->ogrn != '') $requisites .= ' ОГРН <strong>' . $model->ogrn . '</strong>';
$requisites = trim($requisites);

$bank_account = '';
if ($model->bank_an != null && $model->bank_an != '')
    $bank_account = 'Р/с <strong>' . $model->bank_an . '</strong> БИК <strong>' . $model->bank_bik . '</strong><br/>'
        . $model->bank_name . ' корр. счет <strong>' . $model->bank_ca . '</strong>';
?>
<div class="counteragents-summary_card">
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold"><?= $model->attributeLabels()['name_full'] ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $model->name_full ?></p>
        </div>
    </div>
    <?php if ($requisites != null && $requisites != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold">Реквизиты</label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $requisites ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($bank_account != null && $bank_account != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold">Банковский счет</label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $bank_account ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($model->contact_person != null && $model->contact_person != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold"><?= $model->attributeLabels()['contact_person'] ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $model->contact_person ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($model->phones != null && $model->phones != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold"><?= $model->attributeLabels()['phones'] ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $model->phones ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($model->email != null && $model->email != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold"><?= $model->attributeLabels()['email'] ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $model->email ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($model->address_j != null && $model->address_j != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold"><?= $model->attributeLabels()['address_j'] ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $model->address_j ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($model->address_p != null && $model->address_p != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold"><?= $model->attributeLabels()['address_p'] ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $model->address_p ?></p>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($model->address_m != null && $model->address_m != ''): ?>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label font-weight-bold"><?= $model->attributeLabels()['address_m'] ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><?= $model->address_m ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
