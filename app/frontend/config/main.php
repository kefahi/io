<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [],
    'components' => [
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
            'errorAction' => 'site/error',
        ],
        // Configuration Yii2-User //
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_frontIdentity',
                'path' => '/',
                'httpOnly' => true,
            ],
        ],
        // Configuration Request Frontend [Yii2-User] //
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'baseUrl' => '',
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'YOUR_KEY_HERE',
            'csrfParam' => '_frontendCSRF',
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/',
            ],
        ],
        // Configuration Session Frontend [Yii2-User] //
        'session' => [
            'name' => 'FRONTENDSESSID',
            'cookieParams' => [
                'path' => '/',
            ],
        ], 
    ],
    'params' => $params,
];
