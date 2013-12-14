<?php
/**
 * Вьюшка просмотра деталей админа серверов
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle = Yii::app()->name . ' :: Админцентр - Редактировать админа';
$this->breadcrumbs = array(
	'Админцентр' => array('/admin/index'),
	'AmxModX админы' => array('admin'),
	'Админ ' . $model->nickname
);
$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servamxadmins'));

$this->menu=array(
	array('label'=>'Добавить AmxModX админа', 'url'=>array('create')),
	array('label'=>'Управление AmxModX админами', 'url'=>array('admin')),
);
?>
<h2>Детали админа &laquo;<?php echo $model->nickname; ?>&raquo;</h2>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array(
			'name' => 'flags',
			'value' => Amxadmins::getAuthType($model->flags)
		),
		'username',
		'nickname',
		'steamid',
		'access',
		'icq',
		array(
			'name' => 'ashow',
			'value' => $model->ashow == 1 ? 'Да' : 'Нет'
		),
		array(
			'name' => 'created',
			'type' => 'datetime',
			'value' => $model->created
		),
		array(
			'name' => 'expired',
			'value' => $model->expired == 0 ? 'Навсегда' : date('d.m.Y H:i', $model->expired)
		),
		'days',
	),
)); ?>
