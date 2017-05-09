<?php

use yii\db\Migration;

/**
 * Создается таблица "Банковские выписки".
 */
class m170423_201800_create_bank_statements_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Банковские выписки (движения по банку)"';
        };

        $this->createTable('bank_statements', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull()->comment('Дата и время создания'),
            'created_by' => $this->integer()->notNull()->comment('Автор создания'),
            'period_id' => $this->integer()->notNull()->comment('Период'),
            'type' => 'TINYINT(1) DEFAULT 0 NOT NULL COMMENT "Тип (0 - авто, 1 - вручную добавлено)"',
            'ca_id' => $this->integer()->comment('Контрагент'),
            'is_active' => 'TINYINT(1) DEFAULT 1 NOT NULL COMMENT "Принимать к расчету"',
            'bank_date' => $this->date()->comment('Дата'),
            'bank_dt' => $this->text()->comment('Дебет'),
            'bank_kt' => $this->text()->comment('Кредит'),
            'bank_amount_dt' => $this->decimal(12, 2)->comment('Сумма Дт'),
            'bank_amount_kt' => $this->decimal(12, 2)->comment('Сумма Кт'),
            'bank_bik_name' => $this->text()->comment('БИК и название банка'),
            'bank_doc_num' => $this->string(20)->comment('Номер платежного поручения'),
            'bank_description' => $this->text()->comment('Назначение платежа'),
            'inn' => $this->string(12)->comment('ИНН'),
        ], $tableOptions);

        $this->createIndex('created_by', 'bank_statements', 'created_by');
        $this->createIndex('period_id', 'bank_statements', 'period_id');
        $this->createIndex('ca_id', 'bank_statements', 'ca_id');

        $this->addForeignKey('fk_bank_statements_created_by', 'bank_statements', 'created_by', 'user', 'id');
        $this->addForeignKey('fk_bank_statements_period_id', 'bank_statements', 'period_id', 'periods', 'id');
        $this->addForeignKey('fk_bank_statements_ca_id', 'bank_statements', 'ca_id', 'counteragents', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_bank_statements_ca_id', 'bank_statements');
        $this->dropForeignKey('fk_bank_statements_period_id', 'bank_statements');
        $this->dropForeignKey('fk_bank_statements_created_by', 'bank_statements');

        $this->dropIndex('ca_id', 'bank_statements');
        $this->dropIndex('period_id', 'bank_statements');
        $this->dropIndex('created_by', 'bank_statements');

        $this->dropTable('bank_statements');
    }
}
