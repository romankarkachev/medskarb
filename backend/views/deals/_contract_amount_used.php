<?php

/* @var $this yii\web\View */
/* @var $contract common\models\Documents */
/* @var $form yii\widgets\ActiveForm */

$label_amount_used = 'Использовано средств по договору *';
$amount = 0;
$amountUsed = 0;
$amountRemain = 0;
if (isset($contract)) {
    $amount = $contract->amount;
    $amountUsed = $contract->amountUsed;
    $amountRemain = $amount - $amountUsed;
    if ($contract->amountUsed >= $contract->amount)
        $label_amount_used .= ' <i class="fa fa-exclamation-triangle text-danger" aria-hidden="true" title="Сумма использованных средств достигла или превышает сумму по договору!"></i>';
    elseif ((floatval($contract->amount) - floatval($contract->amountUsed)) <= 200000)
        $label_amount_used .= ' <i class="fa fa-exclamation-circle text-warning" aria-hidden="true" title="Приблизился порог лимита"></i>';
}
?>
<label class="control-label" for="documents-amount_used"><?= $label_amount_used ?></label>
                            <p id="documents-deals">
                                <strong><?= Yii::$app->formatter->asDecimal($amountUsed, 2) ?></strong> из
                                <strong><?= Yii::$app->formatter->asDecimal($amount, 2) ?></strong>
                                <small title="Свободный по договору остаток">(<?= Yii::$app->formatter->asDecimal($amountRemain, 2) ?>)</small>.
                            </p>
