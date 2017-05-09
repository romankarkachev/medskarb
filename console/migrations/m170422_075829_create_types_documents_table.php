<?php

use yii\db\Migration;

/**
 * Создается таблица "Типы документов".
 */
class m170422_075829_create_types_documents_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Типы документов"';
        };

        $this->createTable('types_documents', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->notNull()->comment('Наименование'),
        ], $tableOptions);

        $this->insert('types_documents', [
            'name' => 'Договор',
        ]);

        $this->insert('types_documents', [
            'name' => 'Приходная накладная',
        ]);

        $this->insert('types_documents', [
            'name' => 'Расходная накладная',
        ]);

        $this->insert('types_documents', [
            'name' => 'Акт работ',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('types_documents');
    }
}
