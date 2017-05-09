<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use dektrium\user\models\Profile;
use dektrium\user\models\User as BaseUser;

/**
 *
 */
class User extends BaseUser
{
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            // удаляем привязку роли
            AuthAssignment::deleteAll(['user_id' => $this->id]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Делает выборку пользователей и возвращает в виде массива.
     * Применяется для вывода в виджетах Select2.
     * @return array
     */
    public static function arrayMapForSelect2()
    {
        return ArrayHelper::map(User::find()->select([
            'id' => '`user`.`id`',
            'profileName' => '`profile`.`name`',
            ])
            ->leftJoin('`profile`', '`profile`.`user_id` = `user`.`id`')
            ->orderBy('profile.name')->all(), 'id', 'profileName');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name'])
            ->via('userRoles');
    }
}