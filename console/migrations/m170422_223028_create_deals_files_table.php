<?php

use yii\db\Migration;

/**
 * Создается таблица "Файлы к сделкам".
 */
class m170422_223028_create_deals_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Файлы к сделкам"';
        };

        $this->createTable('deals_files', [
            'id' => $this->primaryKey(),
            'uploaded_at' => $this->integer()->notNull()->comment('Дата и время загрузки'),
            'uploaded_by' => $this->integer()->notNull()->comment('Автор загрузки'),
            'deal_id' => $this->integer()->notNull()->comment('Сделка'),
            'ffp' => $this->string(255)->notNull()->comment('Полный путь к файлу'),
            'fn' => $this->string(255)->notNull()->comment('Имя файла'),
            'ofn' => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'size' => $this->integer()->comment('Размер файла'),
        ], $tableOptions);

        $this->createIndex('uploaded_by', 'deals_files', 'uploaded_by');
        $this->createIndex('deal_id', 'deals_files', 'deal_id');

        $this->addForeignKey('fk_deals_files_uploaded_by', 'deals_files', 'uploaded_by', 'user', 'id');
        $this->addForeignKey('fk_deals_files_deal_id', 'deals_files', 'deal_id', 'deals', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_deals_files_deal_id', 'deals_files');
        $this->dropForeignKey('fk_deals_files_uploaded_by', 'deals_files');

        $this->dropIndex('deal_id', 'deals_files');
        $this->dropIndex('uploaded_by', 'deals_files');

        $this->dropTable('deals_files');
    }
}
