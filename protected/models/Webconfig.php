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
 * Модель для таблицы "{{webconfig}}".
 *
 * Доступные поля таблицы '{{webconfig}}':
 * @property integer $id
 * @property string $cookie
 * @property integer $bans_per_page
 * @property string $design
 * @property string $banner
 * @property string $banner_url
 * @property string $default_lang
 * @property string $start_page
 * @property integer $show_comment_count
 * @property integer $show_demo_count
 * @property integer $show_kick_count
 * @property integer $demo_all
 * @property integer $comment_all
 * @property integer $use_capture
 * @property integer $max_file_size
 * @property string $file_type
 * @property integer $auto_prune
 * @property integer $max_offences
 * @property string $max_offences_reason
 * @property integer $use_demo
 * @property integer $use_comment
 */
class Webconfig extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{webconfig}}';
	}

	public function rules()
	{
		return array(
			array('max_offences,max_offences_reason, cookie, max_file_size, file_type, bans_per_page', 'required'),
			array('bans_per_page, show_comment_count, show_demo_count, show_kick_count, demo_all, comment_all, use_capture, max_file_size, auto_prune, max_offences, use_demo, use_comment', 'numerical', 'integerOnly'=>true),
			array('cookie, design, default_lang', 'length', 'max'=>32),
			array('banner, start_page, file_type', 'length', 'max'=>64),
			array('banner_url, max_offences_reason', 'length', 'max'=>128),
			array('bans_per_page, design, banner, banner_url, default_lang, start_page, show_comment_count, show_demo_count, show_kick_count, demo_all, comment_all, use_capture, max_file_size, file_type, auto_prune, max_offences, max_offences_reason, use_demo, use_comment', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public static function getCfg() {

		$cache = Yii::app()->cache->get('web_cfg');

		if($cache === FALSE) {

			$cache = self::model()->find();
			Yii::app()->cache->set('web_cfg', $cache, 21600);
		}

		return $cache;
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cookie' => 'Причина бана с сайта',
			'bans_per_page' => 'Элементов на странице',
			'design' => 'Шаблон',
			'banner' => 'Банер',
			'banner_url' => 'Ссылка баннера',
			'default_lang' => 'Язык',
			'start_page' => 'Стартовая страница',
			'show_comment_count' => 'Показывать кол-во коментариев',
			'show_demo_count' => 'Показывать кол-во файлов',
			'show_kick_count' => 'Показывать кол-во киков',
			'demo_all' => 'Разрешить гостям добавлять демо',
			'comment_all' => 'Разрешить гостям добавлять коментарии',
			'use_capture' => 'Отображать город',
			'max_file_size' => 'Срок бана с сайта (в минутах)',
			'file_type' => 'Типы файлов',
			'auto_prune' => 'Скрывать истекшие баны',
			'max_offences' => 'Максимальное кол-во нарушений',
			'max_offences_reason' => 'Причина для макс. Нарушений',
			'use_demo' => 'Разрешить добавлять демо',
			'use_comment' => 'Разрешить добавлять коммент',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('bans_per_page',$this->bans_per_page);
		$criteria->compare('design',$this->design,true);
		$criteria->compare('banner',$this->banner,true);
		$criteria->compare('banner_url',$this->banner_url,true);
		$criteria->compare('default_lang',$this->default_lang,true);
		$criteria->compare('start_page',$this->start_page,true);
		$criteria->compare('show_comment_count',$this->show_comment_count);
		$criteria->compare('show_demo_count',$this->show_demo_count);
		$criteria->compare('show_kick_count',$this->show_kick_count);
		$criteria->compare('demo_all',$this->demo_all);
		$criteria->compare('comment_all',$this->comment_all);
		$criteria->compare('use_capture',$this->use_capture);
		$criteria->compare('max_file_size',$this->max_file_size);
		$criteria->compare('file_type',$this->file_type,true);
		$criteria->compare('auto_prune',$this->auto_prune);
		$criteria->compare('max_offences',$this->max_offences);
		$criteria->compare('max_offences_reason',$this->max_offences_reason,true);
		$criteria->compare('use_demo',$this->use_demo);
		$criteria->compare('use_comment',$this->use_comment);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function afterSave() {
		if(!$this->isNewRecord) {
			Syslog::add(Logs::LOG_EDITED, 'Изменены настройки сайта');
		}

		Yii::app()->cache->delete('web_cfg');

		return parent::afterSave();
	}
}