<?php
/**
 * Управление логами
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */
?>

<?php
$this->pageTitle = Yii::app()->name . ' :: Админцентр - Системный лог';
$this->breadcrumbs = array(
	'Админцентр' => array('/admin/index'),
	'Системный лог'
);

$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'logs'));
?>

<h2>Системный лог</h2>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'logs-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name' => 'timestamp',
			'type' => 'datetime',
			'value' => '$data->timestamp'
		),
		'username',
		array(
			'name' => 'action',
			'value' => 'Logs::getLogType($data->action)',
			'filter' => Logs::getLogType(FALSE, TRUE)
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{view} {delete}'
		),
	),
)); ?>
