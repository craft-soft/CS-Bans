<?php
/**
 * Вьюшка деталей записи системного лога
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Системный лог';

$this->breadcrumbs=array(
	'Logs'=>array('index'),
	$model->id,
);

$this->menu = array(
	array(
		'label' => 'Все записи',
		'url' => array('admin')
	)
);

$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'logs'));
?>

<h2>Запись №<?php echo $model->id; ?></h2>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'name' => 'timestamp',
			'type' => 'datetime',
			'value' => $model->timestamp
		),
		'ip',
		'username',
		array(
			'name' => 'action',
			'value' => Logs::getLogType($model->action)
		),
		array(
			'name' => 'remarks',
			'type' => 'raw',
			'value' => CHtml::decode($model->remarks)
		)
	),
)); ?>
