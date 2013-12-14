<?php
/**
 * Вьюшка управления уровнями веб админов
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
	'Веб уровни',
);

$this->menu=array(
	array('label'=>'Админцентр', 'url'=>array('/admin/index')),
	array('label'=>'Добавить уровень', 'url'=>array('create')),
);
$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webadmlevel'));
?>

<h2>Управление уровнями веб админов</h2>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'levels-grid',
	'dataProvider'=>$model->search(),
	'enableSorting' => FALSE,
	'columns'=>array(
		'level',

		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{update} {delete}'
		),
	),
)); ?>
