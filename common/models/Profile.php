<?php

namespace common\models;

use dektrium\user\models\Profile as BaseProfile;

/**
 * Модель для таблицы "profile".
 *
 * @property integer $user_id
 * @property string $name
 * @property string $timezone
 *
 * @property User $user
 */

class Profile extends BaseProfile
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'name'], 'required'],
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['timezone'], 'string', 'max' => 40],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
       ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'timezone' => 'Часовой пояс',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
