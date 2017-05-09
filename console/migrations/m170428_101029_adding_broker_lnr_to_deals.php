<?php

use yii\db\Migration;

class m170428_101029_adding_broker_lnr_to_deals extends Migration
{
    public function up()
    {
        $this->addColumn('deals', 'broker_lnr_id', $this->integer()->comment('Брокер ЛНР') . ' AFTER `broker_id`');

        $this->createIndex('broker_lnr_id', 'deals', 'broker_lnr_id');

        $this->addForeignKey('fk_deals_broker_lnr_id', 'deals', 'broker_lnr_id', 'counteragents', 'id');

        $this->addCommentOnColumn('deals', 'broker_id', 'Брокер РФ');

        $this->dropForeignKey('fk_deals_broker_id', 'deals');

        $this->dropIndex('broker_id', 'deals');

        $this->renameColumn('deals', 'broker_id', 'broker_ru_id');

        $this->createIndex('broker_ru_id', 'deals', 'broker_ru_id');

        $this->addForeignKey('fk_deals_broker_ru_id', 'deals', 'broker_ru_id', 'counteragents', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_deals_broker_ru_id', 'deals');

        $this->dropIndex('broker_ru_id', 'deals');

        $this->renameColumn('deals', 'broker_ru_id', 'broker_id');

        $this->createIndex('broker_id', 'deals', 'broker_id');

        $this->addForeignKey('fk_deals_broker_id', 'deals', 'broker_id', 'counteragents', 'id');

        $this->addCommentOnColumn('deals', 'broker_id', 'Брокер');

        $this->dropForeignKey('fk_deals_broker_lnr_id', 'deals');

        $this->dropIndex('broker_lnr_id', 'deals');

        $this->dropColumn('deals', 'broker_lnr_id');
    }
}
