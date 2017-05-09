<?php

use yii\db\Migration;

/**
 * Создается таблица "Сделки".
 */
class m170422_222639_create_deals_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Сделки"';
        };

        $this->createTable('deals', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull()->comment('Дата и время создания'),
            'created_by' => $this->integer()->notNull()->comment('Автор создания'),
            'updated_at' => $this->integer()->comment('Дата и время изменения'),
            'updated_by' => $this->integer()->comment('Автор изменений'),
            'deal_date' => $this->date()->comment('Дата'),
            'customer_id' => $this->integer()->comment('Покупатель'),
            'contract_id' => $this->integer()->comment('Договор'),
            'broker_id' => $this->integer()->comment('Брокер'),
            'is_closed' => 'TINYINT(1) COMMENT "Сделка закрыта"',
        ], $tableOptions);

        $this->createIndex('created_by', 'deals', 'created_by');
        $this->createIndex('updated_by', 'deals', 'updated_by');
        $this->createIndex('customer_id', 'deals', 'customer_id');
        $this->createIndex('contract_id', 'deals', 'contract_id');
        $this->createIndex('broker_id', 'deals', 'broker_id');

        $this->addForeignKey('fk_deals_created_by', 'deals', 'created_by', 'user', 'id');
        $this->addForeignKey('fk_deals_updated_by', 'deals', 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_deals_customer_id', 'deals', 'customer_id', 'counteragents', 'id');
        $this->addForeignKey('fk_deals_contract_id', 'deals', 'contract_id', 'documents', 'id');
        $this->addForeignKey('fk_deals_broker_id', 'deals', 'broker_id', 'counteragents', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_deals_broker_id', 'deals');
        $this->dropForeignKey('fk_deals_contract_id', 'deals');
        $this->dropForeignKey('fk_deals_customer_id', 'deals');
        $this->dropForeignKey('fk_deals_updated_by', 'deals');
        $this->dropForeignKey('fk_deals_created_by', 'deals');

        $this->dropIndex('broker_id', 'deals');
        $this->dropIndex('contract_id', 'deals');
        $this->dropIndex('customer_id', 'deals');
        $this->dropIndex('updated_by', 'deals');
        $this->dropIndex('created_by', 'deals');

        $this->dropTable('deals');
    }
}
