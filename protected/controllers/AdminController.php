<?php
/**
 * Контроллер админцентра
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

class AdminController   extends Controller
{

    public $layout='//layouts/main';

    public function filters()
    {
		return array(
			'accessControl',
			'postOnly + delete',
			'ajaxOnly + actions, version',
		);
    }

	public function actionVersion() {
		if(isset($_POST['version']))
			Yii::app ()->end(Prefs::getVersion());
		Yii::app ()->end('1');
	}


	/**
	 * Причины банов
	 * @throws CHttpException
	 */
	public function actionReasons()
	{
		// Проверка прав
		if(Yii::app()->user->isGuest)
			throw new CHttpException(403, "У Вас недостаточно прав");

		// Задаем лайоут
		$this->layout = '//layouts/column2';

		// Вывод модальки с деталями группы
		if(isset($_POST['groupid']))
		{
			$gid = intval($_POST['groupid']);

			// Выбираем все причины
			$reasons = Reasons::model()->findAll();

			// Выбираем группу причин по ID
			$group = ReasonsSet::model()->findByPk($gid);

			$js = CHtml::form('', 'post', array('id' => 'form' . $gid));

			$js .= "<table class=\"table table-bordered table-condensed\"><thead><tr><th colspan=3 style=\"vertical-align: middle\">Название группы ".CHtml::textField('groupname', $group->setname)."</th></tr><tr><th>Причина</th><th>Срок бана</th><th>Действия</th></thead><tbody>";

			foreach ($reasons as $reason)
			{
				$server = ReasonsToSet::model()->findByAttributes(array(
					'setid' => $gid,
					'reasonid' => $reason->id
				));

				$js .= "<tr id=\"reason{$reason->id}\">";
				$js .= "<td>";
				$js .= CHtml::encode($reason->reason);
				$js .= "</td>";
				$js .= "<td>";
				$js .= CHtml::encode($reason->static_bantime);
				$js .= "</td>";
				$js .= "<td>";
				$js .= CHtml::checkBox('active[]', $server !== NULL ? TRUE : FALSE, array('value' => $reason->id, 'id' => 'active' . $reason->id));
				$js .= "</td>";
				$js .= "</tr>";
			}

			$js .= "<tr><td colspan=3 style=\"text-align: center\">";
			$js .= CHtml::hiddenField('gid', $gid);
			$js .= CHtml::button('Сохранить', array('class' => 'btn btn-primary save','onclick' => '$.post(\'\', $(\'#form'.$gid.'\').serialize(), function(data){eval(data);});'));
			$js .= "</td></tr>";
			$js .= "</tbody></table>";
			$js .= CHtml::endForm();

			Yii::app()->end("$('#loading').hide();$('.modal-header').html('<h2>Причины банов для группы \"{$group->setname}\"</h2>');$('.modal-body').html('{$js}');$('#reasons-modal').modal('show');");
		}

		// Сохранение деталей группы
		if(isset($_POST['active']))
		{
			//Yii::app()->end($_POST['gid']);
			// Если не выбрали причины
			if(empty($_POST['active']))
				Yii::app()->end("alert('Выберите причины!');");

			// Если не введено название группы
			if(empty($_POST['groupname']))
				Yii::app()->end("alert('Введите название группы!');");

			// Ищем группу
			$set = ReasonsSet::model()->findByPk($_POST['gid']);
			// Если группы нет, возвращаем ошибку
			if($set === NULL)
				Yii::app()->end("alert('Ошибка! Группа причин под ID ".intval($_POST['gid'])." не найдена');");

			// Если изменено название группы, то обновляем в базе и на странице
			$other = "";
			if($_POST['groupname'] != $set->setname)
			{
				$set->setname = CHtml::encode ($_POST['groupname']);
				if($set->save())
					$other = "$('#rgroup{$_POST['gid']}').children('td:first').html('".CHtml::encode ($_POST['groupname'])."');";
				//Yii::app()->end(var_dump($set->errors()));
			}

			// Ищем и предварительно удаляем все записи для этой группы причин
			if(ReasonsToSet::model()->findAllByAttributes(array('setid' => intval($_POST['gid']))))
				ReasonsToSet::model()->deleteAllByAttributes(array('setid' => intval($_POST['gid'])));

			// Циклим и записываем в базу новые причины для этой группы
			foreach($_POST['active'] as $r)
			{
				$rts = new ReasonsToSet;
				$rts->setid = intval($_POST['gid']);
				$rts->reasonid = intval($r);
				if($rts->save())
					$rts->unsetAttributes();
			}

			Yii::app()->end($other . "$('#reasons-modal').modal('hide');alert('Сохранено!');");
		}

		$this->render('reasons', array(
			'reasons' => new CActiveDataProvider('Reasons', array('criteria' => array('order' => '`static_bantime` DESC'))),
			'reasonsset' => new CActiveDataProvider('ReasonsSet')
		));
	}

	/**
	 * Аякс действия с базой (Кнопки на главной странице админцентра)
	 * @throws CHttpException
	 */
	public function actionActions()
	{
		if(!Webadmins::checkAccess('prune_db'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		switch ($_POST['action'])
		{
			case 'truncatebans':
				Yii::app()->db->createCommand()->truncateTable('{{bans}}');
				Yii::app()->end("$('#loading').hide();alert('Таблица банов успешно очищена');");

			case 'clearcache':
				$dir = ROOTPATH."/assets";
				self::removeDirRec($dir);
				Yii::app()->cache->flush();
				Yii::app()->end("$('#loading').hide();alert('Кэш очищен');");

			case 'optimizedb':
				$query = Yii::app()->db->createCommand("SHOW TABLES FROM `" . Yii::app()->params['dbname']. "` LIKE '".Yii::app()->db->tablePrefix."%'")->queryAll();
				$tables = "";
				foreach($query as $tmp) {
					foreach ($tmp as $key=>$val)
						$tables.=($tables != "" ? "," : "")."`".$val."`";
				}
				$optimize = Yii::app()->db->createCommand("OPTIMIZE TABLES ".$tables)->query();
				$alert = $optimize ? "База оптимизирована" : "Ошибка оптимизации";
				Yii::app()->end("$('#loading').hide();alert('". $alert."');");

			case 'optimizebanstable':
				//$query=mysql_query("SELECT ba.bid,ba.ban_created,ba.ban_length,se.timezone_fixx FROM ".$config->db_prefix."_bans as ba
				//			LEFT JOIN ".$config->db_prefix."_serverinfo AS se ON ba.server_ip=se.address WHERE ba.expired=0");
				$query = Yii::app()->db->createCommand()
						->select("ba.bid,ba.ban_created,ba.ban_length,se.timezone_fixx")
						->from('{{bans}} ba')
						->leftJoin("{{serverinfo}} se", "ba.server_ip=se.address")
						->where("ba.expired=0")->queryAll(true);

				$prunecount="";

				foreach($query as $tmp=>$val)
				{
					//foreach($tmp as $result=>$val)
					//{
						$prunecount.= $val;
					//}
				}

				//($result = $query) {
					//prune expired bans
					/*
					if(($result->ban_created + ($result->timezone_fixx * 60 * 60) + ($result->ban_length * 60)) < time() && $result->ban_length != "0") {
						$prunecount++;
						$prune_query = mysql_query("UPDATE `".$config->db_prefix."_bans` SET `expired`=1 WHERE `bid`=".$result->bid);
						$prune_query = mysql_query("INSERT INTO `".$config->db_prefix."_bans_edit` (`bid`,`edit_time`,`admin_nick`,`edit_reason`) VALUES (
										'".$result->bid."','".($result->ban_created + ($result->timezone_fixx * 60 * 60) + ($result->ban_length * 60))."','amxbans','Bantime expired')");
					}
					 *
					 */
					//$prunecount[] = $result;
				//}
				Yii::app()->end("$('#loading').hide();alert('". $prunecount['']."');");
		}
	}

	/**
	 * Добавить бан онлайн
	 * @throws CHttpException
	 */
	public function actionAddbanonline()
	{
		if(!Webadmins::checkAccess('bans_add'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		if(isset($_POST['sid']) && Yii::app()->request->isAjaxRequest)
		{
			$sid = intval($_POST['sid']);

			$server = Serverinfo::model()->findByPk($sid);
			
			$info = $server->getInfo();

			$js = "";

			if(!$info['players'])
				$js .= "<tr class=\"error\"><td colspan=\"7\">Нет игроков</td></tr>";
			elseif(!$players = $server->getPlayersInfo())
				$js .= "<tr class=\"error\"><td colspan=\"7\">Ошибка получения информации (проверьте RCON пароль)</td></tr>";
			else {
				foreach($players as $key => $player)
				{
					$key = $key + 1;
					if(!$player) continue;
					$js .= "<tr>";
					$js .= "<td>".intval($key)."</td>";
					$js .= "<td>".CHtml::encode($player['nick'])."</td>";
					$js .= "<td>".CHtml::encode($player['steamid'])."</td>";
					$js .= "<td>".CHtml::encode($player['ip'])."</td>";
					$js .= "<td>".intval($player['userid'])."</td>";
					$js .= "<td>".CHtml::encode($player['playertype'])."</td>";
					$js .= "<td style=\"text-align: center\">";
					$js .= CHtml::link(
						"<i class=\"icon-remove\"></i>",
						"#",
						array(
							"onclick" => "playeraction('ban', ".intval($player['userid']).", {$sid})",
							"rel" => "tooltip",
							"title" => "Забанить"
						)
					);
					$js .= "&nbsp";
					$js .= "&nbsp";
					$js .= "&nbsp";
					$js .= CHtml::link(
						"<i class=\"icon-warning-sign\"></i>",
						"#",
						array(
							"onclick" => "playeraction('kick', ".intval($player['userid']).", {$sid})",
							"rel" => "tooltip",
							"title" => "Кикнуть"
						)
					);
					$js .= "</td>";
					$js .= "</tr>";
				}
			}

			Yii::app()->end("$('#loading').hide();$('#players').html('".$js."');");
		}

		$this->render('addbanonline', array(
			'servers'=>Serverinfo::model()->cache(600)->findAll(),
		));
	}

	/**
	 * Главная страница админцентра
	 * @throws CHttpException
	 */
	public function actionIndex()
    {
		// Если гость, выдаем эксепшн
		if(Yii::app()->user->isGuest)
			throw new CHttpException(403, 'У Вас недостаточно прав');

        $this->render(
			'index',
			array(
				'sysinfo'=>array(
					// Всего банов
					'bancount' => Bans::model()->cache(300)->count(),
					// Активные баны
					'activebans' => Bans::model()->cache(300)->count(),
					// Всего файлов
					'filescount' => Files::model()->cache(300)->count(),
					// Всего комментариев
					'commentscount' => Comments::model()->cache(300)->count()
				)
			)
		);
    }

	/**
	 * Управление серверами
	 * @throws CHttpException
	 */
    public function actionServers()
    {
		if(Yii::app()->user->isGuest)
			throw new CHttpException(403, 'У Вас недостаточно прав');

		$model=new Serverinfo('search');
		$model->unsetAttributes();
		if(isset($_GET['Serverinfo']))
			$model->attributes=$_GET['Serverinfo'];

        $servers=new CActiveDataProvider('Serverinfo', array(
			'pagination' => array(
				'pageSize' => Yii::app()->config->bans_per_page,
			))
		 );
        $this->render('servers',array(
			'servers'=>$servers,
			'model' => $model,
        ));
    }

	/**
	 * Настройки сайта
	 * @throws CHttpException
	 */
	public function actionWebsettings()
	{
		// Проверяем права пользователя
		if(!Webadmins::checkAccess('websettings_view'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		// Вытаскиваем модель
		$model =  Webconfig::getCfg();

		$themes = array();

		// Ищем папки тем в themes
		foreach(glob(ROOTPATH . '/themes/*') as $t)
		{
			$themes[basename($t)] = basename($t);
		}

		if(isset($_POST['Webconfig']))
		{
			if(!Webadmins::checkAccess('websettings_edit'))
				throw new CHttpException(403, "У Вас недостаточно прав");

			$model->attributes=$_POST['Webconfig'];
			if($model->save())
				$this->redirect(array('websettings'));
		}

		$this->render('websettings',array(
			'model'=>$model,
			'themes'=>$themes,
		));
	}

	/**
	 * Удаляет файлы и папки рекурсивно из папки $dir
	 * @param string $dir Имя папки, в которой нужно удалить файлы и папки рекурсивно
	 */
	private function removeDirRec($dir)
	{
		if ($objs = glob($dir."/*")) {
			foreach($objs as $obj) {
				if(basename($obj)=='.' || basename($obj)=='..')
					continue;
				if(is_dir($obj))
				{
					self::removeDirRec($obj);
					@rmdir($obj);
				}
				else
					@unlink($obj);
			}
		}
	}
}

?>
