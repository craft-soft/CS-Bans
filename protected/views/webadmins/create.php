<?php
/**
 * Вьюшка добавления веб админа
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Добавить WEB админа';
$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Веб админы'=>array('admin'),
	'Создать нового веб админа',
);

$this->menu=array(
	array('label'=>'Админцентр', 'url'=>array('/admin/index')),
	array('label'=>'ВЕБ админамы', 'url'=>array('admin')),
);
$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webadmins'));
?>

<h2>Добавить веб админа</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>