<?php

use yii\db\Migration;

/**
 * Создается таблица "Файлы к годовым расчетам налога".
 */
class m180211_002422_create_tyc_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Файлы к годовым расчетам налога"';
        };

        $this->createTable('tax_year_calculations_files', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(36)->unique()->comment('GUID'),
            'uploaded_at' => $this->integer()->notNull()->comment('Дата и время загрузки'),
            'uploaded_by' => $this->integer()->notNull()->comment('Автор загрузки'),
            'tyc_id' => $this->integer()->notNull()->comment('Годовой расчет'),
            'ffp' => $this->string(255)->notNull()->comment('Полный путь к файлу'),
            'fn' => $this->string(255)->notNull()->comment('Имя файла'),
            'ofn' => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'size' => $this->integer()->comment('Размер файла'),
        ], $tableOptions);

        $this->createIndex('uploaded_by', 'tax_year_calculations_files', 'uploaded_by');
        $this->createIndex('tyc_id', 'tax_year_calculations_files', 'tyc_id');

        $this->addForeignKey('fk_tax_year_calculations_files_uploaded_by', 'tax_year_calculations_files', 'uploaded_by', 'user', 'id');
        $this->addForeignKey('fk_tax_year_calculations_files_tyc_id', 'tax_year_calculations_files', 'tyc_id', 'tax_year_calculations', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_tax_year_calculations_files_tyc_id', 'tax_year_calculations_files');
        $this->dropForeignKey('fk_tax_year_calculations_files_uploaded_by', 'tax_year_calculations_files');

        $this->dropIndex('tyc_id', 'tax_year_calculations_files');
        $this->dropIndex('uploaded_by', 'tax_year_calculations_files');

        $this->dropTable('tax_year_calculations_files');
    }
}
