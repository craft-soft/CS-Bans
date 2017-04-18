<?php

/* @var $this BansController */
/* @var $model Bans */

?>
<table class="items table table-bordered table-condensed" style="width:500px; margin: 0 auto">
	<tr class="odd">
		<td class="span3">
			<b>Ник</b>
		</td>
		<td class="span6" id="bandetail-nick"><?= CHtml::encode($model->player_nick)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>Steam ID</b>
		</td>
		<td id="bandetail-steam"><?= CHtml::encode($model->player_id)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>Steam Community</b>
		</td>
		<td id="bandetail-steamcommynity"><?= Prefs::steam_convert($model->player_id, true)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>IP адрес</b>
		</td>
		<td id="bandetail-ip">
            <?php if(Webadmins::checkAccess('ip_view')):?>
                <?= CHtml::encode($model->player_ip)?>
            <?php else:?>
                Скрыт
            <?php endif;?>
        </td>
	</tr>
	<tr class="odd">
		<td>
			<b>Тип бана</b>
		</td>
		<td id="bandetail-type"><?= Prefs::getBanType($model->ban_type)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>Причина</b>
		</td>
		<td id="bandetail-reason"><?= CHtml::encode($model->ban_reason)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>Дата/Время</b>
		</td>
		<td id="bandetail-datetime"><?= Yii::app()->format->formatDatetime($model->ban_created)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>Срок</b>
		</td>
		<td id="bandetail-expired">
            <?php if($model->ban_length == '-1'):?>
                Разбанен
            <?php else:?>
                <?= Prefs::date2word($model->ban_length)?>
                <?php if($model->expired == 1):?>
                    (истек)
                <?php endif;?>
            <?php endif;?>
		</td>
	</tr>
	<tr class="odd">
		<td>
			<b>Админ</b>
		</td>
		<td id="bandetail-admin"><?= CHtml::encode($model->adminName)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>Сервер</b>
		</td>
		<td id="bandetail-server"><?= CHtml::encode($model->server_name)?></td>
	</tr>
	<tr class="odd">
		<td>
			<b>Кол-во киков</b>
		</td>
		<td id="bandetail-kicks"><?= intval($model->ban_kicks)?></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center">
            <a href="<?= $this->createUrl('/bans/view', array('id' => $model->bid))?>" class="btn">Показать подробности</a>
		</td>
	</tr>
</table>