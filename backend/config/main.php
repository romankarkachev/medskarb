<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '',
        ],
        'user' => [
            'loginUrl' => ['/login'],
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'default/error',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@backend/views/user',
                ],
            ],
        ],
        // запрещаем bootstrap на корню, у нас свой (из темы coreui)
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null, 'css' => []
                ],
                'yii\bootstrap\BootstrapThemeAsset' => [
                    'sourcePath' => null, 'css' => []
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'default/index',
                '<action:login>' => '/user/security/login',
                '<action:logout>' => '/user/security/logout',
                '<action:users>' => '/user/admin/index',
                '<controller:users>/<action:create>' => '/user/admin/create',
                '<controller:users>/<action:update>' => '/user/admin/update',
                '<controller:users>/<action:delete>' => '/user/admin/delete',
                '<controller:users>/<action:update-profile>' => '/user/admin/update-profile',
                '<controller:users>/<action:assignments>' => '/user/admin/assignments',
                '<action:profile>' => 'user/settings/<action>',
                '<action:account>' => 'user/settings/<action>',
                '<controller:documents>/<action:create|update|delete|upload-files|download|delete-file>' => '<controller>/<action>',
                '<controller:documents>/<action_id:[\w_\/-]+>' => 'documents/index',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];
