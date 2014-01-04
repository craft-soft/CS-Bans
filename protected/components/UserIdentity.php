<?php
/**
 * Идентификация пользователя
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

class UserIdentity extends CUserIdentity
{
	private $_id;

	/**
	 * Аутентификация пользователя.
	 * @return boolean если аутентификация успешна.
	 */
	public function authenticate()
	{
		$username = strtolower($this->username);
		$user = Webadmins::model()->find('LOWER(username)=?', array($username));

		Yii::import('ext.kcaptcha.KCaptchaValidator');

		if($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}
		elseif($user->try >= 3 && empty($_POST['verify'])) {
			Yii::app()->request->cookies['captcha_auth'] = new CHttpCookie('captcha_auth', '1');
			Yii::app()->controller->refresh();
		}
		elseif($user->try >= 3 && !KCaptchaValidator::testCode($_POST['verify'])) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}
		elseif(!$user->validatePassword($this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			$user->try++;
			$user->scenario = 'auth';
			$user->save();
		}
		else {
			$this->_id = $user->id;
			$this->setState('email', $user->email);
			$this->username = $user->username;
			$this->errorCode = self::ERROR_NONE;

			// Лог входа, добавить запись
			$user->last_action = time();
			$user->try = 0;
			$user->scenario = 'auth';
			$user->save();

			unset(Yii::app()->request->cookies['captha_auth']);
		}

		return $this->errorCode == self::ERROR_NONE;
	}

	public function getId() {
		return $this->_id;
	}

	//public function getEmail() {
	//	return $this->getState('email');
	//}
}