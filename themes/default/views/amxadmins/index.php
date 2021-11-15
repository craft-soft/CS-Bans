<?php
/**
 * Вьюшка главной страницы админов серверов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$page = 'Администраторы';

$this->pageTitle = Yii::app()->name . ' - ' . $page;

$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    'links'=>array($page),
));

Yii::app()->clientScript->registerScript('viewdetail', "
$('.admintr').bind('click', function(){
	$('#loading').show();
	var aid = this.id.substr(6);
	$.post('".Yii::app()->urlManager->baseUrl."/amxadmins/view/', {'aid': aid}, function(data){
		eval(data);
	});
})
");
?>
<h2><?php echo $page; ?></h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'dataProvider'=>$admins,
	'type'=>'striped bordered condensed',
	'id' => 'admins-grid',
	//'template' => '{items} {pager}',
	'summaryText' => 'Показано с {start} по {end} админов из {count}. Страница {page} из {pages}',
	'enableSorting' => false,
	'rowHtmlOptionsExpression'=>'array(
		"id" => "admin_$data->id",
		"style" => "cursor:pointer;",
		"class" => "admintr"
	)',
	'pager' => array(
		'class'=>'bootstrap.widgets.TbPager',
		'displayFirstAndLast' => true,
	),
	'columns'=>array(
		array(
			'name' => 'nickname',
			'value' => '$data->nickname',
			'htmlOptions' => array(
				'style' => 'width: 340px;'
			)
		),
		array(
			'name' => 'icq',
			'type' => 'raw',
			'value' => '$data->icq != 0 ? CHtml::encode($data->icq) : "<i>Не задан</i>"',
			'htmlOptions' => array(
				'style' => 'width: 100px;'
			)
		),
		array(
			'name' => 'access',
			'value' => '$data->access',
			'htmlOptions' => array(
				'style' => 'width: 160px;'
			)
		),
		array(
			'name' => 'created',
			'value' => 'date("d.m.Y - H:i:s",$data->created)',
			'htmlOptions' => array(
				'style' => 'width: 170px; text-align: center'
			)
		),

		array(
			'name' => 'expired',
			'type' => 'raw',
			'value' => '$data->expired == 0 ? "<i>Никогда</i>" : date("d.m.Y - H:i:s",$data->expired)',
			'htmlOptions' => array(
				'style' => 'width: 170px; text-align: center'
			)
		),
	),
)); ?>
<?php $this->beginWidget('bootstrap.widgets.TbModal',
	array(
		'id'=>'adminDetail',
		'htmlOptions' => array(
			'style' => 'width: 600px; margin-left: -300px; min-height: 600px'
		)
)); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal" rel="tooltip" data-placement="left" title="Закрыть">&times;</a>
    <h4>Детали админа</h4>
</div>
<div class="modal-body" style="min-height: 450px">
	<h3>Инфо</h3>
	<div id="adminInfo"></div>
	<hr>
	<h3>STEAM</h3>
	<div id="adminSteam"></div>
	<hr>
	<h3>Админка на серверах</h3>
	<div id="adminServers"></div>
	<hr>
</div>
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Закрыть',
        'url'=>'#',
        'htmlOptions'=>array(
			'data-dismiss'=>'modal',
		),
    )); ?>
</div>
<?php $this->endWidget(); ?>

<div style="width: 200px; margin: 0 auto; text-align: center">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Информация доступа',
        'url'=>'#',
        'htmlOptions'=>array(
			'onclick'=>'$("#info_access").slideToggle("slow"); return false;',
		),
    )); ?>
</div>

<div id="info_access" class="row-fluid" style="display: none">
	<div class="span6">
		<h3 class="muted">Права доступа</h3>
		<?php
		foreach(Amxadmins::getFlags(TRUE) as $flag => $desc):
			echo $flag . ' - ' . $desc . '<br />';
		endforeach;
		?>
	</div>
	<div class="span6">
		<h3 class="muted">Флаги доступа</h3>
		a - Кикать игрока при вводе некорректного пароля<br />
		b - Тег клана<br />
		c - Для SteamID<br />
		d - Для IP<br />
		e - Пароль не требуется (важен только SteamID либо IP )<br />
		k - Имя или тег (С УчёТом РеГистРа!).
	</div>
</div>