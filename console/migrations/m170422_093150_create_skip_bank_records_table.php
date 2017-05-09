<?php

use yii\db\Migration;

/**
 * Создается таблица "Элементы, которые будут пропущены при импорте банковской выписки".
 */
class m170422_093150_create_skip_bank_records_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Элементы, которые будут пропущены при импорте банковской выписки"';
        };

        $this->createTable('skip_bank_records', [
            'id' => $this->primaryKey(),
            'substring' => $this->text()->notNull()->comment('Искомая подстрока'),
        ], $tableOptions);

        $this->insert('skip_bank_records', [
            'substring' => 'вноситель:',
        ]);

        $this->insert('skip_bank_records', [
            'substring' => 'заработная плата',
        ]);

        $this->insert('skip_bank_records', [
            'substring' => 'картой mastercard',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('skip_bank_records');
    }
}
