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

$ds = DIRECTORY_SEPARATOR;

if(YII_DEBUG) {
    $fileName = 'db.config.inc.local.php';
} else {
    $fileName = 'db.config.inc.php';
}

// Подключаем конфиг старого AmxBans
require_once "{$basePath}{$ds}include{$ds}{$fileName}";

// Подключаем bootstrap
Yii::setPathOfAlias('bootstrap', realpath(dirname(__FILE__) . "{$ds}..{$ds}extensions{$ds}bootstrap"));

$dirs = scandir(dirname(__FILE__) . "{$ds}..{$ds}modules");

$modules = array();
foreach ($dirs as $name){
	if ($name[0] != '.') {
		$modules[$name] = array('class'=>'application.modules.' . $name . '.' . ucfirst($name) . 'Module');
	}
}

// Главные параметры приложения
return array(
	'basePath'=>$basePath . $ds . 'protected',
	'name'=>'СS:Bans 1.4',
	'sourceLanguage' => 'ru',
	'language'=>'ru',
    'timeZone' => 'Europe/Moscow',
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

    'aliases' => [
        'vendor' => $basePath . $ds . 'vendor',
    ],
    
	// Компоненты приложения
	'components'=>array(
        'prefs' => array(
            'class' => 'application.components.Prefs'
        ),
		// Бутстрап
		'bootstrap'=>array(
			'class'=>'bootstrap.components.Bootstrap',
		),
		// Конфиг (из таблицы {{webconfig}})
		'config'=>array(
			'class' => 'DConfig'
		),
		'IpToCountry'=>array(
			'class' => 'Ip2Country'
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

	// Дополнительные параметры (вызываются так: Yii::app()->params['adminEmail'])
	'params'=>array(
		'adminEmail'=>'webmaster@example.com',
		'dbname' => $config->db_db,
		'Version' => '1.4',
	),
);
