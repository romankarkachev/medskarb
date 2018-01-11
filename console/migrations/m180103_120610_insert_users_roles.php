<?php

use yii\db\Migration;
use dektrium\user\models\User;

/**
 * В систему добавляются роль "Оператор".
 */
class m180103_120610_insert_users_roles extends Migration
{
    /**
     * Функция удаляет все роли пользователя, идентификатор которого передается в параметрах, а также назначает ему
     * новую роль.
     * @param $user_id
     * @param $role
     */
    public function assignNewRole($user_id, $role) {
        Yii::$app->db->createCommand()->delete('auth_assignment', [
            'user_id' => $user_id,
        ])->execute();

        Yii::$app->authManager->assign($role, $user_id);
    }

    public function safeUp()
    {
        $role = Yii::$app->authManager->createRole('operator');
        $role->description = 'Оператор';
        if (Yii::$app->authManager->add($role)) {
            $user = User::findOne(['username' => 'nikolay']);
            if ($user != null) $this->assignNewRole($user->id, $role);

            $user = User::findOne(['username' => 'olga']);
            if ($user != null) $this->assignNewRole($user->id, $role);
        }
    }

    public function safeDown()
    {
        Yii::$app->db->createCommand()->update('auth_assignment', [
            'item_name' => 'root',
        ], [
            'item_name' => 'operator',
        ])->execute();

        $role = Yii::$app->authManager->getRole('operator');
        if ($role != null) Yii::$app->authManager->remove($role);
    }
}
