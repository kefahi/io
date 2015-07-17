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
    'modules' => [
        // Configuration Yii2-User Backend //
        // Disable registration for Backend
        'user' => [
            'enableRegistration' => false,
        ],
    ],
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
        // Configuration Yii2-User //
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_backendIdentity',
                'path' => '/admin',
                'httpOnly' => true,
            ],
        ],
        // Configuration Request Backend [Yii2-User] //
        'request' => [
            // Overrride baseUrl and csrfParam parameters for backend website and enabling CSRF Validation
            'baseUrl' => '/admin',
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'YOUR_KEY_HERE',
            'csrfParam' => '_backendCSRF',
            'enableCsrfValidation' => true,
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/admin',
            ],
        ],
        // Configuration Session Backend [Yii2-User] //
        'session' => [
            'name' => 'BACKENDSESSID',
            'cookieParams' => [
                'httpOnly' => true,
                'path' => '/admin',
            ],
        ], 
    ],
    'params' => $params,
];
