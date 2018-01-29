<?php

use yii\db\Migration;

/**
 * Добавляется поле "GUID" в таблицы, в которых хранится информация о прикрепленных файлах.
 */
class m180129_201703_adding_guid_to_files extends Migration
{
    public function safeUp()
    {
        $this->addColumn('bank_statements_files', 'guid', $this->string(36)->unique()->comment('GUID') . ' AFTER `id`');

        $this->addColumn('counteragents_files', 'guid', $this->string(36)->unique()->comment('GUID') . ' AFTER `id`');

        $this->addColumn('deals_files', 'guid', $this->string(36)->unique()->comment('GUID') . ' AFTER `id`');

        $this->addColumn('documents_files', 'guid', $this->string(36)->unique()->comment('GUID') . ' AFTER `id`');

        // проставим guid существующим уже в системе файлам
        // банковские движения
        foreach (\common\models\BankStatementsFiles::find()->all() as $file) {
            $file->guid = \backend\components\GuidGenerator::GUIDv4();
            try {
                $file->save(false);
            }
            catch (\Exception $exception) {}
        }

        // контрагенты
        foreach (\common\models\CounteragentsFiles::find()->all() as $file) {
            $file->guid = \backend\components\GuidGenerator::GUIDv4();
            try {
                $file->save(false);
            }
            catch (\Exception $exception) {}
        }

        // сделки
        foreach (\common\models\DealsFiles::find()->all() as $file) {
            $file->guid = \backend\components\GuidGenerator::GUIDv4();
            try {
                $file->save(false);
            }
            catch (\Exception $exception) {}
        }

        // документы
        foreach (\common\models\DocumentsFiles::find()->all() as $file) {
            $file->guid = \backend\components\GuidGenerator::GUIDv4();
            try {
                $file->save(false);
            }
            catch (\Exception $exception) {}
        }
    }

    public function safeDown()
    {
        $this->dropColumn('bank_statements_files', 'guid');

        $this->dropColumn('counteragents_files', 'guid');

        $this->dropColumn('deals_files', 'guid');

        $this->dropColumn('documents_files', 'guid');
    }
}
