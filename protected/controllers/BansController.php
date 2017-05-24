<?php
/**
 * Контррллер банов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
class BansController extends Controller
{
	public $layout='//layouts/column1';

	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
			'ajaxOnly + bandetail, unban'
		);
	}

	public function actions(){
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
            ),
        );
    }

	public function actionUnban($id)
	{
		$model = $this->loadModel($id);

		// Проверка прав
		if (!Webadmins::checkAccess('bans_unban', $model->admin_nick)) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $model->ban_length = '-1';
		$model->expired = 1;

		if ($model->save(FALSE)) {
            Yii::app()->end('Игрок разбанен');
        }

        Yii::app ()->end(CHtml::errorSummary($model));
	}

    /**
     * Вывод инфы о бане
     * @param integer $id ID бана
     * @throws CHttpException
     */
	public function actionView($id)
	{
		// Подгружаем комментарии и файлы
		$files = new Files;
		//$this->performAjaxValidation($files);
		$files->unsetAttributes();
		$comments = new Comments;
		$comments->unsetAttributes();

		// Подгружаем баны
		$model=Bans::model()->with('admin')->findByPk($id);
		if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
		$geo = false;
		// Проверка прав на просмотр IP
		$ipaccess = Webadmins::checkAccess('ip_view');
		if($model->player_ip && $ipaccess) {
            $cacheKey = md5('player_ip_'.$model->player_ip);
            $cache = Yii::app()->cache->get($cacheKey);
            if($cache) {
                $geo = $cache;
            } else {
                $geo = array(
                    'city' => 'Н/А',
                    'region' => 'Не определен',
                    'country' => 'Не определен',
                    'lat' => 0,
                    'lng' => 0,
                );
                $client = new \GuzzleHttp\Client([
                    'connect_timeout' => 1,
                    'base_uri' => 'http://ipgeobase.ru:7020'
                ]);
                try {
                    $response = $client->get('/geo', [
                        'query' => ['ip' => $model->player_ip]
                    ]);
                } catch(\GuzzleHttp\Exception\RequestException $e) {
                    return $geo;
                }

                if($response instanceof \GuzzleHttp\Psr7\Response && $response->getStatusCode() === 200) {
                    $content = (string)$response->getBody();
                } else {
                    return $geo;
                }

                if($content) {
                    $xml = @simplexml_load_string($content);
                    if(!empty($xml->ip)) {
                        $geo['city'] = (string)$xml->ip->city;
                        $geo['region'] = (string)$xml->ip->region;
                        $geo['country'] = (string)$xml->ip->country;
                        $geo['lat'] = (string)$xml->ip->lat;
                        $geo['lng'] = (string)$xml->ip->lng;
                        Yii::app()->cache->set($cacheKey, $geo);
                    }
                }
            }
		}

		// Добавление файла
		if(isset($_POST['Files']) && !empty($_POST['Files']['name'])) {
			// Задаем аттрибуты
			$files->attributes = $_POST['Files'];
			$files->bid = intval($id);
			$files->save();
			$this->refresh();
		}

		// Добавление комментария
		if(isset($_POST['Comments'])) {
			//exit(print_r($_POST['Comments']));
			$comments->attributes = $_POST['Comments'];
			$comments->bid = $id;
			if ($comments->save()) {
                $this->refresh();
            }
        }

		// Выборка комментариев
		$c = new CActiveDataProvider($comments, array(
			'criteria' => array(
				'condition' => 'bid = :bid',
				'params' => array(
					':bid' => $id
				),
			),
		));

		// Выборка файлов
		$f = new CActiveDataProvider(Files::model(), array(
			'criteria' => array(
				'condition' => 'bid = :bid',
				'params' => array(
					':bid' => $id
				),
			),
		));
		
		// История банов
		$history = new CActiveDataProvider('Bans', array(
			'criteria' => array(
				'condition' => '`bid` <> :hbid AND (`player_ip` = :hip OR `player_id` = :hid)',
				'params' => array(
					':hbid' => $id,
					':hip' => $model->player_ip,
					':hid' => $model->player_id
				),
			),
			'pagination' => array(
				'pageSize' => 5
			)
		));

		// Вывод всего на вьюху
		$this->render('view',array(
			'geo' => $geo,
			'ipaccess' => $ipaccess,
			'model'=>$model,
			'files' => $files,
			'comments'=> $comments,
			'filesProvider' => Prefs::dataFromProvider($f),
			'commentsProvider' => Prefs::dataFromProvider($c),
			'historyProvider' => Prefs::dataFromProvider($history),
            'canEditBan' => Webadmins::checkAccess('bans_edit', $model->admin_nick),
            'canUnbanBan' => Webadmins::checkAccess('bans_unban', $model->admin_nick),
            'canDeleteBan' => Webadmins::checkAccess('bans_delete', $model->admin_nick),
            'canAddComment' => Yii::app()->config->use_comment && (!Yii::app()->user->isGuest || Yii::app()->config->comment_all),
            'canAddDemo' => Yii::app()->config->use_demo && (!Yii::app()->user->isGuest || Yii::app()->config->demo_all),
            'playerSteam' => Prefs::steam_convert($model->player_id, TRUE)
				? CHtml::link($model->player_id, 'http://steamcommunity.com/profiles/'
						. Prefs::steam_convert($model->player_id), array('target' => '_blank'))
				: $model->player_id
		));
	}

	/**
	 * Добавить бан
	 */
	public function actionCreate()
	{
		// Проверка прав
		if (!Webadmins::checkAccess('bans_add')) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $model=new Bans;

		// Аякс проверка формы
		$this->performAjaxValidation($model);

		if(isset($_POST['Bans'])) {
			$model->attributes=$_POST['Bans'];
			$model->admin_nick = 'Web';
			$model->server_name = 'Забанен с сайта';
			if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->bid));
            }
        }

		$this->render('create',array(
			'model'=>$model,
		));
	}

    /**
     * Редактировать бан
     * @param integer $id ID бана
     * @throws CHttpException
     */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Проверка прав
		if (!Webadmins::checkAccess('bans_edit', $model->admin_nick)) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        // Аякс проверка формы
		// $this->performAjaxValidation($model);

		// Сохраняем форму
		if(isset($_POST['Bans'])) {
			$model->attributes=$_POST['Bans'];
			if(isset($_POST['selfreasoncheckbox']) && isset($_POST['self_ban_reason'])) {
				$model->ban_reason = $_POST['self_ban_reason'];
			}
			if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->bid));
            }
        }

		$this->render('update',array(
			'model'=>$model,
		));
	}

    /**
     * Удаление бана
     * @param integer $id ID бана
     * @throws CHttpException
     */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id);

		// Проверка прав
		if (!Webadmins::checkAccess('bans_delete', $model->admin_nick)) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $model->delete();
		// Если не аякс запрос, то редиректим
		if (!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
    }

	/**
	 * Вывод всех банов
	 */
	public function actionIndex()
	{
		$model=new Bans('search');
		$model->unsetAttributes();
		if (isset($_GET['Bans'])) {
            $model->attributes = $_GET['Bans'];
        }

        $select = "((ban_created+(ban_length*60)) > UNIX_TIMESTAMP() OR ban_length = 0) AND `expired` = 0";

        /* @var $dataProvider CActiveDataProvider */
        if(isset($_GET['Bans'])) {
            $dataProvider = $model->search();
        } else {
            $dataProvider=new CActiveDataProvider('Bans', array(
                'criteria'=>array(
                    'condition' => Yii::app()->config->auto_prune
                        ?
                    $select
                        :
                    null,
                    'order' => '`ban_created` DESC',
                ),
                'pagination' => array(
                    'pageSize' =>  Yii::app()->config->bans_per_page,

                ),)
             );
        }
        $clientIp = Prefs::getRealIp();
		// Проверяем IP посетителя, есть ли он в активных банах
		$check = Bans::model()->count(
			"`player_ip` = :ip AND " . $select,
			array(
				':ip'=> $clientIp,
			)
		);

		$this->render('index',array_merge(Prefs::dataFromProvider($dataProvider), array(
			'searchModel'=>$model,
            'clientIp' => $clientIp,
            'clientFindedInBans' => $check > 0
		)));

	}

	/**
	 * Управление банами (Хз, буду ли использовать)
	 */
	public function actionAdmin()
	{
		if (Yii::app()->user->isGuest) {
            throw new CHttpException(403, "У Вас недостаточно прав");
        }

        $model=new Bans('search');
		$model->unsetAttributes();
		if (isset($_GET['Bans'])) {
            $model->attributes = $_GET['Bans'];
        }

        $this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Вывод данных о бане в модальке
	 */
	public function actionBandetail()
	{
        if(!is_numeric($_POST['bid'])) {
            throw new CHttpException(400, 'Не передан bid');
        }
        $model = Bans::model()->with('admin')->findByPk($_POST['bid']);
        if($model === null) {
            $response = 'Ошибка. Бан не найден';
        } else {
            $response = $this->renderPartial('_ban_modal', [
                'model' => $model
            ]);
        }
        Yii::app()->end($response);
	}

	public function actionMotd($sid, $adm = 0, $lang = 'ru')
	{
		$this->layout = FALSE;

		$sid = (int)SubStr( $sid, 1 );

		$model = Bans::model()->findByPk($sid);
		if ($model === null) {
            Yii::app()->end('Error!');
        }

        $this->render('motd', array(
			'model'=>$model,
			'show_admin' => $adm == 1 ? TRUE : FALSE
		));
	}

    /**
     * Загрузка модели по ID
     * @param $id
     * @return static
     * @throws CHttpException
     * @internal param ID $integer бана
     */
	public function loadModel($id)
	{
		$model=Bans::model()->findByPk($id);
		if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
	}

    /**
     * Аякс проверка формы
     * @param $model
     */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='bans-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
