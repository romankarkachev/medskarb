<?php

use yii\db\Migration;

/**
 * Добавляется поле "Сумма факт" в таблицу расчетов налогов.
 */
class m180103_194820_adding_amount_fact_to_tax_calculations extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tax_calculations', 'amount_fact', $this->decimal(12, 2)->comment('Сумма налога, уплаченная по факту') . ' AFTER `amount`');
    }

    public function safeDown()
    {
        $this->dropColumn('tax_calculations', 'amount_fact');
    }
}
