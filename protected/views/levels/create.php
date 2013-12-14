<?php
/**
 * Вьюшка добавления уровня веб админов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Веб уровни';

$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Веб уровни'=>array('admin'),
	'Добавить'
);

$this->menu=array(
	array('label'=>'Админцентр','url'=>array('index')),
	array('label'=>'Уровни','url'=>array('admin')),
);

$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webadmlevel'));
?>

<h2>Добавить новый уровень веб админов</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>