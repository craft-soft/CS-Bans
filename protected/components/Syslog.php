<?php
/**
 * Системный лог
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

class Syslog {

	public static function add($action, $desc) {
		$log = new Logs;

		$log->action = CHtml::encode($action);
		$log->remarks = CHtml::encode($desc);
		if($log->save())
			return TRUE;

		return FALSE;
	}
}
?>
