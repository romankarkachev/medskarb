<?php

use yii\db\Migration;

/**
 * Удаляется колонка "Минимальный налог" из таблицы "Квартальные расчеты налога".
 * Добавляются колонки Сумма и Дата фактической оплаты.
 * Сама таблица переименовывается.
 */
class m180127_103710_enhancing_tax_calculations extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('tax_calculations', 'min');

        $this->addColumn('tax_calculations', 'paid_at', $this->date()->comment('Дата оплаты налога') . ' AFTER `amount_fact`');

        $this->addCommentOnTable('tax_calculations', 'Квартальные расчеты налога');
        $this->renameTable('tax_calculations', 'tax_quarter_calculations');
    }

    public function safeDown()
    {
        $this->renameTable('tax_quarter_calculations', 'tax_calculations');
        $this->addCommentOnTable('tax_calculations', 'Расчеты налога');

        $this->dropColumn('tax_calculations', 'paid_at');

        $this->addColumn('tax_calculations', 'min', $this->decimal(12, 2)->comment('Минимальный налог') . ' AFTER `amount`');
    }
}
