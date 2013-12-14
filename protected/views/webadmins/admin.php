<?php
/**
 * Вьюшка управления веб админами
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - WEB админы';
$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Управление WEB админами',
);

$this->menu=array(
	array('label'=>'Админцентр', 'url'=>array('/admin/index')),
	array('label'=>'Добавить админа', 'url'=>array('create')),
);

$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webadmins'));
?>

<h2>Управление ВЕБ админаи</h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'webadmins-grid',
	'type'=>'striped bordered condensed',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'username',
		'email',
		'level',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
