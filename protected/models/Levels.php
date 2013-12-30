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
 * Модель для таблицы "{{levels}}".
 *
 * Доступные поля таблицы '{{levels}}':
 * @property integer $level ID уровня (он же название)
 * @property string $bans_add
 * @property string $bans_edit
 * @property string $bans_delete
 * @property string $bans_unban
 * @property string $bans_import
 * @property string $bans_export
 * @property string $amxadmins_view
 * @property string $amxadmins_edit
 * @property string $webadmins_view
 * @property string $webadmins_edit
 * @property string $websettings_view
 * @property string $websettings_edit
 * @property string $permissions_edit
 * @property string $prune_db
 * @property string $servers_edit
 * @property string $ip_view
 */
class Levels extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{levels}}';
	}

	public function rules()
	{
		return array(
			array('level', 'numerical', 'integerOnly'=>true),
			array('bans_add, bans_edit, bans_delete, bans_unban, bans_import, bans_export, amxadmins_view, amxadmins_edit, webadmins_view, webadmins_edit, websettings_view, websettings_edit, permissions_edit, prune_db, servers_edit, ip_view', 'in', 'range' => array('yes', 'no', 'own')),
			array('level, bans_add, bans_edit, bans_delete, bans_unban, bans_import, bans_export, amxadmins_view, amxadmins_edit, webadmins_view, webadmins_edit, websettings_view, websettings_edit, permissions_edit, prune_db, servers_edit, ip_view', 'safe', 'on'=>'search'),
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
			'level' => 'Уровень',
			'bans_add' => 'Добавить бан',
			'bans_edit' => 'Изменять бан',
			'bans_delete' => 'Удалять бан',
			'bans_unban' => 'Разбан',
			'bans_import' => 'Импорт банов',
			'bans_export' => 'Экспорт банов',
			'amxadmins_view' => 'Просмотр AMX админов',
			'amxadmins_edit' => 'Редактирование AMX админов',
			'webadmins_view' => 'Просмотр WEB админов',
			'webadmins_edit' => 'Редактирование WEB админов',
			'websettings_view' => 'Просмотр настроек',
			'websettings_edit' => 'Редактирование настроек',
			'permissions_edit' => 'Редактирование WEB прав',
			'prune_db' => 'Оптимизация БД',
			'servers_edit' => 'Редактирование серверов',
			'ip_view' => 'Просмотр IP',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('level',$this->level);
		$criteria->compare('bans_add',$this->bans_add,true);
		$criteria->compare('bans_edit',$this->bans_edit,true);
		$criteria->compare('bans_delete',$this->bans_delete,true);
		$criteria->compare('bans_unban',$this->bans_unban,true);
		$criteria->compare('bans_import',$this->bans_import,true);
		$criteria->compare('bans_export',$this->bans_export,true);
		$criteria->compare('amxadmins_view',$this->amxadmins_view,true);
		$criteria->compare('amxadmins_edit',$this->amxadmins_edit,true);
		$criteria->compare('webadmins_view',$this->webadmins_view,true);
		$criteria->compare('webadmins_edit',$this->webadmins_edit,true);
		$criteria->compare('websettings_view',$this->websettings_view,true);
		$criteria->compare('websettings_edit',$this->websettings_edit,true);
		$criteria->compare('permissions_edit',$this->permissions_edit,true);
		$criteria->compare('prune_db',$this->prune_db,true);
		$criteria->compare('servers_edit',$this->servers_edit,true);
		$criteria->compare('ip_view',$this->ip_view,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getList() {
		$model = self::model()->findAll();

		$list = CHtml::listData($model, 'level', 'level');

		return $list;
	}

	public static function getValues($ban = FALSE)
	{
		$return = array(
			'yes' => 'Да',
			'no' => 'Нет',
		);

		if($ban)
		{
			$return['own'] = 'Свои';
		}

		return $return;
	}

	public function beforeSave() {
		parent::beforeSave();

		if($this->isNewRecord)
		{
			$oldlevel = $this->model()->findBySql("SELECT MAX(`level`) AS `level` FROM {{levels}}");
			$this->level =  $oldlevel->level + 1;
		}

		return TRUE;
	}

	public function afterSave() {
		if($this->isNewRecord)
			Syslog::add(Logs::LOG_ADDED, 'Добавлен новый уровень веб админов');
		else
			Syslog::add(Logs::LOG_EDITED, 'Изменены уровень веб админов № <strong>' . $this->level . '</strong>');
		return parent::afterSave();
	}

	public function afterDelete() {
		Syslog::add(Logs::LOG_DELETED, 'Удален уровень веб админов № <strong>' . $this->level . '</strong>');
		return parent::afterDelete();
	}
}