<?php
/**
 * Вьюшка главной страницы серверов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Список серверов';

$this->breadcrumbs=array(
	'Серверы',
);

Yii::app()->clientScript->registerScript('serverview', '
$(".servtr").bind("click", function(){
	$("#loading").show();
	var sid = this.id.substr(6);
	$.post(
		"'.Yii::app()->urlManager->createUrl('/serverinfo/serverdetail').'",
		{
			"'.Yii::app()->request->csrfTokenName.'": "'.Yii::app()->request->csrfToken.'",
			"sid": sid
		},
		function(data){
			eval(data);
		}
	);
});
');

?>
<h2>Серверы</h2>

<div class="container">
	<div class="row-fluid">
		<div class="span9">
			<table class="table table-bordered table-condensed table-striped">
				<thead>
					<tr>
						<th>Мод</th>
						<th>OS</th>
						<th>VAC</th>
						<th>Имя</th>
						<th>Игроки</th>
						<th>Карта</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="servers">
					<?php foreach($servers as $server):?>
					<tr class="warning" id="server<?php echo intval($server['id'])?>">
						<td colspan="7">
							<?php echo $server['hostname']?>
							&nbsp;
							<?php echo CHtml::image(Yii::app()->baseUrl . '/images/loading.gif'); ?>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		<div class="span3">
			<table class="items table table-bordered table-condensed">
				<thead>
					<tr>
						<th>Статистика</th>
					</tr>
				</thead>
				<tbody>
					<tr class="odd">
						<td>
							<div class="row-fluid">
								<div class="span8 muted">
									<b>Всего банов:</b>
								</div>
								<div class="span4">
									<?php echo $info['bancount']; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr class="odd">
						<td>
							<div class="row-fluid">
								<div class="span8 muted">
									<b>Активные:</b>
								</div>
								<div class="span4">
									<?php echo $info['actbans']; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr class="odd">
						<td>
							<div class="row-fluid">
								<div class="span8 muted">
									<b>Перманентные:</b>
								</div>
								<div class="span4">
									<?php echo $info['permbans']; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr class="odd">
						<td>
							<div class="row-fluid">
								<div class="span8 muted">
									<b>Временные:</b>
								</div>
								<div class="span4">
									<?php echo $info['tempbans']; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr class="odd">
						<td>
							<br />
						</td>
					</tr>
					<tr class="odd">
						<td>
							<div class="row-fluid">
								<div class="span8 muted">
									<b>Админов:</b>
								</div>
								<div class="span4">
									<?php echo $info['admins']; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr class="odd">
						<td>
							<div class="row-fluid">
								<div class="span8 muted">
									<b>Серверов:</b>
								</div>
								<div class="span4">
									<?php echo $info['serversCount']; ?>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php $this->beginWidget('bootstrap.widgets.TbModal',
	array(
		'id'=>'ServerDetail',
		'htmlOptions' => array(
			'style' => 'width: 600px; margin-left: -300px'
		)
)); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal"  data-placement="bottom">&times;</a>
    <h4>Детали сервера <a id="serverlink" rel="tooltip" data-placement="bottom" data-title="Показать подробности"></a></h4>
</div>

<div class="modal-body" id="ban_name">
	<div class="row-fluid">
			<div class="span6">
				<b>Название: </b>
				<span id="server-name"></span>
				<hr>
				<b>Адрес: </b>
				<span id="server-address"></span>
				<hr>
				<b>Карта: </b>
				<span id="server-map"></span>
				<hr>
				<b>Игроки: </b>
				<span id="server-players"></span>
			</div>
			<div class="span6">
				<div id="server-mapimage" class="text-center"></div>
				<hr>
				<div id="server-links" class="text-center">
					<a id="steam-connect" title="Подключиться">
						<?php echo CHtml::image(Yii::app()->urlManager->baseUrl. '/images/steam-connect.png'); ?>
					</a>
					&nbsp;
					<a id="hlws-add">
						<?php echo CHtml::image(Yii::app()->urlManager->baseUrl . '/images/hlsw-add.png'); ?>
					</a>
				</div>
			</div>
	</div>
	<hr>
	<p class="text-success">
		<i class="icon-user"></i>
		Игроки
	</p>
	<div id="serverinfo-players"></div>
</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Закрыть',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
<?php $this->endWidget(); ?>
<script>
	$(document).ready(function(){
	<?php foreach($servers as $server):?>
		$.post(
			"<?php echo $this->createUrl('/serverinfo/getinfo')?>",
			{
				'<?php echo Yii::app()->request->csrfTokenName ?>': '<?php echo Yii::app()->request->csrfToken ?>',
				'server': '<?php echo intval($server->id)?>'
			},
			function(data){
				var ret;
				var info = $.parseJSON(data);
				var elem = $('#server<?php echo intval($server->id)?>');
				if(!info)
				{
					ret = '<td colspan="7"><?php echo $server['hostname']?> <b>Не отвечает</b></td>';
					elem.addClass('error');
				}
				else
				{
					ret = '<td style="text-align:center"><img src="' + info.modimg + '" title="' + info.game + '" /></td>' + 
						  '<td style="text-align:center"><img src="' + info.osimg + '" title="' + info.os + '" /></td>' +
						  '<td style="text-align:center"><img src="' + info.vacimg + '" /></td>' + 
						  '<td>' + info.name + '</td>' + 
						  '<td style="text-align:center">' + info.players + '/' + info.playersmax + '</td>' + 
						  '<td>' + info.map + '</td>' + 
						  '<td style="text-align:center"><?php echo CHtml::link('<i class="icon-eye-open"></i>',Yii::app()->createUrl('/serverinfo/view',array('id' => $server->id)),array('rel' => 'tooltip','title' => 'Подробности'))?></td>';
				  elem.addClass('servtr').css('cursor', 'pointer');
				}
				elem.removeClass('warning').html(ret);
			}
		);
	<?php endforeach;?>
	});
</script>