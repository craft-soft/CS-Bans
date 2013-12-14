<?php
/**
 * Вьюшка назначения админов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Администраторы серверов';
$this->breadcrumbs=array(
	'Админцентр' => array('/admin/index'),
	'Администраторы серверов',
);

$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servadmassign'));
?>
<h2>Управление админами серверов</h2>
<?php

Yii::app()->clientScript->registerScript('serverview', '
$(".servtr").live("click", function(){
	$("#loading").show();
	var sid = this.id.substr(5);
	$.post(
		"",
		{
			"'.Yii::app()->request->csrfTokenName.'": "'.Yii::app()->request->csrfToken.'",
			"sid": sid
		},
		function(data){
			$("#loading").hide();
			$("#ololo").html(data);
		}
	);
});
$.post(
	"'.Yii::app()->createUrl('/serverinfo/getinfo', array('page' => 'serveradmins')).'",
	{"'.Yii::app()->request->csrfTokenName.'": "'.Yii::app()->request->csrfToken.'"},
	function(data) {$("#servers").html(data);}
);
');

Yii::app()->clientScript->registerScript('checkaccess', '
function checkaccess(hash)
{

	if($("#active" + hash).prop("checked"))
		$(".input" + hash).show();

	else
		$(".input" + hash).hide();
}
', CClientScript::POS_END);
?>

<table class="table table-bordered table-condensed table-striped">
	<thead>
		<tr>
			<th style="width: 20px">Мод</th>
			<th style="width: 150px">Адрес</th>
			<th>Имя</th>
		</tr>
	</thead>
	<tbody id="servers">
		<tr class="warning">
			<td colspan="7">
				Получение информации с серверов
				&nbsp;
				<?php echo CHtml::image(Yii::app()->baseUrl . '/images/loading.gif'); ?>
			</td>
		</tr>
	</tbody>
</table>
<div id="ololo"></div>
<?php $this->beginWidget('bootstrap.widgets.TbModal',
	array(
		'id'=>'selectadmins',
		'htmlOptions' => array(
			'style' => 'width: 800px; margin-left: -400px'
		)
)); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal"  data-placement="bottom">&times;</a>
    <h4>Выберите админов</h4>
</div>

<div class="modal-body">
</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Закрыть',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
<?php $this->endWidget(); ?>