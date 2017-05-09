<?php

use yii\db\Migration;

class m170429_225054_insert_another_to_types_counteragents extends Migration
{
    public function up()
    {
        $this->insert('types_counteragents', [
            'id' => \common\models\TypesCounteragents::COUNTERAGENT_TYPE_ПРОЧЕЕ,
            'name' => 'Прочий',
        ]);
    }

    public function down()
    {
        $this->delete('types_counteragents', [
            'id' => \common\models\TypesCounteragents::COUNTERAGENT_TYPE_ПРОЧЕЕ,
        ]);
    }
}
