<?php
/**
 * Вьюшка добавления ссылки главного меню
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Добавить ссылку';
$this->breadcrumbs=array(
	'Админцентр'=> array('/admin/index'),
	'Главное меню'=>array('index'),
	'Добавить ссылку',
);

$this->menu=array(
	array('label'=>'Управление ссылками','url'=>array('admin')),
);
$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webmainmenu'));
?>

<h2>Добавить ссылку</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
