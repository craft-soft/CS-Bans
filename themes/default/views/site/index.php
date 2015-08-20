<?php
/**
 * Вьюшка главной страницы сайта
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

// Перенаправление, если в параметрах указана другая страница главной
if(Yii::app()->config->start_page !== '/site/index')
	$this->redirect(array(Yii::app()->config->start_page));

$this->pageTitle=Yii::app()->name;

?>

<?php
$banner = Yii::app()->config->banner ? ' url('.Yii::app()->urlManager->baseUrl.'/images/banner/'.Yii::app()->config->banner.')' : '';
$this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name),
	'htmlOptions'=>array(
		//'style' => 'background: #c1c1c1'.$banner.';color:#fff;text-shadow: 2px 2px 3px #1b1b1b;'
	)
)); ?>

<p><?php echo CHtml::encode(Yii::app()->name); ?> установлен.</p>

<?php $this->endWidget(); ?>

<div class="row-fluid">
	<div class="span6">
		<div class="alert alert-info"><h4>Последние 10 банов</h4></div>
		<?php
		$this->widget('bootstrap.widgets.TbGridView', array(
			'dataProvider'=>$bans,
			'type'=>'striped bordered condensed',
			'id' => 'bans-grid',
			'template' => '{items} {pager}',
			'enableSorting' => false,
			'rowHtmlOptionsExpression'=>'array(
				"style" => "cursor:pointer;",
				"class" => $data->expired == 1 ? "bantr success" : "bantr",
				"onclick" => "document.location.href=\'".Yii::app()->createUrl("/bans/view", array("id" => $data->bid))."\'"
			)',
			'columns'=>array(
				'player_nick',
				array(
					'name' => 'ban_created',
					'value' => 'date("d.m.Y",$data->ban_created)',
				),
				array(
					'name'=>'ban_length',
					'value' => 'Prefs::date2word($data->ban_length)',
					'htmlOptions' => array(
						'style' => 'width: 130px'
					)
				)
			),
		));
		?>
	</div>

	<?php
	// Информация с серверов собирается аяксом. Функция написана выше
	?>
	<div class="span6">
		<div class="alert alert-info"><h4>Сервера</h4></div>
		<table class="table table-bordered table-condensed table-striped">
			<thead>
				<tr>
					<th>Имя сервера</th>
					<th>Игроки</th>
					<th>Карта</th>
				</tr>
			</thead>
			<tbody id="servers">
				<?php foreach($servers as $server):?>
				<tr 
					class="warning" 
					style="cursor: pointer"
					id="server<?php echo intval($server['id'])?>"
					onclick="document.location.href='<?php echo $this->createUrl('/serverinfo/view', array('id' => $server['id'])) ?>'"
				>
					<td colspan="3">
						<?php echo $server['hostname']?>
						&nbsp;
						<?php echo CHtml::image(Yii::app()->baseUrl . '/images/loading.gif'); ?>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>

	</div>
</div>
<script>
	$(document).ready(function(){
	<?php foreach($servers as $server):?>
		$.post(
			"<?php echo $this->createUrl('/serverinfo/getinfo')?>",
			{
				'<?php echo Yii::app()->request->csrfTokenName ?>': '<?php echo Yii::app()->request->csrfToken ?>',
				'server': '<?php echo intval($server['id'])?>'
			},
			function(data){
				var ret;
				var info = $.parseJSON(data);
				var elem = $('#server<?php echo intval($server['id'])?>');
				if(!info)
				{
					ret = '<td colspan="3"><?php echo $server['hostname']?> <b>Не отвечает</b></td>';
					elem.addClass('error');
				}
				else
				{
					ret = '<td>' + info.name + '</td><td>' + info.players + '/' + info.playersmax + '</td><td>' + info.map + '</td>';
				}
				elem.removeClass('warning').html(ret);
			}
		);
	<?php endforeach;?>
	});
</script>