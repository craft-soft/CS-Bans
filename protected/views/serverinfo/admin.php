<?php
/**
 * Вьюшка управления серверами
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Управление серверами';

$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Управление серверами',
);

$this->menu=array(
	array('label'=>'Админцентр', 'url'=>array('/admin/index')),
	array('label'=>'Добавить сервер', 'url'=>array('create')),
);

$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servsettings'));

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#serverinfo-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>Управление серверами</h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type'=>'striped bordered condensed',
	'id'=>'serverinfo-grid',
	'dataProvider'=>$model->search(),
	'enableSorting' => FALSE,
	//'filter'=>$model,
	'columns'=>array(
		array(
			'name' => 'Мод',
			'type' => 'image',
			'value' => '$data->modimg',
			'htmlOptions' => array('style'=>'text-align: center'),
		),
		'address',
		'hostname',
		'amxban_version',
		array(
			'name' => 'timestamp',
			'type' => 'datetime',
			'value' => '$data->timestamp'
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
