<?php 
/**
 * Форма поиска бана
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

Yii::app()->clientScript->registerScript('bansearch', '
	$("#Bans_server_ip").change(function(){
		$.post("'.Yii::app()->createUrl('/bans/index').'", {"server": $(this).val()}, function(data) {eval(data);});
		return false;
	});
');

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array( 
    'action'=>Yii::app()->createUrl($this->route), 
    'method'=>'get', 
));
?>

    <?php echo $form->textFieldRow($model,'player_nick',array('maxlength'=>100)); ?>

	<?php echo $form->textFieldRow($model,'player_id',array('maxlength'=>20)); ?>

    <?php echo $form->textFieldRow($model,'player_ip',array('maxlength'=>15)); ?>

	<?php echo $form->textFieldRow($model,'ban_reason',array('maxlength'=>100)); ?>

	<label for="Bans_ban_created" class="required">Дата бана</label>
	<?php 
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'id' => 'ban_created',
				'attribute' => 'ban_created',
				'language' => 'ru',
				'i18nScriptFile' => 'jquery-ui-i18n.min.js',
				'htmlOptions' => array(
					'id' => 'ban_created',
					'size' => '10',
				),
				'options' => array(
					'showAnim'=>'fold',
				)
			)
		)
	?>

    <div class="form-actions"> 
        <?php $this->widget('bootstrap.widgets.TbButton', array( 
            'buttonType'=>'submit', 
            'type'=>'primary', 
            'label'=>'Искать', 
        )); ?>
    </div> 

<?php $this->endWidget(); ?>