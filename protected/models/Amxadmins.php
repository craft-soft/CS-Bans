<?php
/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

/**
 * Модель для таблицы "{{amxadmins}}".
 *
 * Доступные поля таблицы '{{amxadmins}}':
 * @property integer $id ID админа
 * @property string $username имя админа
 * @property string $password Пароль админа
 * @property string $access Доступ
 * @property string $flags Флаги
 * @property string $steamid Стим
 * @property string $nickname Ник
 * @property integer $icq Контакты
 * @property integer $ashow Показывать ли на странице админов
 * @property integer $created Дата добавления
 * @property integer $expired Дата окончания
 * @property integer $days Дней админки
 */
class Amxadmins extends CActiveRecord
{
	//public $accessflags = array();
	public $change;
	public $addtake = null;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{amxadmins}}';
	}

	public function getAccessflags() {

		return str_split($this->access);
	}

	public function setAccessflags($value) {
		$this->access = is_array($value) ? implode('', $value) : '';
	}

	public function scopes()
    {
        return array(
            'sort'=>array(
                'order'=>'`expired` ASC, `nickname` ASC'
            ),
        );
    }

	public function rules()
	{
		return array(
			array('steamid, nickname', 'required'),
			array('accessflags, addtake', 'safe'),
			array('icq, ashow, days, change', 'numerical', 'integerOnly'=>true),
			array('username, access, flags, steamid, nickname', 'length', 'max'=>32),
			array('password', 'length', 'max'=>50),
			array('id, username, password, access, flags, steamid, nickname, icq, ashow, created, expired, days', 'safe',  'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'servers' => array(
				self::MANY_MANY,
				'Serverinfo',
				'{{admins_servers}}(admin_id, server_id)'
			),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'SteamID',
			'password' => 'Пароль',
			'access' => 'Флаги доступа',
			'accessflags' => 'Флаги доступа',
			'flags' => 'Тип админки',
			'steamid' => 'Steamid/IP/Ник',
			'nickname' => 'Ник',
			'icq' => 'ICQ',
			'ashow' => 'Видимость в списке админов',
			'created' => 'Дата добавления',
			'expired' => 'Истекает',
			'days' => 'Дней',
			'long' => 'Осталось дней',
			'change' => 'Новый срок',
			'addtake' => 'Выбор'
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('access',$this->access,true);
		$criteria->compare('flags',$this->flags,true);
		$criteria->compare('steamid',$this->steamid,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('icq',$this->icq);
		$criteria->compare('ashow',$this->ashow);
		$criteria->compare('created',$this->created);
		$criteria->compare('expired',$this->expired);
		$criteria->compare('days',$this->days);
		//$criteria->order = 'nickname ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' =>  Yii::app()->config->bans_per_page,
			),
		));
	}

	public static function getFlags($adminlist = FALSE)
	{
		if($adminlist)
		{
			return array(
				'a' => 'Иммунитет (не может быть кикнут / забанен и т.д)',
				'b' => 'Резервирование слотов (может использовать зарезервированные слоты)',
				'c' => 'Команда amx_kick',
				'd' => 'Команда amx_ban и amx_unban',
				'e' => 'Команда amx_slay и amx_slap',
				'f' => 'Команда amx_map',
				'g' => 'Команда amx_cvar (не все CVAR\'ы доступны)',
				'h' => 'Команда amx_cfg',
				'i' => 'amx_chat и другие команды чата',
				'j' => 'amx_vote и другие команды голосований (Vote)',
				'k' => 'Доступ к изменению значения команды sv_password (через команду amx_cvar)',
				'l' => 'Доступ к amx_rcon и rcon_password (через команду amx_cvar)',
				'm' => 'Уровень доступа A (для иных плагинов)',
				'n' => 'Уровень доступа B',
				'o' => 'Уровень доступа C',
				'p' => 'Уровень доступа D',
				'q' => 'Уровень доступа E',
				'r' => 'Уровень доступа F',
				's' => 'Уровень доступа G',
				't' => 'Уровень доступа H',
				'u' => 'Основной доступ',
				'z' => 'Игрок (не администратор)'
			);
		}

		return array(
			'a' => '[a] Иммунитет (не может быть кикнут / забанен и т.д)',
			'b' => '[b] Резервирование слотов (может использовать зарезервированные слоты)',
			'c' => '[c] Команда amx_kick',
			'd' => '[d] Команда amx_ban и amx_unban',
			'e' => '[e] Команда amx_slay и amx_slap',
			'f' => '[f] Команда amx_map',
			'g' => '[g] Команда amx_cvar (не все CVAR\'ы доступны)',
			'h' => '[h] Команда amx_cfg',
			'i' => '[i] amx_chat и другие команды чата',
			'j' => '[j] amx_vote и другие команды голосований (Vote)',
			'k' => '[k] Доступ к изменению значения команды sv_password (через команду amx_cvar)',
			'l' => '[l] Доступ к amx_rcon и rcon_password (через команду amx_cvar)',
			'm' => '[m] Уровень доступа A (для иных плагинов)',
			'n' => '[n] Уровень доступа B',
			'o' => '[o] Уровень доступа C',
			'p' => '[p] Уровень доступа D',
			'q' => '[q] Уровень доступа E',
			'r' => '[r] Уровень доступа F',
			's' => '[s] Уровень доступа G',
			't' => '[t] Уровень доступа H',
			'u' => '[u] Основной доступ',
			'z' => '[z] Игрок (не администратор)'
		);
	}

	protected function beforeDelete() {
		parent::beforeDelete();
		$servers = AdminsServers::model()->findByAttributes(array('admin_id' => $this->id));
		if($servers !== NULL)
			$servers->deleteAllByAttributes(array('admin_id' => $this->id));

		return TRUE;
	}

	protected function beforeSave() {
		if($this->isNewRecord)
		{
			$this->created = time();
			$this->password = $this->scenario == 'buy' ? $this->password : md5($this->password);
			$this->expired = $this->days != 0 ? ($this->days * 86400) + time() : 0;
		}
		else
		{
			$oldadmin = Amxadmins::model()->findByPk($this->id);
			if(!empty($this->password))
				$this->password = md5($this->password);
			else
				$this->password = $oldadmin->password;

			if($this->expired == 0)
			{
				$this->expired = time();
			}

			switch($this->addtake)
			{
				case '1':
					$this->expired = $this->expired - ($this->change *86400);
					$this->days = $this->days - $this->change;
					break;
				case '0':
					$this->expired = $this->expired + ($this->change *86400);
					$this->days = $this->days + $this->change;
					break;
				default:
					$this->expired = 0;
					$this->days = 0;
			}
		}
		return parent::beforeSave();
	}

	protected function beforeValidate() {
		parent::beforeValidate();

		if($this->scenario == 'buy') return TRUE;

		/*if($this->addtake && $this->long <= 0)
			return $this->addError ('change', 'Ошибка. Нельзя забрать дни админки. Этот админ просрочен');*/

		if(!$this->access)
			return $this->addError ('access', 'Выберите флаги доступа');

		if
		(
			(
				$this->isNewRecord
					||
				!empty($this->password)
			)
				&&
			(
				$this->flags === 'a'
					&&
				!preg_match('#^([a-z0-9]+)$#i', $this->password)
			)
		)
		{
			return $this->addError ('password', 'Пароль может содержать только буквы латинского алфавита и цифры');
		}

		if($this->flags === 'de' && !filter_var($this->steamid, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4)))
			return $this->addError ('steamid', 'Неверно введен IP');

		if($this->flags === 'ce' && !Prefs::validate_value($this->steamid, 'steamid'))
			return $this->addError ('steamid', 'Неверно введен SteamID');

		if(!$this->isNewRecord && $this->days < $this->change && $this->addtake === '1')
		{
			return $this->addError ('', 'Ошибка! Нельзя забрать дней больше, чем у него уже есть');
		}

		return TRUE;
	}

	public static function getAuthType($get = FALSE)
	{
		$flags = array(
			'a' => 'Ник + пароль',
			'ce' => 'SteamID',
			'de' => 'IP'
		);

		if($get)
		{
			if(array_key_exists($get, $flags)) {
				return $flags[$get];
			}

			return 'Неизвестно';
		}
		return $flags;
	}

	public function getLong()
	{
		$long = $this->expired - time();
		if($this->expired == 0 || $long < 0)
			return FALSE;

		return intval($long / 86400);
	}

	public function afterSave() {
		if($this->isNewRecord)
			Syslog::add(Logs::LOG_ADDED, 'Добавлен новый AmxModX админ <strong>' . $this->nickname . '</strong>');
		else
			Syslog::add(Logs::LOG_EDITED, 'Изменены детали AmxModX админа <strong>' . $this->nickname . '</strong>');
		return parent::afterSave();
	}

	public function afterDelete() {
		Syslog::add(Logs::LOG_DELETED, 'Удален AmxModX админ <strong>' . $this->nickname . '</strong>');
		return parent::afterDelete();
	}
}