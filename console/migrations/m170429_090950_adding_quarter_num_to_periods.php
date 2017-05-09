<?php

use yii\db\Migration;

/**
 * Добавляется колонка "Номер квартала".
 */
class m170429_090950_adding_quarter_num_to_periods extends Migration
{
    public function up()
    {
        $this->addColumn('periods', 'quarter_num', $this->integer()->comment('Номер квартала'));
    }

    public function down()
    {
        $this->dropColumn('periods', 'quarter_num');
    }
}
