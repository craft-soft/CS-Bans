<?php
/**
 * Контроллер сайта
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
class SiteController extends Controller
{
	public $layout='//layouts/column1';
	public function actions()
	{
		return array(
			'captcha' => array(
				'class' => 'ext.kcaptcha.KCaptchaAction',
				'settings' => array()
			),
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * Главная страница сайта
	 */
	public function actionIndex()
	{
		// Вытаскиваем 10 последних банов
		$dependecy = new CDbCacheDependency('SELECT MAX(`bid`) FROM {{bans}}');

		$bans = new CActiveDataProvider(Bans::model()->cache(300, $dependecy), array(
			'criteria' => array(
				'condition' => Yii::app()->config->auto_prune ? 'expired = 0' : null,
				'order' => 'bid DESC',
				'limit' => 10,
			),
			'pagination' => false,
		));

		$this->render('index',array(
			'bans' => $bans,
			'servers' => Serverinfo::model()->findAll(),
		));
	}

	/**
	 * Обработка HTTP ошибок
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Страница входа
	 */
	public function actionLogin()
	{
            if(!Yii::app()->user->isGuest)
                $this->redirect (array('/site/index'));

            $model=new LoginForm;

            // Если аякс запрос
            if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
            {
                    echo CActiveForm::validate($model);
                    Yii::app()->end();
            }

            // Собираем введенную инфу
            if(isset($_POST['LoginForm']))
            {
                    $model->attributes=$_POST['LoginForm'];
                    // Проверяем введенные данные и, если всё верно, перенаправляем на предыдущую страницу
                    if($model->validate() && $model->login())
                            $this->redirect(Yii::app()->request->urlReferrer);
            }
            // Вывод формы
            $this->render('login',array('model'=>$model));
	}

	/**
	 * Выход
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionInstall() {

		if(Yii::app()->db->username) {
			throw new CHttpException(404, 'Система уже установлена');
		}

		define('NOREDIRECT', TRUE);

		// Список требований
		$minPhp = '5.3';

		$testFunc = array(
			'chmod',
			'mkdir',
			'copy',
		);
		$disFunc = explode(',', ini_get('disable_functions'));

		$testExt = array(
			'pdo',
			'pdo_mysql',
			'gd',
			'mbstring',
			'ctype', // ?
		);

		$confFile = __DIR__ . '/../../include/db.config.inc.php';
		$assetDir = __DIR__ . '/../../assets';
		$runtimeDir = __DIR__ . '/../runtime';

		// Проверка подключения к БД
		if(Yii::app()->request->isAjaxRequest) {

			$form = new InstallForm('test');

			$form->attributes = $_POST['InstallForm'];

			$conn = $form->testConnect();

			if($conn !== TRUE) {
				Yii::app()->end('<span class="text-error">'.$conn.'</span>');
			}

			Yii::app()->end('<span class="text-success">Соединение установлено</span>');
		}

		$error = array();

		// Проверка требований
		if (version_compare(PHP_VERSION, $minPhp, '<')) {
			$error[] = 'Вы используете PHP версию ниже рекомендуемой';
		}

		foreach($testFunc AS $func) {
			if(!function_exists($func) || in_array($func, $disFunc)) {
				$error[] = "Недоступна функция {$func}";
			}
		}

		foreach($testExt AS $ext) {
			if(!extension_loaded($ext)) {
				$error[] = "Недоступно расширение {$ext}";
			}
		}

		if(!is_writable($confFile)) {
			if(!chmod($confFile, 0666)) {
				$error[] = 'Недостаточно прав для записи в конфиг';
			}
		}
		if(!is_writable($assetDir)) {
			if(!chmod($assetDir, 0777)) {
				exit('Недостаточно прав для записи в папку /assets');
			}
		}
		if(!is_writable($runtimeDir)) {
			if(!chmod($runtimeDir, 0777)) {
				exit('Недостаточно прав для записи в папку /protected/runtime');
			}
		}

		$form = new InstallForm();
		$success = FALSE;

		// ПОСТ? Ошибок нет? Устанавливаем!
		if(!count($error) && Yii::app()->request->isPostRequest) {

			$form->attributes = $_POST['InstallForm'];
			if($form->validate()) {

				$res = $form->installDB();
				if($res !== TRUE) {
					$form->addError('', $res);
				}
				else {
					$success = TRUE;
				}
			}
		}

		$this->render('install', array(
			'form' => $form,
			'error' => $error,
			'success' => $success,
		));
	}

	public function actionUpdate() {

		if(Yii::app()->user->isGuest) {
			throw new CHttpException(403, 'Фигвам');
		}

		$info = Yii::app()->db->createCommand('SELECT `start_page` FROM {{webconfig}}')->queryAll();

		if($info[0]['start_page'] === '/site/index') {

			throw new CHttpException(404, 'Нет обновлений');
		}

		if(isset($_POST['license'])) {

			$file = __DIR__ . '/../data/update.sql';
			$cmd = explode(';', file_get_contents($file));
			//if(!count($cmd)) return FALSE;

			try {
				foreach($cmd AS $sql) {

					$sql = trim($sql);
					if(!$sql) continue;

					Yii::app()->db->createCommand(str_replace('%prefix%_', Yii::app()->db->tablePrefix, $sql))->execute();
				}
				Yii::app()->cache->flush();
			}
			catch(Exception $e) {
				$this->render('/site/error', array(
					'code' => '',
					'message' => 'Произошла ошибка: ' . $e->getMessage(),
				));
				Yii::app()->end();
			}
		}

		$this->render('update');
	}

	public function actionLicense() {

		$this->render('license');
	}

}
