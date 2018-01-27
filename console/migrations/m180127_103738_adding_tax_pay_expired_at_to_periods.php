<?php

use yii\db\Migration;

/**
 * Добавляется поле "Крайний срок оплаты налога за квартал".
 */
class m180127_103738_adding_tax_pay_expired_at_to_periods extends Migration
{
    public function safeUp()
    {
        $this->addColumn('periods', 'tax_pay_expired_at', $this->date()->comment('Крайний срок оплаты налога за квартал'));
    }

    public function safeDown()
    {
        $this->dropColumn('periods', 'tax_pay_expired_at');
    }
}
