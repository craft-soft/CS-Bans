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
 * Модель для таблицы "{{usermenu}}".
 *
 * Доступные поля таблицы '{{usermenu}}':
 * @property integer $id ID записи
 * @property integer $pos Позиция
 * @property integer $activ Активность
 * @property string $lang_key Анкор для гостя
 * @property string $url Ссылка для гостя
 * @property string $lang_key2 Анкор для админа
 * @property string $url2 Ссылка для админа
 */
class Usermenu extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{usermenu}}';
	}

	public function rules()
	{
		return array(
			array('pos, activ', 'numerical', 'integerOnly'=>true),
			array('pos', 'unique'),
			array('lang_key, url, lang_key2, url2', 'length', 'max'=>64),
			array('id, pos, activ, lang_key, url, lang_key2, url2', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pos' => 'Позиция',
			'activ' => 'Активность',
			'lang_key' => 'Имя для гостей',
			'url' => 'URL для гостей',
			'lang_key2' => 'Имя для админов',
			'url2' => 'URL для админов',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('pos',$this->pos);
		$criteria->compare('activ',$this->activ);
		$criteria->compare('lang_key',$this->lang_key,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('lang_key2',$this->lang_key2,true);
		$criteria->compare('url2',$this->url2,true);
		$criteria->order = '`pos` ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getMenu()
	{
		if(
				in_array(Yii::app()->controller->action->id, array('install', 'license'))
					||
				defined('NOREDIRECT')
		) {
			return array(
				array(
					'label' => 'Главная',
					'url' => '../',
				)
			);
		}
		elseif(!Yii::app()->db->username) {
			Yii::app()->controller->redirect(array('/site/install'));
		}

		// Получаем модель
		$model = self::model()->findAll('`activ` = 1');

		// Гость?
		$guest = Yii::app()->user->isGuest;

		// Задаем меню массив
		$menu = array();

		// Проверки на ланг ключи (чтобы не было проблем со ссылками после обновления)
		$match = array(
			'_HOME' => 'Главная',
			'_BANLIST' => 'Банлист',
			'_SERVER' => 'Серверы',
			'_ADMLIST' => 'Админы',
			'_SEARCH' => 'Поиск',
			'_LOGIN' => 'Войти',
			'_LOGOUT' => 'Выйти',
		);

		foreach ($model as $m)
		{
			// Пропускаем неактивные ссылки
			//if($m->activ !== '1') continue;

			// Задаем урл для гостей и админов
			$url = $guest ? $m->url : $m->url2;

			// Если ссылка внутренняя, выводим через юии
			if(!filter_var($url, FILTER_VALIDATE_URL))
				$url = Yii::app()->createUrl($url);

			// Задаем анкор для гостей и админов
			$key = $guest ? $m->lang_key : $m->lang_key2;

			// Если анкора нет, не выводим ссылку
			if(!$key || empty($key)) continue;

			// Если анкоры прописаны ланг ключами, то подменяем ключи на слова
			if(array_key_exists($key, $match))
				$key = $match[$key];

			// Формируем массив для меню
			$menu[] = array(
				'label' => $key,
				'url' => $url
			);
		}

		// Возвращаем меню
		return $menu;
	}

	public static function getPositions()
	{
		$count = Usermenu::model()->count();
		$return = array();
		for($i=1; $i<=$count + 1; $i++)
		{
			$return[$i] = $i;
		}

		return $return;
	}

	public function afterSave() {
		if($this->isNewRecord)
			Syslog::add(Logs::LOG_ADDED, 'Добавлена новая ссылка меню <strong>' . $this->id . '</strong>');
		else
			Syslog::add(Logs::LOG_EDITED, 'Изменена ссылка <strong>' . $this->id . '</strong>');
		return parent::afterSave();
	}

	public function afterDelete() {
		Syslog::add(Logs::LOG_DELETED, 'Удалена ссылка <strong>' . $this->id . '</strong>');
		return parent::afterDelete();
	}
}