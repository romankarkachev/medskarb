<?php

use yii\db\Migration;

/**
 * Создается таблица "Файлы к документам".
 */
class m170422_080852_create_documents_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Файлы к документам"';
        };

        $this->createTable('documents_files', [
            'id' => $this->primaryKey(),
            'uploaded_at' => $this->integer()->notNull()->comment('Дата и время загрузки'),
            'uploaded_by' => $this->integer()->notNull()->comment('Автор загрузки'),
            'doc_id' => $this->integer()->notNull()->comment('Документ'),
            'ffp' => $this->string(255)->notNull()->comment('Полный путь к файлу'),
            'fn' => $this->string(255)->notNull()->comment('Имя файла'),
            'ofn' => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'size' => $this->integer()->comment('Размер файла'),
        ], $tableOptions);

        $this->createIndex('uploaded_by', 'documents_files', 'uploaded_by');
        $this->createIndex('doc_id', 'documents_files', 'doc_id');

        $this->addForeignKey('fk_documents_files_uploaded_by', 'documents_files', 'uploaded_by', 'user', 'id');
        $this->addForeignKey('fk_documents_files_doc_id', 'documents_files', 'doc_id', 'documents', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_documents_files_doc_id', 'documents_files');
        $this->dropForeignKey('fk_documents_files_uploaded_by', 'documents_files');

        $this->dropIndex('doc_id', 'documents_files');
        $this->dropIndex('uploaded_by', 'documents_files');

        $this->dropTable('documents_files');
    }
}
