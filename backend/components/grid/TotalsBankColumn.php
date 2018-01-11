<?php

/**
 * Выполняет суммирование значения колонки для вывода в подвале таблицы в качестве итога.
 */

namespace backend\components\grid;

use common\models\BankStatements;
use yii\grid\DataColumn;

class TotalsBankColumn extends DataColumn
{
    /**
     * @var int сумма доходов
     */
    private $_totalPlus = 0;

    /**
     * @var int сумма расходов только по банку
     */
    private $_totalMinusBank = 0;

    /**
     * @var int сумма расходов вместе с введенными вручную
     */
    private $_totalMinus = 0;

    /**
     * @param $model \common\models\BankStatements
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($model->bank_amount_dt != null) {
            // расходы
            $this->_totalMinus += $model->bank_amount_dt;
            if ($model->type == BankStatements::TYPE_AUTO) $this->_totalMinusBank += $model->bank_amount_dt;
        }
        if ($model->bank_amount_kt != null) $this->_totalPlus += $model->bank_amount_kt; // доходы

        return parent::getDataCellValue($model, $key, $index);
    }

    protected function renderFooterCellContent()
    {
        $format = ['decimal', 'decimals' => 2];
        return 'Всего расходов: ' .
            '<strong class="text-danger">' . $this->grid->formatter->format($this->_totalMinus, $format) . '</strong>' .
            ' (в т.ч. б/н ' . $this->grid->formatter->format($this->_totalMinusBank, $format) . ')' .
            ', всего доходов: ' .
            '<strong class="text-success">' . $this->grid->formatter->format($this->_totalPlus, $format) . '</strong>.';
    }
}
