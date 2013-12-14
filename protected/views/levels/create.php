<?php
/**
 * Вьюшка добавления уровня веб админов
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
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