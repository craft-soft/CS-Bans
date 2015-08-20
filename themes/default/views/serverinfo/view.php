<?php
/**
 * Вьюшка просмотра сервера
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$info = $server->getInfo();

$this->pageTitle = Yii::app()->name .' :: Сервер ' . $info['name'];

$this->breadcrumbs=array(
	'Серверы'=>array('index'),
	$info['name'],
);

// Если страницу запрашивает аякс, то не отдаем ему жабаскрипт совсем
if(!Yii::app()->request->isAjaxRequest):

if(!Yii::app()->user->isGuest)
{
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.contextmenu.js');
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/jquery.contextmenu.css');
	Yii::app()->clientScript->registerScript('playerAction', "
	setInterval('reloadplayers()', 5000);
	function reloadplayers()
	{
		$.post('',{'".Yii::app()->request->csrfTokenName."': '".Yii::app()->request->csrfToken."'},function(data){ $('#container').html(data); });
	}
	function playeraction(player, action, reason, time)
	{
		var reasontext;
		var profile = false;
		var reason = null;

		switch (action)
		{
			case 'ban':
				reasontext = 'Забанить пользователя'
				break;
			case 'kick':
				reasontext = 'Кикнуть пользователя'
				break;
			case 'message':
				reasontext = 'Отправить сообщение пользователю'
				break;
			default:
				return false;
		}

		if(!confirm(reasontext + ' ' + player + '?')) {
			return false;
		}
		if(action == 'ban')
		{
			var reason = prompt('Введите причину бана', 'Читер');
			var bantime = prompt('Введите время бана в минутах (1440 - сутки, 10080 - неделя, 43200 - месяц)', '1440');
			if(!reason || !bantime) {
				return false;
			}
		}

		if(action == 'message')
		{
			var reason = prompt('Введите сообщение для игрока ' + player, '');
			if(!reason)
			{
				return false;
			}
		}
		$('#loading').show();
		$.post(
			'".Yii::app()->createUrl('/serverinfo/context', array('id' => $server->id))."',
			{
				'ajax': 1,
				'action': action,
				'player': player,
				'reason': reason,
				'time': bantime
			},
			function(data){eval(data);}
		);
	}

	$(function(){
		$.contextMenu({
			selector: '.context-menu-one',
			callback: function(key, options) {
				var player = options.\$trigger.attr('id');
				playeraction(player, key);
			},
			items: {
				'ban': {name: 'Забанить'},
				'separator': '-----',
				'kick': {name: 'Кикнуть'},
				'separator2': '-----',
				'message': {name: 'Отправить сообщение'},
			}
		});
	});
	", CClientScript::POS_END
	);

}
endif;
?>

<div id="container">
	<?php if($info): ?>
	<h2>Детали сервера &laquo;<?php echo $info['name']; ?>&raquo;</h2>
	<?php if(!Yii::app()->user->isGuest): ?>
	<p class="text-success">
		<i class="icon-exclamation-sign"></i>
		<i>Нажмите правой кнопкой на игроке для вызова меню</i>
	</p>
	<?php endif; ?>

	<div class="row-fluid">
		<div class="span7">
			<?php if(is_array($info['playersinfo']) && !empty($info['playersinfo'])): ?>
			<h5 class="text-center">Игроки</h5>
			<table class="table table-bordered" id="players">
				<thead>
					<th>
						Ник
					</th>
					<th style="text-align: center">
						Счет
					</th>
					<th style="text-align: center">
						Время
					</th>
				</thead>
				<tbody>
					<?php
					foreach($info['playersinfo'] as $player):?>
						<tr class="context-menu-one" id="<?php echo CHtml::encode($player['name'])?>">
							<td><?php echo CHtml::encode($player['name'])?></td>
							<td style="text-align: center"><?php echo CHtml::encode($player['score'])?></td>
							<td style="text-align: center"><?php echo function_exists('query_live') ? $player['time'] : Prefs::date2word(intval($player['time']), FALSE, TRUE)?></td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<?php else: ?>
			<div class="alert alert-error">Нет игроков</div>
			<?php endif; ?>
		</div>
		<div class="span5">
			<h5 class="text-center">Информация</h5>
			<table class="table table-bordered">
				<tr>
					<td style="text-align: center" colspan="2">
						<?php echo $info['mapimg']; ?>
					</td>
				</tr>
				<tr>
					<td class="bold">
						Адрес:
					</td>
					<td>
						 <?php
						 echo CHtml::link(
								 CHtml::encode($server->address),
								 'steam://connect/' . CHtml::encode($server->address)
							 );
						 ?>
					</td>
				</tr>
				<tr>
					<td class="bold">
						Карта:
					</td>
					<td>
						 <?php echo CHtml::encode($info['map']); ?>
					</td>
				</tr>
				<tr>
					<td class="bold">
						Игроки:
					</td>
					<td>
						 <?php
						 echo CHtml::encode($info['players']) . '/' . CHtml::encode($info['playersmax']);
						 ?>
					</td>
				</tr>
				<?php if($info['nextmap']):?>
				<tr>
					<td class="bold">
						Следующая карта:
					</td>
					<td>
						 <?php echo CHtml::encode($info['nextmap']); ?>
					</td>
				</tr>
				<?php endif?>
				<?php if($info['timeleft']):?>
				<tr>
					<td class="bold">
						До смены карты:
					</td>
					<td>
						 <?php echo CHtml::encode($info['timeleft']); ?>
					</td>
				</tr>
				<?php endif?>
				<?php if($info['contact']):?>
				<tr>
					<td class="bold">
						Контакты:
					</td>
					<td>
						 <?php echo CHtml::encode($info['contact']); ?>
					</td>
				</tr>
				<?php endif?>
			</table>
		</div>
	</div>
	<?php else: ?>
	<h2>Детали сервера &laquo;<?php echo $server->hostname; ?>&raquo;</h2>
	<div class="alert alert-error">
		Сервер не отвечает. Возможно сервер выключен или сменяет карту
	</div>
	<?php endif; ?>
</div>