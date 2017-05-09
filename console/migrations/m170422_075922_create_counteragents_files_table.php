<?php

use yii\db\Migration;

/**
 * Создается таблица "Файлы контрагентов".
 */
class m170422_075922_create_counteragents_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Файлы контрагентов"';
        };

        $this->createTable('counteragents_files', [
            'id' => $this->primaryKey(),
            'uploaded_at' => $this->integer()->notNull()->comment('Дата и время загрузки'),
            'uploaded_by' => $this->integer()->notNull()->comment('Автор загрузки'),
            'ca_id' => $this->integer()->notNull()->comment('Контрагент'),
            'ffp' => $this->string(255)->notNull()->comment('Полный путь к файлу'),
            'fn' => $this->string(255)->notNull()->comment('Имя файла'),
            'ofn' => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'size' => $this->integer()->comment('Размер файла'),
        ], $tableOptions);

        $this->createIndex('ca_id', 'counteragents_files', 'ca_id');
        $this->createIndex('uploaded_by', 'counteragents_files', 'uploaded_by');

        $this->addForeignKey('fk_counteragents_files_ca_id', 'counteragents_files', 'ca_id', 'counteragents', 'id');
        $this->addForeignKey('fk_counteragents_files_uploaded_by', 'counteragents_files', 'uploaded_by', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_counteragents_files_uploaded_by', 'counteragents_files');
        $this->dropForeignKey('fk_counteragents_files_ca_id', 'counteragents_files');

        $this->dropIndex('uploaded_by', 'counteragents_files');
        $this->dropIndex('ca_id', 'counteragents_files');

        $this->dropTable('counteragents_files');
    }
}
