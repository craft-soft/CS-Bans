<?php
/**
 * Вьюшка добавления причины бана
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$page = 'Причины банов :: Добавить причину';
$this->pageTitle = Yii::app()->name . ' - ' . $page;

$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Причины банов' => array('/admin/reasons'),
	'Добавить причину'
);

$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servreasons'));

?>

<h2>Добавить причину бана</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>