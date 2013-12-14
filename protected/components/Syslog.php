<?php
/**
 * Системный лог
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 * 
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
