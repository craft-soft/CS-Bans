<?php
/**
 * Индекс
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

date_default_timezone_set('Europe/Moscow');

define('ROOTPATH', __DIR__);

// Дебаг
//defined('YII_DEBUG') or define('YII_DEBUG', true);
// Кол-во стак трейсов ошибок
//defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

// Ядро
require_once(ROOTPATH . '/include/yii/framework/yii.php');

// Создаем приложение
Yii::createWebApplication(ROOTPATH . '/protected/config/main.php');
$app = Yii::app();

if(Yii::app()->db->username) {

	// Здаем главную страницу
	if($app->config->start_page !== '/site/index')
		$app->homeUrl = array($app->config->start_page);

	// Задаем шаблон
	if(is_dir(ROOTPATH . '/themes/' . $app->config->design))
		$app->setTheme($app->config->design);
}

Yii::app()->db->autoConnect = TRUE;

// Запускаем приложение
$app->run();
