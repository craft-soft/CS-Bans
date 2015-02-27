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
 * Модель для таблицы "{{bans}}".
 * Доступные поля таблицы '{{bans}}':
 * @property integer $bid ID бана
 * @property string $player_ip IP игрока
 * @property string $player_id Стим игрока
 * @property string $player_nick Ник игрока
 * @property string $admin_ip IP админа
 * @property string $admin_id Стим админа
 * @property string $admin_nick Ник админа
 * @property string $ban_type Тип бана
 * @property string $ban_reason Причина
 * @property string $cs_ban_reason Доп. причина
 * @property integer $ban_created Дата добавления
 * @property integer $ban_length Срок бана
 * @property string $server_ip IP сервера
 * @property string $server_name Название сервера
 * @property integer $ban_kicks Кол-во киков
 * @property integer $expired Дата истечения бана
 * @property integer $imported Импортирован бан или нет
 *
 * The followings are the available model relations:
 * @property integer $commentsCount
 * @property Comments[] $comments
 * @property integer $filesCount
 * @property Files[] $files
 * @property Amxadmins $admin
 */
class Bans extends CActiveRecord
{
	/**
	 * Флаг страны
	 * @var string
	 */
	public $country = null;
	//public $expiredTime = null;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{bans}}';
	}

	public function rules()
	{
		return array(
			array('player_nick', 'required'),
			array('ban_length, imported', 'numerical', 'integerOnly'=>true),
			array('player_ip', 'match', 'pattern' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/'),
			array('player_id', 'match', 'pattern' => '/^(STEAM|VALVE)_([0-9]):([0-9]):\d{1,21}$/'),
			array('player_nick, ban_reason, cs_ban_reason', 'length', 'max'=>100),
			array('ban_type', 'in', 'range' => array('S', 'SI')),
			//array('expiredTime', 'safe'),
			array('bid, player_ip, player_id, player_nick, admin_ip, admin_id, admin_nick, ban_type, ban_reason, cs_ban_reason, ban_created, ban_length, server_ip, server_name, ban_kicks, expired, imported, expiredTime', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'commentsCount' => array(
                self::STAT,
                'Comments',
                'bid',
                'defaultValue' => 0,
            ),
			'comments' => array(
                self::HAS_MANY,
                'Comments',
                'bid',
                'order' => 'comments.id DESC',
            ),
			'filesCount' => array(
                self::STAT,
                'Files',
                'bid',
                'defaultValue' => 0,
            ),
			'files' => array(
                self::HAS_MANY,
                'Files',
                'bid',
                'order' => 'files.id DESC',
            ),
            'admin' => array(
                self::HAS_ONE,
                'Amxadmins',
                '',
                'on' => '`admin`.`steamid` = `t`.`admin_nick` OR '
                    . '`admin`.`steamid` = `t`.`admin_ip` OR '
                    . '`admin`.`steamid` = `t`.`admin_id`'
            )
		);
	}

	public function attributeLabels()
	{
		return array(
			'bid'				=> 'Bid',
			'player_ip'			=> 'IP игрока',
			'player_id'			=> 'Steam  игрока',
			'player_nick'		=> 'Ник игрока',
			'admin_ip'			=> 'IP админа',
			'admin_id'			=> 'Steam ID админа',
			'admin_nick'		=> 'Ник админа',
			'adminName'         => 'Админ',
			'ban_type'			=> 'Тип бана',
			'ban_reason'		=> 'Причина',
			'cs_ban_reason'		=> 'Доп. Причина',
			'ban_created'		=> 'Дата',
			'ban_length'		=> 'Срок бана',
			'server_ip'			=> 'IP сервера',
			'server_name'		=> 'Название сервера',
			'ban_kicks'			=> 'Кики',
			'expired'			=> 'Истек',
			'imported'			=> 'Импортированный',
			'city'				=> 'Город',
			'expiredTime'		=> 'Истекает',
		);
	}

    public function getAdminName()
    {
        $return = $this->admin_nick;
        if(!Yii::app()->user->isGuest && $this->admin) {
            $return .= ' (<strong>'.CHtml::encode($this->admin->nickname).'</strong>)';
        }
        return $return;
    }
    
	public function getUnbanned() {
		return $this->ban_length == '-1' || $this->expired == 1 || ($this->ban_length && ($this->ban_created + ($this->ban_length * 60)) < time());
	}
	
	protected function afterFind() {
		$country = strtolower(Yii::app()->IpToCountry->lookup($this->player_ip));
		$this->country = CHtml::image(
            Yii::app()->urlManager->baseUrl 
            . '/images/country/' 
            . ($country != 'zz' ? $country : 'clear') . '.png'
        );
        return parent::afterFind();
	}

	protected function beforeSave() {
		if($this->isNewRecord) {
			$this->ban_created = time();
		} else {
			if($this->getUnbanned()) {
				$this->expired = time() + $this->ban_length * 60;
			} else {
				 $oldban = self::model()->findByPk($this->bid);
				 $this->expired = $oldban->expired + $this->ban_length * 60;
			 }
		}
		return parent::beforeSave();
	}

	public function afterSave() {
		if ($this->isNewRecord) {
            Syslog::add(Logs::LOG_ADDED, 'Добавлен новый бан игрока <strong>' . $this->player_nick . '</strong>');
        } else {
            Syslog::add(Logs::LOG_EDITED, 'Изменены детали бана игрока <strong>' . $this->player_nick . '</strong>');
        }
        return parent::afterSave();
	}

	public function afterDelete() {
		Syslog::add(Logs::LOG_DELETED, 'Удален бан игрока <strong>' . $this->player_nick . '</strong>');
		return parent::afterDelete();
	}

	protected function beforeValidate() {
		if($this->isNewRecord) {
			if (!filter_var($this->player_ip, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4))) {
                return $this->addError($this->player_ip, 'Неверно введен IP');
            }

            if($this->player_ip && Bans::model()->count('`player_ip` = :ip AND (`ban_length` = 0 OR `ban_created` + (`ban_length` * 60) >= UNIX_TIMESTAMP())', array(
					':ip' => $this->player_ip
				)))
			{
				return $this->addError($this->player_ip, 'Этот IP уже забанен');
			}
			
			if($this->player_id && Bans::model()->count('`player_id` = :id AND (`ban_length` = 0 OR `ban_created` + (`ban_length` * 60) >= UNIX_TIMESTAMP())', array(
					':id' => $this->player_id
				)))
			{
				return $this->addError($this->player_id, 'Этот STEAMID уже забанен');
			}
		}

		return parent::beforeValidate();
	}

	/**
	 * Возвращает список банов для селекта
	 * @return array
	 */
	public static function getBanLenght()
	{
		return array(
			'0'			=> 'Навсегда',
			'5'			=> '5 минут',
			'10'		=> '10 минут',
			'15'		=> '15 минут',
			'30'		=> '30 минут',
			'60'		=> '1 час',
			'120'		=> '2 часа',
			'180'		=> '3 часа',
			'300'		=> '5 часов',
			'600'		=> '10 часов',
			'1440'		=> '1 сутки',
			'4320'		=> '3 дня',
			'10080'		=> '1 неделя',
			'20160'		=> '2 недели',
			'43200'		=> '1 Месяц',
			'129600'	=> '3 месяца',
			'259200'	=> '6 месяцев',
			'518400'	=> '1 год',
		);
	}

	/**
	 * Возвращает дату истечения бана
	 * @return string
	 */
	public function getExpiredTime()
	{
		return Prefs::getExpired($this->ban_created, $this->ban_length);
	}
    
	/**
	 * Настройки поиска
	 * @return \CActiveDataProvider
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('bid',$this->bid);
		$criteria->addSearchCondition('player_ip',$this->player_ip);
		$criteria->addSearchCondition('player_id',$this->player_id);
		$criteria->addSearchCondition('player_nick',$this->player_nick);
		$criteria->compare('admin_ip',$this->admin_ip,true);
		$criteria->compare('admin_id',$this->admin_id,true);
		if ($this->admin_nick) {
            $criteria->compare('admin_nick', $this->admin_nick, true);
        }
        $criteria->compare('ban_type',$this->ban_type,true);
		$criteria->addSearchCondition('ban_reason',$this->ban_reason);
		$criteria->compare('cs_ban_reason',$this->cs_ban_reason,true);
		if ($this->ban_created) {
            $start = strtotime("{$this->ban_created} 00:00:00");
            $end = strtotime("{$this->ban_created} 23:59:59");
            $criteria->addBetweenCondition('ban_created', $start, $end);
        }
        $criteria->compare('ban_length',$this->ban_length);
		$criteria->compare('server_ip',$this->server_ip,true);
		$criteria->compare('server_name',$this->server_name,true);
		$criteria->compare('ban_kicks',$this->ban_kicks);
		$criteria->compare('expired',$this->expired);
		$criteria->compare('imported',$this->imported);

		$criteria->order = '`bid` DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => Yii::app()->config->bans_per_page
			)
		));
	}
}
