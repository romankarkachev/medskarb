<?php

/* @var $this yii\web\View */
/* @var $model common\models\TaxYearCalculations */

// итоговые показатели
$totalRow = [
    'bsKtCrude' => 0, 'bsKtExclude' => 0, 'bsKt' => 0,
    'bsDtCrude' => 0, 'bsDtExclude' => 0, 'bsDt' => 0,
];
?>
<?php if ($model->calculation_details != null): ?>
<div class="form-group">
    <h3>Исходные данные</h3>
</div>
<div class="form-group">
                <table class="table table-sm table-responsive text-center">
                    <thead>
                    <tr>
                        <th>Период</th>
                        <th class="text-center">Всего доходы</th>
                        <th class="text-center">Не отн. к дох.</th>
                        <th class="text-center">Дох., прин. к расч.</th>
                        <th class="text-center">Всего расходы</th>
                        <th class="text-center">Не отн. к расх.</th>
                        <th class="text-center">Расх., прин. к расч.</th>
                    </tr>
                    </thead>
                    <tbody>
                <?php foreach (\yii\helpers\Json::decode($model->calculation_details) as $row): ?>
                    <tr>
                        <td class="text-left">
                            <?= $row['period_name'] ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['bsKtCrude'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['bsKtExclude'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['bsKt'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['bsDtCrude'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['bsDtExclude'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['bsDt'], 2) ?>
                        </td>
                    </tr>
                <?php
                    $totalRow['bsKtCrude'] += $row['bsKtCrude'];
                    $totalRow['bsKtExclude'] += $row['bsKtExclude'];
                    $totalRow['bsKt'] += $row['bsKt'];
                    $totalRow['bsDtCrude'] += $row['bsDtCrude'];
                    $totalRow['bsDtExclude'] += $row['bsDtExclude'];
                    $totalRow['bsDt'] += $row['bsDt'];
                ?>
                <?php endforeach; ?>
                    <tr class="font-weight-bold table-active">
                        <td class="text-right">Итого:</td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($totalRow['bsKtCrude'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($totalRow['bsKtExclude'], 2) ?>
                        </td>
                        <td class="font-weight-bold text-success">
                            <?= Yii::$app->formatter->asDecimal($totalRow['bsKt'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($totalRow['bsDtCrude'], 2) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($totalRow['bsDtExclude'], 2) ?>
                        </td>
                        <td class="font-weight-bold text-danger">
                            <?= Yii::$app->formatter->asDecimal($totalRow['bsDt'], 2) ?>
                        </td>
                    </tr>
                </table>
            </div>
<?php endif; ?>
