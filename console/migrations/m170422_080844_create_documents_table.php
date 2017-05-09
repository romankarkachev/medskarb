<?php

use yii\db\Migration;

/**
 * Создается таблица "Документы".
 */
class m170422_080844_create_documents_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Документы"';
        };

        $this->createTable('documents', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull()->comment('Дата и время создания'),
            'created_by' => $this->integer()->notNull()->comment('Автор создания'),
            'updated_at' => $this->integer()->comment('Дата и время изменения'),
            'updated_by' => $this->integer()->comment('Автор изменений'),
            'ca_id' => $this->integer()->notNull()->comment('Контрагент'),
            'type_id' => $this->integer()->notNull()->comment('Тип документа'),
            'doc_num' => $this->string(30)->notNull()->comment('Номер'),
            'doc_date'=> $this->date()->notNull()->comment('Дата'),
            'amount' => $this->decimal(12, 2)->comment('Сумма'),
            'comment' => $this->text()->comment('Описание'),
        ], $tableOptions);

        $this->createIndex('created_by', 'documents', 'created_by');
        $this->createIndex('updated_by', 'documents', 'updated_by');
        $this->createIndex('ca_id', 'documents', 'ca_id');
        $this->createIndex('type_id', 'documents', 'type_id');

        $this->addForeignKey('fk_documents_created_by', 'documents', 'created_by', 'user', 'id');
        $this->addForeignKey('fk_documents_updated_by', 'documents', 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_documents_ca_id', 'documents', 'ca_id', 'counteragents', 'id');
        $this->addForeignKey('fk_documents_type_id', 'documents', 'type_id', 'types_documents', 'id');

        // для таблицы контрагентов
        $this->addForeignKey('fk_counteragents_contract_id', 'counteragents', 'contract_id', 'documents', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // из таблицы контрагентов
        $this->dropForeignKey('fk_counteragents_contract_id', 'counteragents');

        $this->dropForeignKey('fk_documents_type_id', 'documents');
        $this->dropForeignKey('fk_documents_ca_id', 'documents');
        $this->dropForeignKey('fk_documents_updated_by', 'documents');
        $this->dropForeignKey('fk_documents_created_by', 'documents');

        $this->dropIndex('type_id', 'documents');
        $this->dropIndex('ca_id', 'documents');
        $this->dropIndex('updated_by', 'documents');
        $this->dropIndex('created_by', 'documents');

        $this->dropTable('documents');
    }
}
