<?php

use yii\db\Migration;

/**
 * Добавляется колонка "Год" в таблицу периодов.
 */
class m180103_194528_adding_year_to_periods extends Migration
{
    public function safeUp()
    {
        $this->addColumn('periods', 'year', $this->integer()->comment('Номер года'));
    }

    public function safeDown()
    {
        $this->dropColumn('periods', 'year');
    }
}
