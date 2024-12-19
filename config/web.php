<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name'=>'Lux View',
    'language'=>'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'novikova',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],        
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false, 
        'rules' => [
            'POST registration'=>'users/create',
            'POST authorization'=>'users/login',
            'GET account' => 'users/view',
            'POST rooms/add' => 'rooms/create',
            'GET rooms' => 'rooms/view',
            'PATCH rooms/edit' => 'rooms/edit',
            'GET rooms/search' => 'rooms/search',
            'POST reservations/add' => 'reservations/new',
            'GET reservations' =>'reservations/items',
            'DELETE reservations/del' => 'reservations/delete',
            'POST contract/add' => 'contracts/create',
            'GET contracts' => 'contracts/view',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1','*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1','*'],
    ];
}

return $config;