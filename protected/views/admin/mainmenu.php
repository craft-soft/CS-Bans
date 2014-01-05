<?php
/**
 * Верхнее меню админцентра
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
 * @param string $active Определяет активный раздел меню
 * @param string $activeиет Определяет активную кнопку
 */
$disabled = ' disabled="disabled" onclick="return false;"';
$this->widget('bootstrap.widgets.TbTabs', array(
	'type' => 'tabs',
	'placement' => 'above',
	'tabs' => array(
		array(
			'label' => 'Админцентр',
			'content' => '
				<ul class="inline">
					<li><a href="' . Yii::app()->createUrl('/admin/index') . '" class="btn"'.($activebtn == 'admsystem' ? $disabled : '').'>Информация о системе</a></li>
					<li><a href="' . Yii::app()->createUrl('/Bans/create') . '" class="btn"'.($activebtn == 'admaddban' ? $disabled : '').'>Добавить бан</a></li>
					<li><a href="' . Yii::app()->createUrl('/admin/addbanonline') . '" class="btn"'.($activebtn == 'addbanonline' ? $disabled : '').'>Добавить бан онлайн</a></li>
				</ul>',
			'active' => $active == 'main'
		),
		array(
			'label' => 'Сервер',
			'content' => '
				<ul class="inline">
					<li><a href="' . Yii::app()->createUrl('/serverinfo/admin') . '" class="btn"'.($activebtn == 'servsettings' ? $disabled : '').'>Настройки</a></li>
					<li><a href="' . Yii::app()->createUrl('/admin/reasons') . '" class="btn"'.($activebtn == 'servreasons' ? $disabled : '').'>Причины банов</a></li>
					<li><a href="' . Yii::app()->createUrl('/amxadmins/admin') . '" class="btn"'.($activebtn == 'servamxadmins' ? $disabled : '').'>Админы</a></li>
					<li><a href="' . Yii::app()->createUrl('/amxadmins/adminsonservers') . '" class="btn"'.($activebtn == 'servadmassign' ? $disabled : '').'>Назначение админов</a></li>
				</ul>',
			'active' => $active == 'server'
		),
		array(
			'label' => 'Веб сайт',
			'content' => '
				<ul class="inline">
					<li><a href="' . Yii::app()->createUrl('/webadmins/admin') . '" class="btn"'.($activebtn == 'webadmins' ? $disabled : '').'>ВЕБ админы</a></li>
					<li><a href="' . Yii::app()->createUrl('/levels/admin') . '" class="btn"'.($activebtn == 'webadmlevel' ? $disabled : '').'>Уровни</a></li>
					<li><a href="' . Yii::app()->createUrl('/usermenu/admin') . '" class="btn"'.($activebtn == 'webmainmenu' ? $disabled : '').'>Ссылки</a></li>
					<li><a href="' . Yii::app()->createUrl('/admin/websettings') . '" class="btn"'.($activebtn == 'websettings' ? $disabled : '').'>Настройки</a></li>
					<li><a href="' . Yii::app()->createUrl('/logs/admin') . '" class="btn"'.($activebtn == 'logs' ? $disabled : '').'>Логи</a></li>
					'.(Yii::app()->hasModule('billing')
						?
					'<li><a href="'.Yii::app()->createUrl('/billing/tariff/admin').'" class="btn"'.($activebtn == 'tariffs' ? $disabled : '').'>Тарифы</a></li>'
						:
					'').'
				</ul>',
			'active' => $active == 'site'
		),
	),
));
?>
