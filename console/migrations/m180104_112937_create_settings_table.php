<?php

use yii\db\Migration;

/**
 * Создается таблица "Настройки".
 */
class m180104_112937_create_settings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Настройки"';
        };

        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'default_buyer_id' => $this->integer()->comment('Основной покупатель'),
            'default_broker_ru' => $this->integer()->comment('Основной брокер РФ'),
            'default_broker_lnr' => $this->integer()->comment('Основной брокер ЛНР'),
        ], $tableOptions);

        $this->createIndex('default_buyer_id', 'settings', 'default_buyer_id');
        $this->createIndex('default_broker_ru', 'settings', 'default_broker_ru');
        $this->createIndex('default_broker_lnr', 'settings', 'default_broker_lnr');

        $this->addForeignKey('fk_settings_default_buyer_id', 'settings', 'default_buyer_id', 'counteragents', 'id');
        $this->addForeignKey('fk_settings_default_broker_ru', 'settings', 'default_broker_ru', 'counteragents', 'id');
        $this->addForeignKey('fk_settings_default_broker_lnr', 'settings', 'default_broker_lnr', 'counteragents', 'id');

        $this->insert('settings', [
            'id' => 1,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_settings_default_broker_lnr', 'settings');
        $this->dropForeignKey('fk_settings_default_broker_ru', 'settings');
        $this->dropForeignKey('fk_settings_default_buyer_id', 'settings');

        $this->dropIndex('default_broker_lnr', 'settings');
        $this->dropIndex('default_broker_ru', 'settings');
        $this->dropIndex('default_buyer_id', 'settings');

        $this->dropTable('settings');
    }
}
