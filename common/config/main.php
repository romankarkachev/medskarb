<?php
return [
    'name' => 'Медскарб',
    'language' => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['root'],
            'enableRegistration' => false,
            'enableConfirmation' => false,
            'enablePasswordRecovery' => false,
            'enableFlashMessages' => false,
            'modelMap' => [
                'User' => 'common\models\User',
                'UserSearch' => 'common\models\UserSearch',
            ],
            'controllerMap' => [
                'admin' => 'backend\controllers\UsersController',
                'settings' => 'backend\controllers\UsersSettingsController',
                'security' => 'backend\controllers\SecurityController',
            ],
        ],
        'rbac' => 'dektrium\rbac\RbacWebModule',
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',
            'displayTimezone' => 'Europe/Moscow',
            'saveTimezone' => 'Europe/Moscow',
            'autoWidget' => true,
            'ajaxConversion' => true,
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru_RU',
            'defaultTimeZone' => 'Europe/Moscow',
            'timeZone' => 'Europe/Moscow',
            'currencyCode' => 'RUR',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.Y H:i:s',
            'timeFormat' => 'php:H:i:s',
            'thousandSeparator' => ' ',
            'decimalSeparator' => ',',
            'numberFormatterOptions' => [
                NumberFormatter::MIN_FRACTION_DIGITS => 0,
                NumberFormatter::MAX_FRACTION_DIGITS => 2,
                NumberFormatter::DECIMAL_ALWAYS_SHOWN => 0,
            ],
            'nullDisplay' => '',
        ],
    ],
];
