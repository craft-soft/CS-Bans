<?php
/**
 * Управление логами
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name . ' :: Админцентр - Системный лог';
$this->breadcrumbs = array(
	'Админцентр' => array('/admin/index'),
	'Системный лог'
);

$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'logs'));
?>

<h2>Системный лог</h2>

<?php

$criteria = new CDbCriteria();
$criteria->group = 'username';

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'logs-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'reinstallDatePicker',
	'columns'=>array(
		array(
			'name' => 'timestamp',
			'type' => 'datetime',
			'value' => '$data->timestamp',
			'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'id' => 'timestamp',
				'attribute' => 'timestamp',
				'language' => 'ru',
				'i18nScriptFile' => 'jquery-ui-i18n.min.js',
				'htmlOptions' => array(
					'id' => 'timestamp',
					'size' => '10',
				),
				'options' => array(
					'showAnim'=>'fold',
					/*'dateFormat'=>'yy-mm-dd',
					'changeMonth' => 'true',
					'changeYear'=>'true',*/
				)
			), true),
		),
		array(
			'name' => 'username',
			'filter' => CHtml::listData(Logs::model()->findAll($criteria), 'username', 'username'),
		),
		array(
			'name' => 'action',
			'value' => 'Logs::getLogType($data->action)',
			'filter' => Logs::getLogType(FALSE, TRUE),
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{view} {delete}',
		),
	),
));

Yii::app()->clientScript->registerScript('re-install-date-picker', "
	function reinstallDatePicker(id, data) {
		$('#timestamp').datepicker(jQuery.extend({showMonthAfterYear:false},jQuery.datepicker.regional['ru'],{'showAnim':'fold'}));
	}
");
?>
