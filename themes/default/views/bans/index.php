<?php

/* @var $this BansController */
/* @var $models Bans[] */
/* @var $searchModel Bans */
/* @var $sort CSort */
/* @var $pagination CPagination */
/* @var $start intval */
/* @var $end intval */
/* @var $count intval */
/* @var $page intval */
/* @var $pages intval */

/**
 * Вьюшка главной страницы банов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name . ' - Банлист';

$this->breadcrumbs=array(
	'Банлист',
);

Yii::app()->prefs->registerGridAssets('bans-grid', $pagination);

Yii::app()->clientScript->registerScript('banlist', "
$(document).on('click', 'tr.bantr td:not(:last-child)', function(){
	$('#loading').show();
	var bid = $(this).closest('tr').prop('id').substr(4);
	$.post(
        '".$this->createUrl('/bans/bandetail')."',
        {'bid': bid},
        function(response){
            $('#loading').hide();
            $('#BanDetail div.modal-body').html(response);
            $('#BanDetail').modal('show');
        }
    );
});
$('.search-button').click(function(){
    $('.search-form').slideToggle(1500);
    return false;
});
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('bans-grid', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<div class="alert alert-<?php echo $check ? 'error' : 'success' ?>">
	<a href="#" class="close" data-dismiss="alert">&times;</a>
	<?php 
	$ip = $_SERVER['REMOTE_ADDR'];
	echo $check
			?
		'<strong>Внимание!</strong> Ваш IP (<strong>'.$ip.'</strong>) забанен'
			:
		'Ваш IP (<strong>'.$ip.'</strong>) не забанен'
	?>
</div>

<?php echo CHtml::link('Поиск','#',array('class'=>'search-button btn btn-small')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
    'model'=>$searchModel,
)); ?>
</div>
<div style="width: 100%" id="bans-grid" class="grid-view">
    <div class="summary">Показано с <?= $start?> по <?= $end?> банов из <?= $count?>. Страница <?= $page?> из <?= $pages?></div>
    <table class="items table table-striped table-bordered table-condensed">
        <col style="width: 70px">
        <col style="width: 180px">
        <col>
        <col>
        <col style="width: 130px">
        <thead>
            <tr>
                <th>Дата</th>
                <th>Ник</th>
                <th>Админ</th>
                <th>Причина</th>
                <th>Срок</th>
                <?php if(Yii::app()->config->show_comment_count):?>
                    <th>Комментарии</th>
                <?php endif;?>
                <?php if(Yii::app()->config->show_demo_count):?>
                    <th>Файлы</th>
                <?php endif;?>
                <?php if(Yii::app()->config->show_kick_count):?>
                    <th>Кики</th>
                <?php endif;?>
                <th class="button-column" id="bans-grid_c8">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($models as $key => $model):?>
            <tr id="ban_<?= $model->bid?>" class="bantr<?php if($model->unbanned == 1) echo ' success';?> <?= $key % 2 == 0 ? 'odd' : 'even'?>" style="cursor:pointer;">
                <td>
                    <?= Yii::app()->format->formatDate($model->ban_created)?>
                </td>
                <td>
                    <?= $model->country?>
                    <?= CHtml::encode(mb_substr($model->player_nick, 0, 18, "UTF-8"))?>
                </td>
                <td>
                    <?= CHtml::encode($model->admin_nick)?>
                </td>
                <td>
                    <?php if(mb_strlen($model->ban_reason, "UTF-8") > 25):?>
                        <?= CHtml::encode(mb_substr($model->ban_reason, 0, 25, "UTF-8"))?>...
                    <?php else:?>
                        <?= CHtml::encode($model->ban_reason)?>
                    <?php endif;?>
                </td>
                <td>
                    <?php if($model->ban_length == '-1'):?>
                        Разбанен
                    <?php else:?>
                        <?= Prefs::date2word($model->ban_length)?>
                        <?php if($model->expired == 1):?>
                            (истек)
                        <?php endif;?>
                    <?php endif;?>
                </td>
                <?php if(Yii::app()->config->show_comment_count):?>
                <td class="text-center">
                    <?= intval($model->commentsCount)?>
                </td>
                <?php endif;?>
                <?php if(Yii::app()->config->show_demo_count):?>
                <td class="text-center">
                    <?= intval($model->filesCount)?>
                </td>
                <?php endif;?>
                <?php if(Yii::app()->config->show_kick_count):?>
                <td class="text-center">
                    <?= intval($model->ban_kicks)?>
                </td>
                <?php endif;?>
                <td class="button-column text-center">
                    <a class="view" title="Просмотреть" rel="tooltip" href="<?= $this->createUrl('/bans/view', ['id' => $model->bid])?>"><i class="icon-eye-open"></i></a>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <div class="pagination">
        <?php $this->widget('bootstrap.widgets.TbPager', [
            'pages' => $pagination,
            'displayFirstAndLast' => true
        ])?>
    </div>
</div>

<?php $this->beginWidget('bootstrap.widgets.TbModal',
	array(
		'id'=>'BanDetail',
		'htmlOptions'=> array('style'=>' width: 600px; margin-left: -300px'),
)); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Подробности бана </h4>
</div>

<div class="modal-body" id="ban_name"></div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Закрыть',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
<?php $this->endWidget(); ?>