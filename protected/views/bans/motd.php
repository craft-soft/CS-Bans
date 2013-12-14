<!DOCTYPE html>
<?php
/**
 * MOTD окно. Показывается забаненному игроку в игре
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
?>
<html>
<head>
	<title>Детали бана</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
	<?php Yii::app()->bootstrap->registerResponsiveCss(); ?>
	<?php Yii::app()->clientScript->enableJavaScript = FALSE; ?>
	<style>
		#footer {
			background-color: #f5f5f5;
			height: 100px;
		}
		.container .credit {
			margin: 20px 0;
		}
	</style>
</head>
<body>
	<div class="container" style="padding-top: 20px">
		<table class="table table-bordered">
			<tr>
				<td>Ваш ник:</td>
				<td><?php echo $model->country; ?> <?php echo $model->player_nick; ?></td>
			</tr>
			<tr>
				<td>Ваш SteamID:</td>
				<td><?php echo $model->player_id; ?></td>
			</tr>
			<tr>
				<td>Истекает:</td>
				<td><?php echo Prefs::getExpired($model->ban_created,$model->ban_length); ?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>Срок бана:</td>
				<td class="banDuration<?php echo $model->ban_length == 0 ? 'Perm' : ''; ?>"><?php echo Prefs::date2word($model->ban_length); ?></td>
			</tr>
			<tr>
				<td>Причина:</td>
				<td><?php echo $model->ban_reason; ?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>Забанен админом:</td>
				<td><?php echo $show_admin ? $model->admin_nick : '<i>Информация засекречена</i>'; ?></td>
			</tr>
			<tr>
				<td>Забанен на сервере:</td>
				<td><?php echo $model->server_name; ?></td>
			</tr>
		</table>
	</div>
	<div id="footer">
		<div class="container">
			<p class="muted credit">
				&copy; <?php echo date('Y'); ?> 
				<?php echo CHtml::link('Craft-Soft Studio', 'http://craft-soft.ru', array('target' => '_blank'));  ?>
				<br />
				All Rights Reserved.
			</p>
		</div>
	</div>
</body>
</html>