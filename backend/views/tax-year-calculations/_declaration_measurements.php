<?php

/* @var $this yii\web\View */
/* @var $model common\models\TaxYearCalculations */

// итоговые показатели
$totalRow = [
    'bsKtCrude' => 0, 'bsKtExclude' => 0, 'bsKtInclude' => 0,
    'bsDtCrude' => 0, 'bsDtExclude' => 0, 'bsDtInclude' => 0,
];
?>
<?php if ($model->tdm != null): ?>
<div class="form-group">
    <h3>Данные для декларации</h3>
</div>
<div class="form-group">
                <table class="table table-sm table-responsive text-center">
                    <thead>
                    <tr>
                        <th>Период</th>
                        <th class="text-center">Доходы</th>
                        <th class="text-center">Расходы</th>
                        <th class="text-center">База</th>
                        <th class="text-center">Налог</th>
                        <th class="text-center">Налог за кв.</th>
                        <th class="text-center">Оплачено</th>
                    </tr>
                    </thead>
                    <tbody>
                <?php foreach (\yii\helpers\Json::decode($model->tdm) as $row): ?>
                    <tr>
                        <td class="text-left">
                            <?= $row['period_name'] ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['kt']) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['dt']) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['base']) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['tax']) ?>
                        </td>
                        <td>
                            <?= Yii::$app->formatter->asDecimal($row['taxQ']) ?>
                        </td>
                        <?php if ($row['q_num'] == 4): ?>
                            <td>
                                <strong><?= Yii::$app->formatter->asDecimal($row['fact'], 2) ?></strong>
                                <br />
                                <small class="font-italic text-muted">доплатить</small>
                            </td>
                        <?php else: ?>
                            <td>
                                <?= Yii::$app->formatter->asDecimal($row['fact'], 2) ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
<?php endif; ?>
