<?php
/**
 * Конфигурация для MOTD
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
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