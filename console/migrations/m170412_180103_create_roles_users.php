<?php

use yii\db\Migration;

/**
 * Создается роль "Полные права" и три пользователя с этой ролью.
 */
class m170412_180103_create_roles_users extends Migration
{
    public function up()
    {
        $role_root = Yii::$app->authManager->createRole('root');
        $role_root->description = 'Полные права';
        Yii::$app->authManager->add($role_root);

        // пользователь 1
        $user = new \dektrium\user\models\User();
        $user->id = 1;
        $user->username = 'root';
        $user->email = 'root@gmail.com';
        $user->password = '1Qazxsw2';
        $user->confirmed_at = time();
        $user->save();

        $user->profile->name = 'Полные права';
        $user->profile->save();
        Yii::$app->authManager->assign($role_root, $user->id);
        unset($user);

        // пользователь 2
        $user = new \dektrium\user\models\User();
        $user->id = 2;
        $user->username = 'olga';
        $user->email = 'olga@gmail.com';
        $user->password = '123456';
        $user->confirmed_at = time();
        $user->save();

        $user->profile->name = 'Ольга';
        $user->profile->save();
        Yii::$app->authManager->assign($role_root, $user->id);
        unset($user);

        // пользователь 3
        $user = new \dektrium\user\models\User();
        $user->id = 3;
        $user->username = 'nikolay';
        $user->email = 'nikolay@gmail.com';
        $user->password = '123456';
        $user->confirmed_at = time();
        $user->save();

        $user->profile->name = 'Николай';
        $user->profile->save();
        Yii::$app->authManager->assign($role_root, $user->id);
    }

    public function down()
    {
        $role_root = Yii::$app->authManager->getRole('root');
        Yii::$app->authManager->remove($role_root);

        $user = \dektrium\user\models\User::findOne(['username' => 'nikolay']);
        if ($user != null) $user->delete();

        $user = \dektrium\user\models\User::findOne(['username' => 'olga']);
        if ($user != null) $user->delete();

        $user = \dektrium\user\models\User::findOne(['username' => 'root']);
        if ($user != null) $user->delete();
    }
}
