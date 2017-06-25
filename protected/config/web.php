<?php

$mainConfig = require __DIR__ . DIRECTORY_SEPARATOR . 'main.php';

$webConfig = [
    'onBeginRequest' => function() use ($basePath) {
        if(!Yii::app()->db->username) {
			Yii::app()->catchAllRequest = array('site/install');
            return;
		}
        // Здаем главную страницу
        try {
            if (Yii::app()->config->start_page !== '/site/index') {
                Yii::app()->homeUrl = array(Yii::app()->config->start_page);
            }
            if (is_dir($basePath . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . Yii::app()->config->design)) {
                Yii::app()->setTheme(Yii::app()->config->design);
            }
        } catch(Exception $e) {
            throw new CHttpException(500, 'Проблемы с базой данных. Похоже отсутствуют необходимые таблицы базы данных');
        }
        Yii::app()->db->autoConnect = true;
    },
    'components' => [
		// Компонент пользователей
		'user'=>array(
			// Аутентификация по куки
			'allowAutoLogin'=>true,
		),
		// ЧПУ
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'urlSuffix'=>'.html',
			'rules'=>array(
				'/'=>'site/index',
				array(
                    'bans/motd',
                    'pattern' => 'motd',
                    'urlSuffix' => '.php'
                ),
				'billing/unban/<id:\d+>' => 'billing/default/unban',
				'billing/<controller:\w+>/<action:\w+>/<id:\d+>' => 'billing/<controller>/<action>',
                'billing/<controller:\w+>/<action:\w+>' => 'billing/<controller>/<action>',
				'billing/<action:\w+>' => 'billing/default/<action>',
                'billing/<controller:\w+>' => 'billing/<controller>/buy',
				
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
    ]
];

if(!YII_DEBUG) {
    $webConfig['components']['errorHandler'] = [
        'errorAction'=>'site/error'
    ];
}    

return CMap::mergeArray($mainConfig, $webConfig);
