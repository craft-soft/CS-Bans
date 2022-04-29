<?php
/**
 * @var Bans $model
 * Форма добавления/редактирования AmxModX админа
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */


Yii::app()->clientScript->registerScript('adminactions', '
	var days = $("#Amxadmins_days");
	var flags = $("#Amxadmins_flags");
	var password = $("#Amxadmins_password");
	var forever = $("#forever");
	var placeholder;

	if(days.val() == 0) {
		days.prop("disabled", true);
		forever.prop("checked", "checked");
	}

	forever.click(function() {
		if($(this).prop("checked")) {
			days.val("0");
			days.prop("readonly", true);
		} else {
			days.prop("readonly", false);
			days.val("30");
		}
	});

	flags.change(function(){
        $("#removePwd").prop("disabled", false);
		switch($(this).val()) {
			case "d":
				placeholder = "127.0.0.1";
				break;
			case "c":
				placeholder = "STEAM_0:0:00000000";
				break;
			case "a":
				placeholder = "";
				$("#removePwd").prop("disabled", true);
				break;
		}
		$("#Amxadmins_steamid").attr("placeholder", placeholder);
	});

	$("#flagsselector").click(function() {
		$("#flagsmodal").modal("show");
		return false;
	});

	$("#setFlags").click(function(){
		var finputs = [];
		$("input[id^=Amxadmins_accessflags]:checked").each(function(){
			finputs.push($(this).val());
		});
		$("#Amxadmins_access").val(finputs.join(""));
		$("#flagsmodal").modal("hide");
		return false;
	});
	$("#checkAllFlags").click(function(){
		if(!$(this).prop("checked"))
			$(".adminflag").prop("checked", false);
		else
			$(".adminflag:not(:last)").prop("checked", true);
	});
	$(".adminflag").bind("click", function(){
		var allCB	= $(".adminflag").size();
		var chCB	= $(".adminflag:checked").size();
		if(allCB == chCB)
			$("#checkAllFlags").prop("checked", true);
		else
			$("#checkAllFlags").prop("checked", false);
	});
    $("#removePwd").click(function(){
        if($(this).prop("checked")) {
            $("#Amxadmins_password").prop("disabled", true);
        } else {
            $("#Amxadmins_password").prop("disabled", false);
        }
    });
');

if(!$model->isNewRecord) {
    $model->flags = $model->flags[0];
}

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'amxadmins-form',
	'enableAjaxValidation'=>false,
));
	echo $form->errorSummary($model);
	if ($model->isNewRecord) {
        echo $form->errorSummary($webadmins);
    }

    echo $form->dropDownListRow(
		$model,
		'flags',
		Amxadmins::getAuthType(),
		array(
			'class' => 'span6',
			'maxlength'=>32,
		)
	);

	echo $form->textFieldRow($model,'nickname',array('class' => 'span6','maxlength'=>32));
	echo $form->textFieldRow($model,'steamid',array('class' => 'span6','maxlength'=>32));
	echo $form->textFieldRow($model,'username',array('class' => 'span6','maxlength'=>32));

    if(!$model->isNewRecord && $model->flags != 'a' && $model->password) {
        $htmlOptions = array(
            'append' => '<label>'.CHtml::checkBox('removePwd') . ' Удалить пароль</label>',
            'style' => 'width: 167px',
            'value' => isset($_POST['Amxadmins']['password']) ? CHtml::encode($_POST['Amxadmins']['password']) : '',
        );
    } else {
        $htmlOptions = array(
            'class' => 'span6',
            'value' => isset($_POST['Amxadmins']['password']) ? CHtml::encode($_POST['Amxadmins']['password']) : '',
        );
    }

	echo $form->passwordFieldRow(
		$model,
		'password',
		$htmlOptions
	);

	echo $form->textFieldRow(
		$model,
		'access',
		array(
			'style' => 'width: 233px',
			'append' => '<span id="flagsselector" style="cursor: pointer">Выбрать</span>'
		)
	);

	echo $form->textFieldRow($model,'icq',array('class' => 'span6',));
	echo $form->dropDownListRow($model,'ashow', array('Нет', 'Да'),array('class' => 'span6',));

	if($model->isNewRecord):
		echo $form->textFieldRow(
			$model,
			'days',
			array(
				'class' => 'span6',
				'value' => '30',
				'append' => '<label>'.CHtml::checkBox('', false, array('id' => 'forever')) . ' навсегда</label>'
			)
		);

		echo $form->checkBoxListRow(
			$model,
			'servers',
			CHtml::listData(
				Serverinfo::model()->findAll(),
				'id',
				'hostname'
			),
			array(
				'multiple'=>true
			)
		);

	else:
		if($model->expired != 0):
		echo $form->textFieldRow(
			$model,
			'long',
			array(
				'class' => 'span6',
				'disabled' => 'disabled'
			)
		);
	endif;?>
	<label for="Amxadmins_change">Изменить срок админки</label>
	<div class="row-fluid">
		<div class="span2">

			<label class="radio"><input id="Amxadmins_addtake_0" value="0" type="radio" name="Amxadmins[addtake]" checked /> Добавить</label>

			<?php if($model->long > 0): ?>

				<label class="radio"><input id="Amxadmins_addtake_1" value="1" type="radio" name="Amxadmins[addtake]" /> Забрать</label>

			<?php endif ?>

			<label class="radio"><input id="Amxadmins_addtake_2" value="2" type="radio" name="Amxadmins[addtake]"<?php echo $model->expired == 0 ? ' checked="checked"' : ''?> /> Навсегда</label>
		</div>
		<div class="offset2 span2">
			<div class="input-append pull-right" style="padding-top: 5px">
				<input class="input-small" name="Amxadmins[change]" id="Amxadmins_change" type="text" />
				<span class="add-on">Дней</span>
			</div>
		</div>
	</div>

	<?php
	endif;
	$this->beginWidget('bootstrap.widgets.TbModal',
		array(
			'id'=>'flagsmodal',
			'htmlOptions' => array(
				'style' => 'width: 600px; margin-left: -300px'
			)
	)); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal" rel="tooltip" data-placement="left" title="Закрыть">&times;</a>
		<h4>Выбор флагов доступа</h4>
	</div>
	<div class="modal-body">
		<label class="checkbox">
			<input type="checkbox" id="checkAllFlags"> Выбрать все (кроме z)
		</label>
		<?php
			echo $form->checkboxListRow($model, 'accessflags', Amxadmins::getFlags(), array('class' => 'adminflag'));
		?>
	</div>
	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Установить',
			'type'=>'primary',
			'htmlOptions'=>array(
				'id'=>'setFlags',
			),
		));
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Отмена',
			'url'=>'#',
			'htmlOptions'=>array(
				'data-dismiss'=>'modal',
			),
		)); ?>
	</div>
	<?php
	$this->endWidget();

	if($model->isNewRecord):?>
		<hr class="row-divider">
		<button class="btn btn-info" type="button" onclick="$('#webrights').slideToggle('slow');">Добавить WEB админа</button>
		<div id="webrights" style="display: none"><br>
			<?php echo $form->textFieldRow($webadmins,'username',array('class' => 'span6','size'=>32,'maxlength'=>32, 'value' => 'Будет использован ник Amx админа', 'disabled' => 'disabled'));?>
			<?php echo $form->passwordFieldRow($webadmins,'password',array('class' => 'span6','size'=>32,'maxlength'=>32, 'value' => '')); ?>
			<?php echo $form->textFieldRow($webadmins,'email',array('class' => 'span6','size'=>60,'maxlength'=>64)); ?>
			<?php echo $form->dropdownListRow($webadmins,'level', Levels::getList(), array('class' => 'span6')); ?>
		</div>
	<?php endif;?>


	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget();?>
