<?php

use yii\db\Migration;

/**
 * Создается таблица "Периоды".
 */
class m170422_093115_create_periods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Периоды"';
        };

        $this->createTable('periods', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('Наименование'),
            'start' => $this->integer()->notNull()->comment('Начало периода'),
            'end' => $this->integer()->notNull()->comment('Конец периода'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('periods');
    }
}
