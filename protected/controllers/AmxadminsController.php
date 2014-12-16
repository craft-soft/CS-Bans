<?php
/**
 * Контроллер админов серверов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
class AmxadminsController extends Controller
{
	public $layout='//layouts/column2';

	public function filters()
	{
		return array(
			//'accessControl',
			'postOnly + delete',
		);
	}

	// Назначение админов
	public function actionAdminsonservers()
	{
		// Проверка прав
		if(!Webadmins::checkAccess('amxadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		$this->layout = '//layouts/main';

		// Сохранение параметров админа
		if(Yii::app()->request->isAjaxRequest && isset($_POST['sid']) && isset($_POST['aid'])) {

			$exit = "alert('Ошибка');";

			if(isset($_POST['active'])) {

				if(!empty($_POST['customflags']) && !preg_match('#^([a-z]+)$#', $_POST['customflags']))
					Yii::app()->end("alert('Ошибка!В флаги нужно прописывать только буквы латинского алфавита');");

				$adm = AdminsServers::model()->findByAttributes(array(
					'admin_id' => $_POST['aid'],
					'server_id' => $_POST['sid'],
				));

				if($adm === NULL) {
					$adm = new AdminsServers;
					$adm->admin_id = $_POST['aid'];
					$adm->server_id = $_POST['sid'];
				}

				$adm->flags = isset($_POST['customflags']) ? str_split($_POST['customflags']) : array();
				$adm->use_static_bantime = $_POST['staticbantime'];

				if($adm->save()) {
					$exit = "alert('Сохранено');";
				}
			}
			else {

				$admin = Amxadmins::model()->findByPk($_POST['aid']);

				$res = AdminsServers::model()->deleteAllByAttributes(array(
					'admin_id' => $_POST['aid'],
					'server_id' => $_POST['sid'],
				));

				if($res)
				{
					$hash = crc32($admin->steamid);
					$exit = "alert('Удалено'); $('.input{$hash}').hide();";
				}
			}

			Yii::app()->end($exit);
		}

		// Вывод админов для данного сервера
		elseif(Yii::app()->request->isAjaxRequest && isset($_POST['sid']) && is_numeric($_POST['sid']))
		{
			$sid = $_POST['sid'];
			$admins = Amxadmins::model()->sort()->findAll();
			$server = Serverinfo::model()->findByPk($sid);

			// Шапка таблицы с админами
			$js = "<table class=\"table table-bordered table-stripped\"><thead><tr class=\"info\"><th colspan=5>{$server->hostname}</th></tr></tr><tr><th>Ник</th><th>Steam/IP/Ник</th><th>Доп. флаги</th><th>Установленное время бана</th><th>Вкл</th><th>Сохранить</th></thead><tbody>";

			foreach($admins as $admin)
			{
				$customflags = NULL;
				$staticbantime = NULL;
				$active = NULL;
				$link = NULL;
				$checked = FALSE;
				$display = 'display: none';
				$name1 = NULL;
				$name2 = NULL;
				$class = $admin->expired && $admin->expired <= time() ? ' class="error"' : '';

				$s = AdminsServers::model()->findByAttributes(array(
					'admin_id' => $admin->id,
					'server_id' => $sid
				));

				if($s !== NULL)
				{
					$checked = TRUE;
					$display = NULL;
					$name1 = $s->custom_flags;
					$name2 = $s->use_static_bantime;
				}

				$customflags = CHtml::textField(
					'customflags',
					$name1,
					array(
						'class' => 'input' . $admin->id,
						'style' => $display
					)
				);

				$staticbantime = CHtml::dropDownList(
					'staticbantime',
					$name2,
					array('no' => 'Нет', 'yes' => 'Да'),
					array(
						'class' => 'input' . $admin->id,
						'style' => $display
					)
				);

				$link = CHtml::link(
					'<i class="icon-ok-sign"></i>',
					'#',
					array(
						//'onclick' => '$.post("", $("#form' . $admin->id . '").serialize(), function(data){ eval(data); }); return false;',
						'onclick' => '$.post("", $("#tr' . $admin->id . ' input, #tr' . $admin->id . ' select").serialize(), function(data){ eval(data); }); return false;',
						'rel' => 'tooltip',
						'title' => 'Сохранить'
					)
				);

				$js .= "<tr id='tr" . $admin->id . "'{$class}>";
				$js .= CHtml::form('', 'post', array('id' => 'form' . $admin->id));
				$js .= "<td id=\"nickname\">" . CHtml::encode($admin->nickname) . '</td>';
				$js .= "<td id=\"\">{$admin->steamid}</td>";
				$js .= "<td id=\"customflags\">{$customflags}</td>";
				$js .= "<td id=\"staticbantime\">{$staticbantime}</td>";
				$js .= "<td style=\"text-align: center; vertical-alifn: middle\">".
						CHtml::checkBox(
							'active',
							$checked,
							array(
								'id' => 'active' . $admin->id,
								'onclick' => 'checkaccess("'.$admin->id.'");'
							)
						).
						"</td>";
				$js .= "<td style=\"text-align: center; vertical-alifn: middle\">{$link}</td>";
				$js .= CHtml::hiddenField('aid', $admin->id);
				$js .= CHtml::hiddenField('sid', $sid);
				$js .= CHtml::endForm();
				$js .= "</tr>";
			}

			$js .= '</tbody></table>';
			Yii::app()->end($js);
		}

		$this->render('adminsonservers', array('servers'=>Serverinfo::model()->cache(600)->findAll(),));
	}

	/**
	 * Детали админа.
	 */
	public function actionView($id=NULL)
	{
		if(!Yii::app()->request->isAjaxRequest && $id !== NULL)
		{
			// Проверка прав
			if(!Webadmins::checkAccess('amxadmins_edit'))
				throw new CHttpException(403, "У Вас недостаточно прав");

			$model = $this->loadmodel($id);
			$this->render('view', array('model' => $model));
			Yii::app()->end();
		}

		$model = Amxadmins::model()->with('servers')->findByPk($_POST['aid']);
		if($model === null)
		{
			Yii::app()->end("alert('Ошибка!')");
		}

		$steam = '';

		// Если стимайди админа проходит проверку на валидность, получаем инфу об админе с вальве
		if(Prefs::validate_value($model->steamid))
		{
			if($url = @file_get_contents(Prefs::steam_convert($model->steamid, false, true)))
			{
				$xmlres = simplexml_load_string($url);
				$steam = CHtml::image($xmlres->avatarIcon) . " " . CHtml::link(
					$xmlres->steamID,
					"http://steamcommunity.com/profiles/" . $xmlres->steamID64,
					array(
						"target" => "_blank",
						"rel" => "tooltip",
						"title" => "Просмотреть профиль"
					)
				);
			}
		}

		$servers = '';

		// Если есть сервера у админа, выводим сервера
		if($model->servers)
		{
			foreach($model->servers as $server)
			{
				if(!$server->hostname) continue;
				$servers .= CHtml::link(
					$server->hostname,
					Yii::app()->createUrl(
						'/serverinfo/view',
						array(
							'id' => intval($server->id)
						)
					),
					array(
						'target' => '_blank'
					)
				)."<br>";
			}
		}

		// Формируем таблицу с инфой об админе
		$info = "<table class=\"table table-bordered\">";
		$info .= "<tr>";
		$info .= "<td><b>Ник</b></td>";
		$info .= "<td>".CHtml::encode($model->nickname)."</td>";
		$info .= "</tr><tr>";
		$info .= "<td><b>Контакты</b></td>";
		$info .= "<td>" . ($model->icq ? CHtml::image("//icq-rus.com/icq/3/".$model->icq.".gif"). " " . $model->icq : 'Не задан') . "</td>";
		$info .= "</tr><tr>";
		$info .= "<td><b>Доступ</b></td>";
		$info .= "<td>".$model->access."</td>";
		$info .= "</tr><tr>";
		$info .= "<td><b>Добавлен</b></td>";
		$info .= "<td>".date("d.m.Y - H:i:s", $model->created)."</td>";
		$info .= "</tr><tr>";
		$info .= "<td><b>Истекает</b></td>";
		$info .= "<td>" . ($model->expired != 0 ? date("d.m.Y - H:i:s", $model->expired) : "Никогда") . "</td>";
		$info .= "</tr>";
		$info .= "</table>";
		$js  = "$('#adminInfo').html('".$info."');";
		$js .= "$('#adminSteam').html('".($steam ? $steam : '<i>Информация отсутствует</i>')."');";
		$js .= "$('#adminServers').html('".($model->servers ? $servers : '<i>Информация отсутствует</i>')."');";
		$js .= "$('#loading').hide();";
		$js .= "$('#adminDetail').modal('show');";
		// Выводим инфу
		Yii::app()->end($js);
	}

	/**
	 * Добавить нового админа
	 */
	public function actionCreate()
	{
		if(!Webadmins::checkAccess('amxadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		$model=new Amxadmins;

		// Аякс проверка формы
		$this->performAjaxValidation($model);

		if(isset($_POST['Amxadmins']))
		{
			$model->attributes=$_POST['Amxadmins'];

			if(isset($_POST['Webadmins']) && $model->validate())
			{
				$wa = new Webadmins;
				$wa->attributes = $_POST['Webadmins'];
				$wa->username = $_POST['Amxadmins']['nickname'];
				$wa->save();
			}

			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Редактировать админа
	 * @param integer $id ID админа
	 */
	public function actionUpdate($id)
	{
		if(!Webadmins::checkAccess('amxadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		$model=$this->loadModel($id);

		// Аякс проверка формы
		$this->performAjaxValidation($model);

		if(isset($_POST['Amxadmins']))
		{
			$model->attributes=$_POST['Amxadmins'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Удаление админа
	 * @param integer $id ID админа
	 */
	public function actionDelete($id)
	{
		if(!Webadmins::checkAccess('amxadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		$this->loadModel($id)->delete();

		// Если не аякс запрос, то перенаправляем
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Главная страница админов серверов
	 */
	public function actionIndex()
	{
		$this->layout='//layouts/column1';

		// Вытаскиваем админов через датапровайдер
		$dependecy = new CDbCacheDependency('SELECT MAX(`created`), MAX(`expired`) FROM {{amxadmins}}');

		$admins=new CActiveDataProvider(Amxadmins::model()->cache(300, $dependecy), array(
			'criteria'=>array(
				// Выводим только тех, кого разрешено ваыводить
				'condition' => "`ashow`=1 AND (`expired` = 0 OR `expired` > UNIX_TIMESTAMP())",
				'order' => '`expired` DESC, `nickname` ASC',
			),
			'pagination' => array(
				'pageSize' => Yii::app()->config->bans_per_page,

			)
		));
		//$servers = new CActiveDataProvider('Serverinfo');
		$this->render('index',array(
			'admins'=>$admins,
			//'servers'=>$servers,
		));
	}

	/**
	 * Управление админами серверов
	 */
	public function actionAdmin()
	{
		if(!Webadmins::checkAccess('amxadmins_view'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		$model=new Amxadmins('search');
		$model->unsetAttributes();
		if(isset($_GET['Amxadmins']))
			$model->attributes=$_GET['Amxadmins'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Загрузка модели по ID
	 * Если не найдено, выводим эксепшн
	 * @param integer ID админа
	 */
	public function loadModel($id)
	{
		$model=Amxadmins::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * AJAX проверка формы
	 */
	protected function performAjaxValidation($model, $id='amxadmins-form')
	{
		if(isset($_POST['ajax']) && $_POST['ajax']===$id)
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
