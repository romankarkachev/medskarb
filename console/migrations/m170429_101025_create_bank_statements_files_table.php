<?php

use yii\db\Migration;

/**
 * Создается таблица "Изображения чеков".
 */
class m170429_101025_create_bank_statements_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Изображения чеков"';
        };

        $this->createTable('bank_statements_files', [
            'id' => $this->primaryKey(),
            'uploaded_at' => $this->integer()->notNull()->comment('Дата и время загрузки'),
            'uploaded_by' => $this->integer()->notNull()->comment('Автор загрузки'),
            'bs_id' => $this->integer()->notNull()->comment('Банковское движение'),
            'ffp' => $this->string(255)->notNull()->comment('Полный путь к файлу'),
            'fn' => $this->string(255)->notNull()->comment('Имя файла'),
            'ofn' => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'size' => $this->integer()->comment('Размер файла'),
        ], $tableOptions);

        $this->createIndex('bs_id', 'bank_statements_files', 'bs_id');
        $this->createIndex('uploaded_by', 'bank_statements_files', 'uploaded_by');

        $this->addForeignKey('fk_bank_statements_files_bs_id', 'bank_statements_files', 'bs_id', 'bank_statements', 'id');
        $this->addForeignKey('fk_bank_statements_files_uploaded_by', 'bank_statements_files', 'uploaded_by', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_bank_statements_files_uploaded_by', 'bank_statements_files');
        $this->dropForeignKey('fk_bank_statements_files_bs_id', 'bank_statements_files');

        $this->dropIndex('uploaded_by', 'bank_statements_files');
        $this->dropIndex('bs_id', 'bank_statements_files');

        $this->dropTable('bank_statements_files');
    }
}
