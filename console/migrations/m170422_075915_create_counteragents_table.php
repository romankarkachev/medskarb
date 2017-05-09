<?php

use yii\db\Migration;

/**
 * Создается таблица "Контрагенты".
 */
class m170422_075915_create_counteragents_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Контрагенты"';
        };

        $this->createTable('counteragents', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull()->comment('Дата и время создания'),
            'created_by' => $this->integer()->notNull()->comment('Автор создания'),
            'updated_at' => $this->integer()->comment('Дата и время изменения'),
            'updated_by' => $this->integer()->comment('Автор изменений'),
            'name' => $this->string(100)->notNull()->comment('Наименование'),
            'name_full' => $this->string(200)->notNull()->comment('Полное наименование'),
            'type_id' => $this->integer()->notNull()->comment('Тип контрагента'),
            'inn' => $this->string(12)->comment('ИНН'),
            'kpp' => $this->string(9)->comment('КПП'),
            'ogrn' => $this->string(15)->comment('ОГРН(ИП)'),
            'bank_an' => $this->string(25)->comment('Номер р/с'),
            'bank_bik' => $this->string(10)->comment('БИК банка'),
            'bank_name' => $this->string()->comment('Наименование банка'),
            'bank_ca' => $this->string(25)->comment('Корр. счет'),
            'email' => $this->string()->comment('E-mail'),
            'contact_person' => $this->string(50)->comment('Контактное лицо'),
            'address_j' => $this->text()->comment('Адрес юридический'),
            'address_p' => $this->text()->comment('Адрес фактический'),
            'address_m' => $this->text()->comment('Адрес почтовый'),
            'phones' => $this->string(50)->comment('Телефоны'),
            'contract_id' => $this->integer()->comment('Основной договор'),
            'comment' => $this->text()->comment('Примечания'),
        ], $tableOptions);

        $this->createIndex('created_by', 'counteragents', 'created_by');
        $this->createIndex('updated_by', 'counteragents', 'updated_by');
        $this->createIndex('type_id', 'counteragents', 'type_id');
        $this->createIndex('contract_id', 'counteragents', 'contract_id');

        $this->addForeignKey('fk_counteragents_created_by', 'counteragents', 'created_by', 'user', 'id');
        $this->addForeignKey('fk_counteragents_updated_by', 'counteragents', 'updated_by', 'user', 'id');
        $this->addForeignKey('fk_counteragents_type_id', 'counteragents', 'type_id', 'types_counteragents', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_counteragents_type_id', 'counteragents');
        $this->dropForeignKey('fk_counteragents_updated_by', 'counteragents');
        $this->dropForeignKey('fk_counteragents_created_by', 'counteragents');

        $this->dropIndex('contract_id', 'counteragents');
        $this->dropIndex('type_id', 'counteragents');
        $this->dropIndex('updated_by', 'counteragents');
        $this->dropIndex('created_by', 'counteragents');

        $this->dropTable('counteragents');
    }
}
