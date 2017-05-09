<?php

use yii\db\Migration;

/**
 * Поле "user_id" в таблице "auth_assignment" становится из текстового в числовое, а также добавляется внешний ключ.
 */
class m130524_201442_init extends Migration
{
    public function up()
    {
        $this->alterColumn('auth_assignment', 'user_id', $this->integer()->notNull()->comment('Пользователь'));

        $this->createIndex('user_id', 'auth_assignment', 'user_id');

        $this->addForeignKey('fk_auth_assignment_user_id', 'auth_assignment', 'user_id', 'user', 'id');

        $this->execute('ALTER TABLE `auth_assignment` DROP PRIMARY KEY, ADD PRIMARY KEY (`item_name`, `user_id`) USING BTREE;');
    }

    public function down()
    {
        $this->dropForeignKey('fk_auth_assignment_user_id', 'auth_assignment');

        $this->dropIndex('user_id', 'auth_assignment');

        $this->alterColumn('auth_assignment', 'user_id', $this->string(64)->notNull()->comment(''));

        $this->execute('ALTER TABLE `auth_assignment` DROP PRIMARY KEY, ADD PRIMARY KEY (`item_name`) USING BTREE;');
    }
}
