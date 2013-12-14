<?php
/**
 * Вьюшка причин банов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$page = 'Причины банов';
$this->pageTitle = Yii::app()->name . ' - ' . $page;

$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	$page
);

$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servreasons'));

$this->menu=array(
	array('label'=>'Добавить группу причин','url'=>array('/reasonsSet/create')),
	array('label'=>'Добавить причину','url'=>array('/reasons/create')),
);


Yii::app()->clientScript->registerScript('getreasons', '
function getreasons(groupid)
{
	$("#loading").show();
	$.post("", {"groupid": groupid.substr(6), "'.Yii::app()->request->csrfTokenName.'": "'.Yii::app()->request->csrfToken.'"}, function(data){eval(data);});
}
function clearmodal()
{
	$(".modal-header").html("");
	$(".modal-body").html("");
	$(".save").remove();
	$("#reasons-modal").modal("hide");
}
', CClientScript::POS_END);
?>


<h2>Управление причинами банов</h2>

<h4>Группы причин</h4>
<small class="text-success">Кликнуть на группе для редактирования</small>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type' => 'bordered condensed striped',
	'id'=>'reasonsset-grid',
	'dataProvider'=>$reasonsset,
	//'summaryText' => 'Показано с {start} по {end} причин из {count}. Страница {page} из {pages}',
	'template' => '{pager} {items}',
	'pager' => array(
		'class'=>'bootstrap.widgets.TbPager',
		'displayFirstAndLast' => true,
	),
	'rowHtmlOptionsExpression'=>'array(
		"class" => "rgroup",
		"id" => "rgroup" . $data->id,
		"style" => "cursor:pointer;",
	)',
	'enableSorting' => FALSE,
	'columns'=>array(
		array(
			'name' => 'setname',
			'value' => '$data->setname',
			'htmlOptions' => array(
				'onclick' => 'getreasons($(this).closest("tr").attr("id"));'
			)
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{delete}',
			'buttons' => array(
				'delete' => array(
					'url' => 'Yii::app()->createUrl("/reasonsSet/delete", array("id" => $data->id))'
				)
			)
		),
	),
)); ?>


<h4>Причины</h4>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type' => 'bordered condensed striped',
	'id'=>'reasons-grid',
	'dataProvider'=>$reasons,
	'template' => '{pager} {items}',
	//'summaryText' => 'Показано с {start} по {end} причин из {count}. Страница {page} из {pages}',
	'pager' => array(
		'class'=>'bootstrap.widgets.TbPager',
		'displayFirstAndLast' => true,
	),
	'enableSorting' => FALSE,
	'columns'=>array(
		'reason',
		'static_bantime',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{update} {delete}',
			'buttons' => array(
				'update' => array(
					'url' => 'Yii::app()->createUrl("/reasons/update", array("id" => $data->id))'
				),
				'delete' => array(
					'url' => 'Yii::app()->createUrl("/reasons/delete", array("id" => $data->id))'
				)
			)
		),
	),
)); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal',
	array(
		'id'=>'reasons-modal',
		'htmlOptions' => array(
			'style' => 'width: 600px; margin-left: -300px'
		)
)); ?>

<div class="modal-header"></div>

<div class="modal-body"></div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Закрыть',
        'url'=>'#',
        'htmlOptions'=>array(
			//'data-dismiss'=>'modal',
			'onclick' => 'clearmodal()'
			),
    )); ?>
</div>
<?php $this->endWidget(); ?>