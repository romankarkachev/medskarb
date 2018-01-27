<?php

use yii\db\Migration;

/**
 * Добавляются поля "Налоговая инспекция", "Ставка УСН", "Ставка 300 000".
 */
class m180127_103722_enhancing_settings extends Migration
{
    public function safeUp()
    {
        $this->addColumn('settings', 'tax_inspection_id', $this->integer()->comment('Налоговая инспекция'));
        $this->createIndex('tax_inspection_id', 'settings', 'tax_inspection_id');
        $this->addForeignKey('fk_settings_tax_inspection_id', 'settings', 'tax_inspection_id', 'counteragents', 'id');

        $this->addColumn('settings', 'tax_usn_rate', 'TINYINT(1) COMMENT"Ставка налога при применении УСН"');
        $this->addColumn('settings', 'tax_pf_limit', $this->decimal(12,2)->comment('Сумма превышения для уплаты в ПФ'));
        $this->addColumn('settings', 'tax_pf_rate', 'TINYINT(1) COMMENT"Ставка налога при превышении дохода в 300 000 р."');

        $settings = \common\models\Settings::findOne(1);
        if ($settings != null) {
            $settings->tax_usn_rate = 10;
            $settings->tax_pf_limit = 300000;
            $settings->tax_pf_rate = 1;
            if ($settings->save()) {
                $this->alterColumn('settings', 'tax_usn_rate', 'TINYINT(1) NOT NULL COMMENT"Ставка налога при применении УСН"');
                $this->alterColumn('settings', 'tax_pf_limit', $this->decimal(12,2)->notNull()->comment('Сумма превышения для уплаты в ПФ'));
                $this->alterColumn('settings', 'tax_pf_rate', 'TINYINT(1) NOT NULL COMMENT"Ставка налога при превышении дохода в 300 000 р."');
            }
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_settings_tax_inspection_id', 'settings');
        $this->dropIndex('tax_inspection_id', 'settings');
        $this->dropColumn('settings', 'tax_inspection_id');

        $this->dropColumn('settings', 'tax_usn_rate');

        $this->dropColumn('settings', 'tax_pf_rate');
        $this->dropColumn('settings', 'tax_pf_limit');
    }
}
