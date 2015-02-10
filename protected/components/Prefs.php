<?php
/**
 * Прочие функции
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

/**
 * @getBanType получение типа бана
 * @steam_convert Конвертирует steamID в steamcommunityID и обратно
 * @date2word Конвертация даты в строковый формат
 */
class Prefs extends CApplicationComponent {

	/**
	 * Размер базы
	 * @return string размер базы данных
	 */
	public static function db_size() {

		$db_size = Yii::app()->cache->get('dbSize');

		if($db_size === false) {

			$query = Yii::app()->db->createCommand("SHOW TABLE STATUS FROM `".Yii::app()->params['dbname']."`")->queryAll();
			$db_size = 0;
			foreach($query as $row)
			{
				$db_size += $row["Data_length"] + $row["Index_length"];
			}

			Yii::app()->cache->set('dbSize', $db_size, 300);
		}

		return self::formatfilesize($db_size);
	}

	/**
	 * Получение типа бана
	 * @param type $type
	 * @return string|boolean тип бана
	 */
	public static function getBanType($type)
	{
		switch ($type)
		{
			case 'S':
				return 'SteamID';
			case 'SI':
				return 'SteamID + IP';
		}
		return false;
	}

	/**
	* Конвертирует steamID в steamcommunityID и обратно
	* @param string $id steamID/steamcommunityID
	* @param boolean $url [optional] Добавлять к steamcommunityID URL профиля, по умолчанию false.
	* @return string|false
	* @author Kapman <kapman@craft-soft.ru>
	*/
	public static function steam_convert($id, $url = false, $xml = false) {

		$RightSteam = "/^(STEAM_[0-9])\:([0-9])\:([0-9]{4,8})$/";
		$RightNumber = "/^(7656119)([0-9]{10})$/";

		if (!$id) { return false; }

		if(preg_match($RightSteam, $id, $match)) {

			$newst1 = $match[2];
			$newst2 = $match[3];
			$const1 = 7656119;
			$const2 = 7960265728;
			$answer = $newst1 + $newst2 * 2 + $const2;

			if($xml) {
				return CHtml::encode('http://steamcommunity.com/profiles/'.$const1 . $answer . '?xml=1');
			}

			if($url) {
				return CHtml::link($const1 . $answer,'http://steamcommunity.com/profiles/'.$const1 . $answer, array('target' => '_blank'));
			}
			return $const1 . $answer;
		} elseif (preg_match($RightNumber, $id, $match)) {
			if($xml) {
				return CHtml::encode('http://steamcommunity.com/profiles/'.$id . '?xml=1');
			}
			$const1 = 7960265728;
			$const2 = "STEAM_0:";

			if ($const1 <= $match[2]) {
				$a = ($match[2] - $const1)%2;
				$b = ($match[2] - $const1 - $a)/2;

				return $const2.$a.':'.$b;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * Конвертирует время в минутах в человеко-понятный формат
	 * @author SeToY & |PJ|ShOrTy
	 * @param intval $dif
	 * @param boolean $short
	 * @return string
	 * @author AmxBans Team <amxbans.de>
	 */
	public static function date2word($dif, $short=false, $server = false)
	{
		if($dif == 0) {
			return $server ? '' : 'Навсегда';
		}
		if($dif == '-1') {
			return 'Разбанен';
		}

		$dif = $server ? $dif : $dif * 60;

		if($dif) {
			$s = '';
			$years = intval($dif / (60 * 60 * 24 * 365));
			$dif = $dif - ($years * (60 * 60 * 24 * 365));

			if($years) {
				$s .= "{$years} лет ";
			}
			if($years && $short) {
				return $s;
			}

			$months = intval($dif / (60 * 60 * 24 * 30));
			$dif = $dif - ($months * (60 * 60 * 24 * 30));
			if($months) {
				$s .= "{$months} мес. ";
			}
			if($months && $short) {
				return $s;
			}

			$weeks = intval($dif / (60 * 60 * 24 * 7));
			$dif = $dif - ($weeks * (60 * 60 * 24 * 7));

			if($weeks) {
				$s .= "{$weeks} нед. ";
			}

			if($weeks && $short) {
			   return $s;
			}

			$days = intval($dif / (60 * 60 * 24));
			$dif = $dif - ($days * (60 * 60 * 24));
			if($days) {
				$s .= "{$days} дн. ";
			}
			if($days && $short) {
				return $s;
			}

			$hours = intval($dif / (60 * 60));
			$dif = $dif - ($hours * (60 * 60));
			if($hours) {
				$s .= "{$hours} час. ";
			}
			if($hours && $short) {
				return $s;
			}

			$minutes = intval($dif / 60);
			$seconds = $dif - ($minutes * 60);
			if($minutes) {
				$s .= "{$minutes} мин.";
			}
			if($minutes && $short) {
				return $s;
			}

			if($short) {
				return "{$seconds} сек.";
			}

			return $s;
		} else {
			return;
		}
	}

	/**
	 * Вывод даты окончания бана
	 * @author Onotole <webmaster@mix-game.pro>
	 * @param intval $create Дата создания в секундах
	 * @param intval $lenght Срок бана в минутах
	 * @return string Дату окончания бана
	 */
	public static function getExpired($create, $lenght)
	{
		if ($lenght == 0) {
            return 'Никогда';
        }

        if ($lenght == '-1') {
            return 'Разбанен';
        }

        $lenght = $lenght * 60;
		return date('d.m.Y - H:i:s', $create + $lenght);
	}

	/**
	 * Проверка значения на валидность
	 * @author SeToY & |PJ|ShOrTy
	 * @param STRING $types: Тип (email, steamid, ip, amxxaccess, amxxflags)
	 * @return boolean
	 * @author SourceBans Team <sourcebans.com>
	 */

	public static function validate_value($value,$type='steamid') {

		switch($type) {
			case 'email':
				return preg_match("/^[a-zA-Z0-9-_.]{2,}@[a-zA-Z0-9-_.]{2,}.[a-zA-Z]{2,6}$/",$value);
			case 'steamid':
				return preg_match("/^(STEAM|VALVE)_[0-9]:[0-9]:[0-9]{1,15}$/",$value);
			case 'ip':
				return preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/",$value);
			case 'amxxaccess':
				return preg_match("/^[a-u,z]{1,22}$/",$value);
			case 'amxxflags':
				if((strrpos($value,"b")!==false && strrpos($value,"c")!==false)
					||
				   (strrpos($value,"b")!==false && strrpos($value,"d")!==false)
					||
				   (strrpos($value,"c")!==false && strrpos($value,"d")!==false))
				{
					return false;
				}

				if(strrpos($value,"a")===false
						&&
					strrpos($value,"b")===false
						&&
					strrpos($value,"c")===false
						&&
					strrpos($value,"d")===false)
				{
					return false;
				}
				return preg_match("/^[a-e,k]{1,4}$/",$value);
			default:
				return false;
		}
		return false;
	}

	/**
	 * Возвращает размер файла в человеко-понятном формате
	 * @param intval $data
	 * @return string
	 */
    public static function formatfilesize($data) {
        if ($data < 1024) {
            return $data . " b.";
        }
        if ($data < 1024000) {
            return round(($data / 1024), 2) . "Kb";
        }
        return round(($data / 1024000), 2) . " Mb";
    }

    /**
	 * Получение информации о сервере/сайте
	 * @return string
	 */
	public static function sysprefs()
	{
		return array(
			'info' => array(
				//'Версия сайта'			=> self::getVersion(),
				'Версия PHP'			=> version_compare(PHP_VERSION, '5.3', '<')
						? '<span class="text-error"><b>'.PHP_VERSION.'</b> (рекомендуемая 5.3 или выше)</span>'
						: PHP_VERSION,
				'Веб сервер'			=> $_SERVER['SERVER_SOFTWARE'],
				'Версия MySQL'			=> version_compare(Yii::app()->db->serverVersion, '5.0', '<')
						? '<span class="text-error"><b>'.Yii::app()->db->serverVersion.'</b> (рекомендуемая 5.0 или выше)</span>'
						: Yii::app()->db->serverVersion,
				'display_errors'		=> ini_get('display_errors') ? '<span class="text-error"><b>Вкл</b></span>' : 'Выкл',
				'register_globals'		=> ini_get('register_globals') ? '<span class="text-error"><b>Вкл</b></span>' : 'Выкл',
				'magic_quotes_gpc'		=> get_magic_quotes_gpc() ? '<span class="text-error"><b>Вкл</b></span>' : 'Выкл',
				'safe_mode'				=> ini_get('safe_mode') ? '<span class="text-error"><b>Вкл</b></span>' : 'Выкл',
				'post_max_size'			=> ini_get('post_max_size'),
				'upload_max_filesize'	=> ini_get('upload_max_filesize'),
				'max_execution_time'	=> ini_get('max_execution_time'),
			),
			'modules' => array(
				'bcmath'	=> extension_loaded('bcmath') ? 'Да' : 'Нет',
				'gmp'		=> extension_loaded('gmp') ? 'Да' : 'Нет',
				'gd'		=> extension_loaded('gd') ? 'Да' : 'Нет',
			)
		);
	}

	/**
	 * Проверяет и выводит версию
	 */
	public static function getVersion() {
		$current = Yii::app()->params['Version'];
		if( ($last = Yii::app()->cache->get('getVersion')) === false ) {
			$last = @file_get_contents('http://craft-soft.ru/goods/version.html?id=csbans');
		}
		if(!$last) {
			return "{$current} <span class='text-warning'>(не удалось проверить версию)</span>";
		}
		Yii::app()->cache->set('getVersion', $last, 21600);
		if(version_compare($current, $last, '<')) {
			return "{$current} <span class='text-error'>(доступна новая версия)</span>";
		}
		return "{$current} <span class='text-success'>(вы используете последнюю версию)</span>";
	}
    
    public static function getRealIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif(!empty($_SERVER['HTTP_X_REAL_IP'])) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return substr($ip, 0, 16);
	}
}
