<?php
/**
 * Вьюшка просмотра деталей бана
 */

/** @var Bans $model */
/** @var array $geo */
/** @var bool $ipaccess */
/** @var bool $canEditBan */
/** @var bool $canUnbanBan */
/** @var bool $canDeleteBan */
/** @var bool $canAddComment */
/** @var bool $canAddDemo */
/** @var string $playerSteam */
/** @var CActiveDataProvider $commentsProvider */
/** @var CActiveDataProvider $filesProvider */
/** @var CActiveDataProvider $historyProvider */
/** @var Comments $comments */
/** @var Files $files */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$page = 'Банлист';
$this->pageTitle = Yii::app()->name . ' - ' . $page . ' - Детали бана ' . $model->player_nick;
$this->breadcrumbs=array(
	$page=>array('index'),
	$model->player_nick,
);

Yii::app()->clientScript->registerScript('viewBan', "
    jQuery('body').on('click','a[data-action]',function(e){
        e.preventDefault();
        var actionLink = $(this),
            beforeSend;
        if(actionLink.data('confirm')) {
            beforeSend = function() {
                if(!confirm(actionLink.data('confirm'))) {
                    return false;
                }
            };
        }
        jQuery.ajax({
            'type':'post',
            'beforeSend':beforeSend,
            'success':function() {
                document.location.href='".$this->createUrl('/bans/index')."'
            },
            'url':actionLink.attr('href'),
            'cache':false
        });
        return false;
    }
);
", CClientScript::POS_END);

if($geo) {
	Yii::app()->clientScript->registerScriptFile('//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU',CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('yandexmap', "
		ymaps.ready(inityamaps);
		function inityamaps () {
			var myMap = new ymaps.Map('map', {center: [{$geo['lat']}, {$geo['lng']}], zoom: 10});
		}
	",CClientScript::POS_END);
}


?>

<h2>Подробности бана <i><?php echo CHtml::encode($model->player_nick); ?></i></h2>
<div style="float: right">
	<?php if($canEditBan):?>
        <a
            href="<?= $this->createUrl('/bans/update', array('id' => $model->bid))?>"
            rel="tooltip"
            title="Редактировать"><i class="icon-edit"></i></a>
	<?php endif;?>
	<?php if($canUnbanBan && !$model->unbanned):?>
        <a
            href="<?= $this->createUrl('/bans/unban', ['id' => $model->bid, 'ajax' => 1])?>"
            rel="tooltip"
            title="Разбанить"
            data-action="unban"
            data-confirm="Разбанить игрока?"><i class="icon-remove"></i></a>
	<?php endif;?>
	<?php if($canDeleteBan):?>
        <a
            href="<?= $this->createUrl('/bans/delete', ['id' => $model->bid, 'ajax' => 1])?>"
            rel="tooltip"
            title="Удалить"
            data-action="delete"
            data-confirm="Удалить бан?"><i class="icon-trash"></i></a>
	<?php endif;?>
</div>
<table class="table table-condensed table-bordered text-left">
    <tr class="odd">
        <th>IP игрока</th>
        <td>
            <?php if($geo):?>
                <a
                    href="#"
                    onclick="$('#modal-map').modal('show');"
                    rel="tooltip"
                    title="Подробности IP адреса"><?= CHtml::encode($model->player_ip)?></a>
            <?php else:?>
                <?= CHtml::encode($model->player_ip)?>
            <?php endif;?>
        </td>
    </tr>
    <tr class="even">
        <th>Steam  игрока</th>
        <td><?= $playerSteam?></td>
    </tr>
    <tr class="odd">
        <th>Ник игрока</th>
        <td><?= CHtml::encode($model->player_nick)?></td>
    </tr>
    <tr class="even">
        <th>Админ</th>
        <td><?= $model->adminName?></td>
    </tr>
    <tr class="odd">
        <th>Причина</th>
        <td><?= CHtml::encode($model->ban_reason)?></td>
    </tr>
    <tr class="even">
        <th>Дата</th>
        <td><?= date('d.m.Y - H:i:s', $model->ban_created)?></td>
    </tr>
    <tr class="odd">
        <th>Срок бана</th>
        <td>
            <?php if($model->ban_length == '-1'):?>
                Разбанен
            <?php else:?>
                <?= Prefs::date2word($model->ban_length)?>
                <?php if($model->unbanned):?>
                    (Истек)
                <?php elseif(Yii::app()->hasModule('billing')):?>
                    <a
                        href="<?= $this->createUrl('/billing/unban', ['id' => $model->primaryKey])?>"
                        class="btn btn-mini btn-success pull-right">Купить разбан</a>
                <?php endif;?>
            <?php endif;?>
        </td>
    </tr>
    <tr class="even">
        <th>Истекает</th>
        <td><?= $model->expiredTime?></td>
    </tr>
    <tr class="odd">
        <th>Название сервера</th>
        <td><?= CHtml::encode($model->server_name)?></td>
    </tr>
    <tr class="even">
        <th>Кики</th>
        <td><?= intval($model->ban_kicks)?></td>
    </tr>
</table>
<hr>
<p class="text-success">
	<i class="icon-calendar"></i>
	История банов
</p>
<div id="ban-history-grid" class="grid-view">
    <table class="items table table-bordered">
        <thead>
            <tr>
                <th>Ник игрока</th>
                <th>Steam  игрока</th>
                <?php if($ipaccess):?>
                    <th>IP игрока</th>
                <?php endif;?>
                <th>Дата</th>
                <th>Причина</th>
                <th>Срок бана</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($historyProvider['models'] as $item):?>
            <tr>
                <td>
                    <a href="<?= $this->createUrl("/bans/view", array("id" => $item->bid))?>"><?= CHtml::encode($item->player_nick)?></a>
                </td>
                <td>
                    <?= Prefs::steamLink($item->player_id)?>
                </td>
                <?php if($ipaccess):?>
                <td>
                    <?= $item->player_ip?>
                </td>
                <?php endif;?>
                <td>
                    <?= date("d.m.Y - H:i:s", $item->ban_created)?>
                </td>
                <td>
                    <?= CHtml::encode($item->ban_reason)?>
                </td>
                <td>
                    <?php if($item->ban_length == "-1"):?>
                        Разбанен
                    <?php else:?>
                        <?= Prefs::date2word($item->ban_length)?>
                        <?php if($item->expired == 1):?>
                            (истек)
                        <?php endif;?>
                    <?php endif;?>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

<hr>
<p class="text-success">
	<i class="icon-comment"></i>
	Комментарии
</p>
<div id="comments-grid" class="grid-view">
    <table class="items table table-bordered">
        <colgroup>
            <col style="width: 80px">
        </colgroup>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Комментарий</th>
                <?php if($ipaccess):?>
                    <th>Адрес</th>
                <?php endif;?>
                <th>E-mail</th>
                <?php if($canEditBan):?>
                    <th></th>
                <?php endif;?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($commentsProvider['models'] as $item):?>
            <tr id="<?= $item->id?>">
                <td>
                    <?= date("d.m.Y", $item->date)?>
                </td>
                <td>
                    <?= CHtml::encode($item->comment)?>
                </td>
                <td>
                    <?= CHtml::encode($item->name)?>
                </td>
                <?php if($ipaccess):?>
                <td>
                    <?= CHtml::encode($item->addr)?>
                </td>
                <?php endif;?>
                <td>
                    <?= CHtml::encode($item->email)?>
                </td>
                <?php if($canEditBan):?>
                <td>
                    <a
                        href="<?= $this->createUrl("/comments/update", array("id"=>$item->id, "bid" => $item->bid))?>"
                        rel="tooltip"
                        title="Редактировать"><i class="icon-pencil"></i></a>
                    <a
                        href="<?= $this->createUrl("/comments/delete", array("id"=>$item->id))?>"
                        rel="tooltip"
                        title="Удалить"><i class="icon-trash"></i></a>
                </td>
                <?php endif;?>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

<?php if($canAddComment):?>
	<div style="width: auto; margin: 0 auto">
        <button type="button" class="btn btn-small" onclick="$('#addcomment').slideToggle('slow');">Добавить комментарий</button>
	</div>
	<div style="width: 100%; display: none" id="addcomment">
		<?php echo CHtml::form('','post'); ?>
		<?php echo CHtml::errorSummary($comments); ?>
		<table class="table table-bordered">
			<tr>
				<td class="span4">
					<?php echo CHtml::activeLabel(
							$comments,
							'email'
						); ?>
				</td>
				<td class="span8">
					<?php
					echo CHtml::activeEmailField(
							$comments,
							'email',
							!Yii::app()->user->isGuest ?
							array(
								'value' => Yii::app()->user->email,
								'readonly' => 'readonly'
							)
							:
							''
						)
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo CHtml::activeLabel($comments, 'name'); ?>
				</td>
				<td>
					<?php
					echo CHtml::activeTextField(
							$comments,
							'name',
							!Yii::app()->user->isGuest ?
							array(
								'value' => Yii::app()->user->name,
								'readonly' => 'readonly'
							)
							:
							''
						)
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo CHtml::activeLabel($comments, 'comment'); ?>
				</td>
				<td>
					<?php
					echo CHtml::activeTextArea($comments, 'comment')
					?>
				</td>
			</tr>
			<?php if(CCaptcha::checkRequirements() && Yii::app()->user->isGuest):?>
			<tr>
				<td>
					<?php echo CHtml::activeLabelEx($comments, 'verifyCode')?>
				</td>
				<td>
					<?php echo CHtml::activeTextField($comments, 'verifyCode')?>
					<?php $this->widget('CCaptcha')?>
				</td>
			</tr>
			<?php endif?>
			<tr>
				<td colspan="2">
					<?php echo CHtml::submitButton($label = 'Сохранить'); ?>
				</td>
			</tr>
		</table>
		<?php echo CHtml::endForm(); ?>
	</div>
<?php endif?>
<hr />
<p class="text-success">
	<i class="icon-folder-open"></i>
	Файлы
</p>
<div id="files-grid" class="grid-view">
    <table class="items table table-bordered">
        <colgroup>
            <col style="width: 80px">
        </colgroup>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Демо</th>
                <th>Размер</th>
                <th>Комментарий</th>
                <th>Автор</th>
                <?php if($ipaccess):?>
                    <th>Адрес</th>
                <?php endif;?>
                <th>Скачек</th>
                <?php if($canEditBan):?>
                    <th></th>
                <?php endif;?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($filesProvider['models'] as $item):?>
            <tr id="<?= $item->id?>">
                <td>
                    <?= date("d.m.Y", $item->date)?>
                </td>
                <td>
                    <?= CHtml::encode($item->demo_real)?>
                </td>
                <td>
                    <?= Prefs::formatfilesize($item->file_size)?>
                </td>
                <td>
                    <?= CHtml::encode($item->comment)?>
                </td>
                <td>
                    <?= CHtml::encode($item->name)?>
                </td>
                <?php if($ipaccess):?>
                <td>
                    <?= CHtml::encode($item->addr)?>
                </td>
                <?php endif;?>
                <td>
                    <?= intval($item->down_count)?>
                </td>
                <td>
                    <a
                        href="<?= $this->createUrl("/files/download", array("id"=>$item->id))?>"
                        rel="tooltip"
                        title="Скачать"><i class="icon-download-alt"></i></a>
                    <?php if(Webadmins::checkAccess('bans_edit', $item->name)):?>
                    <a
                        href="<?= $this->createUrl("/files/update", array("id"=>$item->id))?>"
                        rel="tooltip"
                        title="Редактировать"><i class="icon-pencil"></i></a>
                    <a
                        href="<?= $this->createUrl("/files/delete", array("id"=>$item->id))?>"
                        rel="tooltip"
                        title="Удалить"><i class="icon-trash"></i></a>
                    <?php endif;?>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

<?php if($canAddDemo):?>
	<div style="width: auto; margin: 0 auto">
        <button type="button" class="btn btn-small" onclick="$('.addfile').slideToggle('slow');">Добавить файл</button>
	</div>
	<div style="width: 100%; display: none; margin: 0 auto" class="addfile">
		<?php echo CHtml::form('','post', array('id' => 'addfile-form', 'enctype'=>'multipart/form-data')); ?>
		<?php echo CHtml::errorSummary($files); ?>
		<table class="table table-bordered">
			<tr>
				<td class="span4">
					<?php echo CHtml::activeLabel(
							$files,
							'email'
						); ?>
				</td>
				<td class="span8">
					<?php
					echo CHtml::activeEmailField(
							$files,
							'email',
							!Yii::app()->user->isGuest ?
							array(
								'value' => Yii::app()->user->email,
								'readonly' => 'readonly'
							)
							:
							array()
						)
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo CHtml::activeLabel($files, 'name'); ?>
				</td>
				<td>
					<?php
					echo CHtml::activeTextField(
							$files,
							'name',
							!Yii::app()->user->isGuest ?
							array(
								'value' => Yii::app()->user->name,
								'readonly' => 'readonly'
							)
							:
							array()
						)
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo CHtml::activeLabel($files, 'demo_real'); ?>
				</td>
				<td>
					<?php echo CHtml::activeFileField($files, 'demo_real') ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo CHtml::activeLabel($files, 'comment'); ?>
				</td>
				<td>
					<?php echo CHtml::activeTextArea($files, 'comment'); ?>
				</td>
			</tr>
			<?php if(CCaptcha::checkRequirements() && Yii::app()->user->isGuest):?>
			<tr>
				<td>
					<?php echo CHtml::activeLabel($files, 'verifyCode'); ?>
				</td>
				<td>
					<?php echo CHtml::activeTextField($files, 'verifyCode') ?>
					<?php $this->widget('CCaptcha')?>
				</td>
			</tr>
			<?php endif?>
			<tr>
				<td colspan="2">
					<?php echo CHtml::submitButton('Сохранить'); ?>
				</td>
			</tr>
		</table>
		<?php echo CHtml::endForm(); ?>
	</div>
<?php endif;?>
<?php if($ipaccess): ?>
    <div
        id="modal-map"
        class="modal hide fade"
        style="width:860px; margin-left: -430px;height: 600px"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
            <h3>Информация об IP "<?php echo $model->player_ip ?>"</h3>
        </div>
        <div class="modal-body" style="min-height: 460px">
            <div id="map" style="width:800px; height:400px; margin: 0 auto"></div>
            <div style="top: -30px">
                <b>Страна: </b>
                <?php echo $geo['country'] ?>
                <br>
                <b>Регион: </b>
                <?php echo $geo['region'] ?>
                <br>
                <b>Город: </b>
                <?php echo $geo['city'] ?>
            </div>
        </div>
        <div class="modal-footer">
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'label'=>'Закрыть',
                'url'=>'#',
                'htmlOptions'=>array('data-dismiss'=>'modal'),
            )); ?>
        </div>
    </div>
<?php endif; ?>
