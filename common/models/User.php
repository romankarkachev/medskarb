<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use dektrium\user\models\Profile;
use dektrium\user\models\User as BaseUser;

/**
 * Модель для таблицы пользователей.
 */
class User extends BaseUser
{
    /**
     * @var string ФИО или наименование организации
     */
    public $name;

    /**
     * @var string роль
     */
    public $role_id;

    /**
     * @var string подтверждение пароля
     */
    public $password_confirm;

    /**
     * ФИО пользователя (для вложенного запроса и сортировки).
     * @var string
     */
    public $profileName;

    /**
     * Описание роли пользователя (для вложенного запроса и сортировки).
     * @var string
     */
    public $roleName;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'name' => 'Имя',
            'role_id' => 'Роль',
            'password_confirm' => 'Подтверждение пароля',
            'profileName' => 'Имя',
            'roleName' => 'Роль',
        ]);
    }

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
     * Формирует пользовательское меню в зависимости от роли.
     * Элементы используются в сайдбаре.
     * @return array
     */
    public static function prepareUserSidebarMenu()
    {
        if (Yii::$app->user->can('root'))
            return [
                ['label' => 'Контрагенты', 'icon' => 'fa fa-address-book-o', 'url' => ['/counteragents']],
                ['label' => 'Сделки', 'icon' => 'fa fa-handshake-o', 'url' => ['/deals']],
                ['label' => '<li class="nav-title">Документы</li>'],
                ['label' => 'Прих. накл.', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/receipts']],
                ['label' => 'Расх. накл.', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/expenses']],
                ['label' => 'Акты брокера РФ', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/broker-ru']],
                ['label' => 'Акты брокера ЛНР', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/broker-lnr']],
                ['label' => 'Договоры', 'icon' => 'fa fa-folder-open', 'url' => ['/documents/contracts']],
                ['label' => 'Все', 'icon' => 'fa fa-folder-open', 'url' => ['/documents']],
                ['label' => '<li class="nav-title">Налогообложение</li>'],
                ['label' => 'Календарь', 'icon' => 'fa fa-calendar', 'url' => ['/accountant-calendar']],
                ['label' => 'Банковские движения', 'icon' => 'fa fa-bank', 'url' => ['/bank-statements']],
                ['label' => 'Авансовые платежи', 'icon' => 'fa fa-balance-scale', 'url' => ['/tax-quarter-calculations']],
                ['label' => 'Расчеты налога', 'icon' => 'fa fa-balance-scale', 'url' => ['/tax-year-calculations']],
                [
                    'label' => 'Справочники',
                    'url' => '#',
                    'items' => [
                        ['label' => 'Периоды', 'icon' => 'fa fa-calendar', 'url' => ['/periods']],
                        ['label' => 'Игнор для банка', 'icon' => 'fa fa-bank', 'url' => ['/skip-bank-records']],
                        ['label' => 'Пользователи', 'icon' => 'fa fa-users', 'url' => ['/users']],
                    ],
                ],
                ['label' => 'Настройки', 'icon' => 'fa fa-cogs', 'url' => ['/settings']],
            ];

        if (Yii::$app->user->can('operator'))
            return [
                ['label' => 'Контрагенты', 'icon' => 'fa fa-address-book-o', 'url' => ['/counteragents']],
                ['label' => '<li class="nav-title">Налогообложение</li>'],
                ['label' => 'Банковские движения', 'icon' => 'fa fa-bank', 'url' => ['/bank-statements']],
                ['label' => 'Авансовые платежи', 'icon' => 'fa fa-balance-scale', 'url' => ['/tax-quarter-calculations']],
                ['label' => 'Расчеты налога', 'icon' => 'fa fa-balance-scale', 'url' => ['/tax-year-calculations']],
            ];

        return [];
    }

    /**
     * Возвращает наименование текущего пользователя в виде root (Иван).
     * @return string
     */
    public static function getCurrentUserFullRepresentation()
    {
        $user = Yii::$app->user->identity;
        $username = $user->username;
        if ($user->profile->name != null && trim($user->profile->name) != '')
            $username .= ' (' . trim($user->profile->name) . ')';

        return $username;
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

    /**
     * Возвращает наименование роли пользователя.
     * @return string
     */
    public function getRoleName()
    {
        return $this->role != null ? $this->role->name : '';
    }

    /**
     * Возвращает описание роли пользователя.
     * @return string
     */
    public function getRoleDescription()
    {
        return $this->role != null ? $this->role->description : '';
    }
}
