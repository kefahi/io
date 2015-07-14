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
    'modules' => [
        // Configuration Yii2-User Frontend //
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableFlashMessages' => false,
            'enableRegistration' => false,
            'enableUnconfirmedLogin' => false,
            'confirmWithin' => 21600,
            'cost' => 12,
            'mailer' => [
                'sender'                => 'no-reply@myhost.com', // or ['no-reply@myhost.com' => 'Sender name']
                'welcomeSubject'        => 'Welcome subject',
                'confirmationSubject'   => 'Confirmation subject',
                'reconfirmationSubject' => 'Email change subject',
                'recoverySubject'       => 'Recovery subject',
            ],
        ],
    ],
    'components' => [
        // Configuration Yii2-User Frontend //
        'user' => [
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_backendIdentity',
                'path' => '/admin',
                'httpOnly' => true,
            ],
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
            'errorAction' => 'site/error',
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
