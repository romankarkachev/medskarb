<?php

use yii\db\Migration;

/**
 * Создается таблица "Расчеты налога".
 */
class m170423_201811_create_tax_calculations_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Расчеты налога"';
        };

        $this->createTable('tax_calculations', [
            'id' => $this->primaryKey(),
            'calculated_at' => $this->integer()->notNull()->comment('Дата и время расчета'),
            'calculated_by' => $this->integer()->notNull()->comment('Автор расчета'),
            'period_id' => $this->integer()->notNull()->comment('Период'),
            'dt' => $this->decimal(12, 2)->notNull()->comment('Расходы'),
            'kt' => $this->decimal(12, 2)->notNull()->comment('Доходы'),
            'diff' => $this->decimal(12, 2)->notNull()->comment('Разница'),
            'rate' => $this->decimal(12, 2)->notNull()->comment('Ставка налога'),
            'amount' => $this->decimal(12, 2)->notNull()->comment('Сумма налога'),
            'min' => $this->decimal(12, 2)->notNull()->comment('Минимальный налог'),
            'comment' => $this->text()->comment('Примечание'),
        ], $tableOptions);

        $this->createIndex('calculated_by', 'tax_calculations', 'calculated_by');
        $this->createIndex('period_id', 'tax_calculations', 'period_id');

        $this->addForeignKey('fk_tax_calculations_calculated_by', 'tax_calculations', 'calculated_by', 'user', 'id');
        $this->addForeignKey('fk_tax_calculations_period_id', 'tax_calculations', 'period_id', 'periods', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_tax_calculations_period_id', 'tax_calculations');
        $this->dropForeignKey('fk_tax_calculations_calculated_by', 'tax_calculations');

        $this->dropIndex('period_id', 'tax_calculations');
        $this->dropIndex('calculated_by', 'tax_calculations');

        $this->dropTable('tax_calculations');
    }
}
