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
		$user->scenario = 'auth';

		if($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}
		elseif(!$user->validatePassword($this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			$user->try++;
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
			$user->save();
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