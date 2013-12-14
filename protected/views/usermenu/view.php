<?php
/**
 * Вьюшка просмотра деталей ссылки главного меню
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Главное меню';
$this->breadcrumbs=array(
	'Админцентр'=> array('/admin/index'),
	'Главное меню'=>array('admin'),
	'Ссылка № '.$model->id,
);

$this->menu=array(
	array('label'=>'Удалить','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Удалить ссылку?')),
	array('label'=>'Редактировать','url'=>array('update','id'=>$model->id)),
	array('label'=>'Добавить ссылку','url'=>array('create')),
	array('label'=>'Управление ссылками','url'=>array('admin')),
);
$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webmainmenu'));
?>

<h2>Детали ссылки №<?php echo $model->id; ?></h2>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'pos',
		array(
			'name' => 'activ',
			'value' => $model->activ == 1 ? 'Да' : 'Нет'
		),
		'lang_key',
		'url',
		'lang_key2',
		'url2',
	),
)); ?>
