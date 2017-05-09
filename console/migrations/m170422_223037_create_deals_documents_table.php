<?php

use yii\db\Migration;

/**
 * Создается таблица "Документы сделок".
 */
class m170422_223037_create_deals_documents_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Документы сделок"';
        };

        $this->createTable('deals_documents', [
            'id' => $this->primaryKey(),
            'deal_id' => $this->integer()->notNull()->comment('Сделка'),
            'doc_id' => $this->integer()->notNull()->comment('Документ'),
        ], $tableOptions);

        $this->createIndex('deal_id', 'deals_documents', 'deal_id');
        $this->createIndex('doc_id', 'deals_documents', 'doc_id');

        $this->addForeignKey('fk_deals_documents_deal_id', 'deals_documents', 'deal_id', 'deals', 'id');
        $this->addForeignKey('fk_deals_documents_doc_id', 'deals_documents', 'doc_id', 'documents', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_deals_documents_doc_id', 'deals_documents');
        $this->dropForeignKey('fk_deals_documents_deal_id', 'deals_documents');

        $this->dropIndex('doc_id', 'deals_documents');
        $this->dropIndex('deal_id', 'deals_documents');

        $this->dropTable('deals_documents');
    }
}
