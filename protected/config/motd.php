<?php
/**
 * Конфигурация для MOTD
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

// Главные параметры приложения
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components' => array(
			'urlManager' => array(
				'urlFormat'=>'path',
				'showScriptName'=>false,
				'urlSuffix'=>'.html',
				'rules'=>array(
					'/'=>'/bans/motd',
				),
			),
		),
	)
);