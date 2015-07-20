<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => [
        // Configuration Yii2-User Backend //
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableFlashMessages' => false,
            'enableRegistration' => true,
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin'],
            'mailer' => [
                'sender'                => 'no-reply@myhost.com', // or ['no-reply@myhost.com' => 'Sender name']
                'welcomeSubject'        => 'Welcome subject',
                'confirmationSubject'   => 'Confirmation subject',
                'reconfirmationSubject' => 'Email change subject',
                'recoverySubject'       => 'Recovery subject',
            ],
            'modelMap' => [
		        'User' => 'common\models\User',
		    ],
        ],
        'rbac' => [
            'class' => 'dektrium\rbac\Module',
        ],
        'markdown' => [
	        // the module class
	        'class' => 'kartik\markdown\Module',
	        // the controller action route used for markdown editor preview
	        'previewAction' => '/markdown/parse/preview',
	        // the list of custom conversion patterns for post processing
	        'customConversion' => [
	            '<table>' => '<table class="table table-bordered table-striped">'
	        ],
	        // whether to use PHP SmartyPantsTypographer to process Markdown output
	        'smartyPants' => true
	    ]
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=io',
            'username' => 'io',
            'password' => 'io',
            'charset' => 'utf8',
        ],
        'mailer' => [                                                 // To be replaced with this \
            'class' => 'yii\swiftmailer\Mailer',                      //                           |
            'viewPath' => '@common/mail',                             //                           |
            // send all mails to a file by default. You have to set   //                           |
            // 'useFileTransport' to false and configure a transport  //                           |
            // for the mailer to send real emails.                    //                           |
            'useFileTransport' => true,                               //                           |
        ],                                                            //                           |
        /*                                                            //                           |
        'mailer' => [                                                 //  //_______________________|
            'class' => 'yii\swiftmailer\Mailer',                      //  \\
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'your-host-domain e.g. smtp.gmail.com',
                'username' => 'your-email-or-username',
                'password' => 'your-password',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        */
        //'redis' => require(__DIR__ . '/redis.php'),
        'urlManager' => [
            'showScriptName' => true,
            'enablePrettyUrl' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'ruleTable' => 'auth_rule',
            'itemTable' => 'auth_item',
            'itemChildTable' => 'auth_item_child',
            'assignmentTable' => 'auth_assignment',
            'defaultRoles' => ['guest'],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            /**
             * AuthClient support all the following third-party services (clients), uncomment the ones
             * that you wish to support, details for each can be found in the AuthClient Guide: 
             * http://www.yiiframework.com/doc-2.0/guide-security-auth-clients.html
            **/
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\GoogleOpenId'
                ],
                'twitter' => [
                    'class' => 'yii\authclient\clients\Twitter',
                    'consumerKey' => 'twitter_consumer_key',
                    'consumerSecret' => 'twitter_consumer_secret',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => 'facebook_client_id',
                    'clientSecret' => 'facebook_client_secret',
                ],
                'github' => [
                    'class' => 'yii\authclient\clients\GitHub',
                    'clientId' => 'github_client_id',
                    'clientSecret' => 'github_client_secret',
                ],
                'linkedin' => [
                    'class' => 'yii\authclient\clients\LinkedIn',
                    'clientId' => 'linkedin_client_id',
                    'clientSecret' => 'linkedin_client_secret',
                ],
                'live' => [
                    'class' => 'yii\authclient\clients\Live',
                    'clientId' => 'live_client_id',
                    'clientSecret' => 'live_client_secret',
                ],
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => 'vkontakte_client_id',
                    'clientSecret' => 'vkontakte_client_secret',
                ],
                'yandex' => [
                    'class' => 'yii\authclient\clients\YandexOpenId'
                ],
            ],
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@app/views/user'
                ],
            ],
        ],
    ],
];
