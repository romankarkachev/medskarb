<?php

use yii\db\Migration;
use common\models\TypesCounteragents;

/**
 * Создается таблица "Типы контрагентов".
 */
class m170422_075826_create_types_counteragents_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT "Типы контрагентов"';
        };

        $this->createTable('types_counteragents', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->notNull()->comment('Наименование'),
        ], $tableOptions);

        $this->insert('types_counteragents', [
            'id' => TypesCounteragents::COUNTERAGENT_TYPE_ПОСТАВЩИК,
            'name' => 'Поставщик',
        ]);

        $this->insert('types_counteragents', [
            'id' => TypesCounteragents::COUNTERAGENT_TYPE_ПОКУПАТЕЛЬ,
            'name' => 'Покупатель',
        ]);

        $this->insert('types_counteragents', [
            'id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_РФ,
            'name' => 'Брокер РФ',
        ]);

        $this->insert('types_counteragents', [
            'id' => TypesCounteragents::COUNTERAGENT_TYPE_БРОКЕР_ЛНР,
            'name' => 'Брокер ЛНР',
        ]);

        $this->insert('types_counteragents', [
            'id' => TypesCounteragents::COUNTERAGENT_TYPE_ПЕРЕВОЗЧИК,
            'name' => 'Перевозчик',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('types_counteragents');
    }
}
