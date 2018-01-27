<?php

use yii\db\Migration;

/**
 * Создается таблица "Годовые расчеты налога".
 */
class m180127_183732_create_tax_year_calculations_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Годовые расчеты налога"';
        };

        $this->createTable('tax_year_calculations', [
            'id' => $this->primaryKey(),
            'calculated_at' => $this->integer()->notNull()->comment('Дата и время расчета'),
            'calculated_by' => $this->integer()->notNull()->comment('Автор расчета'),
            'year' => $this->smallInteger()->unsigned()->notNull()->comment('Отчетный год'),
            'kt' => $this->decimal(12, 2)->notNull()->comment('Доходы'),
            'dt' => $this->decimal(12, 2)->notNull()->comment('Расходы'),
            'base' => $this->decimal(12, 2)->notNull()->comment('База налогообложения'),
            'rate' => $this->decimal(12, 2)->notNull()->comment('Ставка налога'),
            'min' => $this->decimal(12, 2)->notNull()->comment('Минимальный налог'),
            'amount' => $this->decimal(12, 2)->notNull()->comment('Сумма налога'),
            'amount_fact' => $this->decimal(12, 2)->notNull()->comment('Сумма фактически оплаченная'),
            'amount_to_pay' => $this->decimal(12, 2)->notNull()->comment('Сумма доплаты'),
            'declared_at' => $this->date()->comment('Дата подачи годовой декларации'),
            'paid_at' => $this->date()->comment('Дата оплаты годового налога'),
            'pf_base' => $this->decimal(12, 2)->notNull()->comment('База налогообложения для уплаты 1% в ПФ'),
            'pf_limit' => $this->decimal(12, 2)->notNull()->comment('Сумма лимита для уплаты 1% в ПФ'),
            'pf_rate' => $this->decimal(12, 2)->notNull()->comment('Ставка налога для уплаты в ПФ (1%)'),
            'pf_amount' => $this->decimal(12, 2)->notNull()->comment('Сумма налога'),
            'pf_paid_at' => $this->date()->comment('Дата оплаты в ПФ'),
            'calculation_details' => $this->text()->comment('Сводная подробная таблица за год в json'),
            'comment' => $this->text()->comment('Произвольный комментарий'),
        ], $tableOptions);

        $this->createIndex('calculated_by', 'tax_year_calculations', 'calculated_by');

        $this->addForeignKey('fk_tax_year_calculations_calculated_by', 'tax_year_calculations', 'calculated_by', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_tax_year_calculations_calculated_by', 'tax_year_calculations');

        $this->dropIndex('calculated_by', 'tax_year_calculations');

        $this->dropTable('tax_year_calculations');
    }
}
