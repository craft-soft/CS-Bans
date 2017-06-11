<?php
/**
 * Конфигурация приложения
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

/**
 * Класс-заглушка, чтобы нормально использовать переменную $config из старого AmxBans
 */
class conf
{
	public $db_host = null;
	public $db_user = null;
	public $db_pass = null;
	public $db_db = null;
	public $db_prefix = null;
	public $robo_login = null;
	public $robo_pass1 = null;
	public $robo_pass2 = null;
	public $robo_testing = FALSE;
	public $code = NULL;
}
$config = new conf;

$basePath = dirname(dirname(__DIR__));

// Подключаем конфиг старого AmxBans
require_once $basePath . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'db.config.inc.php';

// Подключаем bootstrap
Yii::setPathOfAlias(
    'bootstrap',
    realpath(
        dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'extensions'
            . DIRECTORY_SEPARATOR
            . 'bootstrap'
    )
);

$dirs = scandir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'modules');

$modules = array();
foreach ($dirs as $name){
	if ($name[0] != '.') {
		$modules[$name] = array('class'=>'application.modules.' . $name . '.' . ucfirst($name) . 'Module');
	}
}

// Главные параметры приложения
return array(
	'basePath'=>$basePath . DIRECTORY_SEPARATOR . 'protected',
	'name'=>'СS:Bans 1.4',
	'sourceLanguage' => 'ru',
	'language'=>'ru',
    'version' => '1.4.0',

	// Предзагружаемые компоненты
	'preload'=>array(
		'log',
		'DConfig',
		'Ip2Country'
		),
	// Автозагружаемые модели и компоненты
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'ext.editable.*'
	),
	
	'modules'=>array_replace($modules, array(
		
	)),
    
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
	// Компоненты приложения
	'components'=>array(
        'prefs' => array(
            'class' => 'application.components.Prefs'
        ),
		// Бутстрап
		'bootstrap'=>array(
			'class'=>'bootstrap.components.Bootstrap',
		),
		// Компонент пользователей
		'user'=>array(
			// Аутентификация по куки
			'allowAutoLogin'=>true,
		),
		// Конфиг (из таблицы {{webconfig}})
		'config'=>array(
			'class' => 'DConfig'
		),
		'IpToCountry'=>array(
			'class' => 'Ip2Country'
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
		
		'format'=>array(
			'booleanFormat'=>array('Нет', 'Да'),
			'datetimeFormat'=>'d.m.Y H:i',
		),

		// Подключение к БД
		'db'=>array(
			'connectionString' => 'mysql:host='.$config->db_host.';dbname='.$config->db_db,
			'emulatePrepare' => true,
			'username' => $config->db_user,
			'password' => $config->db_pass,
			'charset' => 'utf8',
			'tablePrefix'=>$config->db_prefix.'_',
			'autoConnect' => FALSE,
			'schemaCachingDuration' => 1000,
		),
		'cache'=>array(
			'class'=>'FileCache'
		),
		// Обработка ошибок
		'errorHandler'=>array(
			'errorAction'=>'site/error',
		),
		// Системный лог
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),

	// Тема (темы лежат в themes)
	'theme'=>'default',

	'homeUrl' => array('/site/index'),

	// Дополнительные параметры (вызываются так: Yii::app()->params['adminEmail'])
	'params'=>array(
		'adminEmail'=>'webmaster@example.com',
		'dbname' => $config->db_db,
		'Version' => '1.4',
	),
);
