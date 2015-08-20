<?php
/**
 * Вьюшка просмотра деталей веб админа
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Детали админа ' . $model->username;
$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Веб админы'=>array('admin'),
	'Детали админа ' . $model->username,
);

$this->menu=array(
	array('label'=>'Админцентр', 'url'=>array('/admin/index')),
	array('label'=>'Управление веб админами', 'url'=>array('index')),
	array('label'=>'Обновить', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Удалитьт веб админа?')),
);
$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webadmins'));
?>

<h2>Детали ВЕБ админа "<?php echo $model->username; ?>"</h2>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'username',
		'level',
		'email',
		array(
			'name' => 'last_action',
			'type' => 'datetime',
			'value' => $model->last_action
		),
		'try',
	),
)); ?>
