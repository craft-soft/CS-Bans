<?php
/**
 * Вьюшка главной страницы админцентра
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$page = 'Админцентр';
$this->pageTitle = Yii::app()->name . ' - ' . $page;

$this->breadcrumbs=array(
	$page,
);

Yii::app()->clientScript->registerScript('adminaction', "
$.post('".$this->createUrl('version')."', {'version': 1}, function(data){
	$('#version').html(data);
});
$('.bdaction').click(function(){
	if(!confirm('Вы подтверждаете свои действия?'))
	{
		return false;
	}
	if(this.id == 'truncatebans' && !confirm('Все баны будут удалены. Вы точно уверены?'))
	{
		return false;
	}
	$('#loading').show();
	$.post('".Yii::app()->createUrl('admin/actions')."', {'ajax': 1, 'action': this.id}, function(data) {eval(data);});
})
");

$this->renderPartial('/admin/mainmenu', array('active' =>'main', 'activebtn' => 'admsystem'));
$sysprefs = Prefs::sysprefs();
?>
<h2>Информация о системе</h2>
<div class="container">
  <div class="row-fluid">
    <div class="span8">
		<table class="table table-bordered table-condensed">
			<tr>
				<td class="info" colspan="2">
					<b>Настройки сервера</b>
				</td>
			</tr>
			<tr>
				<td style="width: 200px">
					Версия сайта
				</td>
				<td id="version">
					<?php echo Yii::app()->params['Version'] ?> <?php echo CHtml::image(Yii::app()->baseUrl . '/images/loading.gif'); ?>
				</td>
			</tr>
			<?php
			foreach($sysprefs['info'] as $key => $val): ?>
			<tr>
				<td style="width: 200px">
					<?php echo $key; ?>
				</td>
				<td>
					<?php echo $val; ?>
				</td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td class="info" colspan="2">
					<b>PHP модули</b>
				</td>
			</tr>
			<?php foreach($sysprefs['modules'] as $key => $val): ?>
			<tr>
				<td style="width: 200px">
					<?php echo $key; ?>
				</td>
				<td>
					<?php echo $val; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
    </div>
    <div class="span4">
		<table class="items table table-bordered table-condensed">
			<thead>
				<tr>
					<th>Статистика</th>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<td>
						<div class="pull-left muted">
							<b>Размер базы данных</b>
						</div>
						<div class="pull-right">
							<?php echo Prefs::db_size(); ?>
						</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div class="pull-left muted">
							<b>Кол-во банов в базе</b>
						</div>
						<div class="pull-right">
							<?php echo $sysinfo['bancount']; ?>
						</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div class="pull-left muted">
							<b>Активные баны</b>
						</div>
						<div class="pull-right">
							<?php echo $sysinfo['bancount']; ?>
						</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div class="pull-left muted">
							<b>Комментарии</b>
						</div>
						<div class="pull-right">
							<?php echo $sysinfo['commentscount']; ?>
						</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div class="pull-left muted">
							<b>Файлы</b>
						</div>
						<div class="pull-right">
							<?php echo $sysinfo['filescount']; ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table class="items table table-bordered table-condensed">
			<thead>
					<tr>
						<th>Действия</th>
					</tr>
				</thead>
			<tbody>
				<tr class="odd">
					<td>
						<input type="button" class="btn btn-small btn-info bdaction span12" id="clearcache" value="Очистить кеш">
					</td>
				</tr>
				<tr class="odd">
					<td>
						<input type="button" class="btn btn-small btn-info bdaction span12" id="optimizedb" value="Оптимизация базы">
					</td>
				</tr>
				<!--
				<tr class="odd">
					<td>
						<div class="left muted">
							<b>Оптимизация таблицы банов</b>
						</div>
						<div class="right">
							<input type="button" class="btn btn-small btn-info bdaction" id="optimizebanstable" value="Ok">
						</div>
					</td>
				</tr>
				-->
				<tr class="odd">
					<td>
						<input type="button" class="btn btn-small btn-info bdaction span12" id="truncatebans" value="Очистить банлист">
					</td>
				</tr>
			</tbody>
		</table>
    </div>
  </div>
</div>
