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
 * Модель инсталлятора
 */

class InstallForm extends CFormModel
{
	/**
	 * Хост MySQL
	 * @var string
	 */
	public $db_host;

	/**
	 * Имя базы данных
	 * @var string
	 */
	public $db_db;

	/**
	 * Пользователь базы
	 * @var string
	 */
	public $db_user;

	/**
	 * Пароль пользователя базы
	 * @var string
	 */
	public $db_pass;

	/**
	 * Префикс таблиц
	 * @var string
	 */
	public $db_prefix;

	/**
	 * Логин нового админа
	 * @var string
	 */
	public $login;

	/**
	 * Пароль нового админа
	 * @var string
	 */
	public $password;

	/**
	 * Почта нового админа
	 * @var string
	 */
	public $email;


	/**
	 * Лицензионное соглашение
	 * @var boolean
	 */
	public $license;

	protected $conn = NULL;

	public function rules()
	{
		return array(
			array('db_host, db_db, db_user, db_pass', 'required'),
			array('db_prefix', 'default', 'value' => 'amx'),
			array('login, password, email', 'required', 'except' => 'test'),
			array('login', 'match', 'pattern' => '#^[a-z0-9]+$#i', 'except' => 'test'),
			array('email', 'email', 'except' => 'test'),

			array('license', 'boolean'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'db_host'		=> 'Хост',
			'db_db'			=> 'База данных',
			'db_user'		=> 'Пользователь',
			'db_pass'		=> 'Пароль',
			'db_prefix'		=> 'Префикс',
			'login'			=> 'Логин',
			'password'		=> 'Пароль',
			'email'			=> 'Email',
			'license'		=> 'Условия лицензионного соглашения',
		);
	}

	/**
	 * Проверка соединения с БД
	 * @param boolean $close
	 * @return boolean|string|CDbConnection
	 */
	public function testConnect($close = TRUE, $minVer = '5.0') {

		$this->conn = new CDbConnection('mysql:host='.
				preg_replace('#[^a-z0-9\.-_]#', '', $this->db_host).';dbname='.
				preg_replace('#[^a-z0-9_]#', '', $this->db_db),
				$this->db_user, $this->db_pass);

		$this->conn->charset = 'utf8';

		try {
			$this->conn->active = TRUE;
		}
		catch(Exception $e) {
			return $e->getMessage();
		}

		if($minVer) {
			if(version_compare($this->conn->serverVersion, $minVer, '<')) {
				return 'Версия сервера БД ниже рекомендуемой, минимальная ' . $minVer;
			}
		}

		if($close) {
			$this->conn->active = FALSE;
			return TRUE;
		}

		return $this->conn;
	}

	/**
	 * Установка системы
	 * @return boolean|string
	 */
	public function installDB() {

		// Создаём массив запросов из файла
		$file = __DIR__ . '/../data/install.sql';
		$cmd = explode(';', file_get_contents($file));
		if(!count($cmd)) return FALSE;

		// Новое соединение
		if(!$this->conn) {
			$this->testConnect(FALSE);
		}
		if(!$this->conn->active) {
			$this->conn->active = TRUE;
		}

		try {
			// Выполняем запросы из SQL файла
			foreach($cmd AS $sql) {

				$sql = trim($sql);
				if(!$sql) continue;

				$this->conn->createCommand(str_replace('%prefix%', $this->db_prefix, $sql))->execute();
			}

			// Добавляем админа
			$admin = "INSERT INTO {$this->db_prefix}_webadmins(username, password, level, email) VALUES(:username, :password, :level, :email)";
			$level = 1;
			$passwd = md5($this->password);
			$comm = $this->conn->createCommand($admin);
			$comm->bindParam(':username', $this->login, PDO::PARAM_STR);
			$comm->bindParam(':password', $passwd, PDO::PARAM_STR);
			$comm->bindParam(':level', $level, PDO::PARAM_INT);
			$comm->bindParam(':email', $this->email, PDO::PARAM_STR);
			$comm->execute();
		}
		catch(Exception $e) {
			return $e->getMessage();
		}

		// Сохраняем конфиг
		$cfg = '<?php' . PHP_EOL;
		$cfg .= '/**' . PHP_EOL;
		$cfg .= ' * Конфигурационные данные системы' . PHP_EOL;
		$cfg .= ' *' . PHP_EOL;
		$cfg .= ' * @author Craft-Soft Team' . PHP_EOL;
		$cfg .= ' * @version 1.0 beta' . PHP_EOL;
		$cfg .= ' * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.' . PHP_EOL;
		$cfg .= ' * @package CS:Bans' . PHP_EOL;
		$cfg .= ' * @link http://craft-soft.ru/' . PHP_EOL;
		$cfg .= '*/' . PHP_EOL;
		$cfg .= PHP_EOL;
		$cfg .= "\$config->db_host	= '{$this->db_host}';" . PHP_EOL;
		$cfg .= "\$config->db_user	= '{$this->db_user}';" . PHP_EOL;
		$cfg .= "\$config->db_pass	= '{$this->db_pass}';" . PHP_EOL;
		$cfg .= "\$config->db_db	= '{$this->db_db}';" . PHP_EOL;
		$cfg .= "\$config->db_prefix	= '{$this->db_prefix}';" . PHP_EOL;

		$cfgFile = __DIR__ . '/../../include/db.config.inc.php';

		if(
			!is_writable($cfgFile)
				||
			!file_put_contents($cfgFile, $cfg)
		) {
			return 'Не удалось сохранить конфиг.';
		}

		return TRUE;
	}

	protected function afterValidate() {

		if(($err = $this->testConnect()) !== TRUE) {
			$this->addError('', 'Ошибка подключения к БД: ' . $err);
		}

		if(!$this->license) {
			$this->addError('license', 'Вы не приняли условия лицензионного соглашения');
		}

		return parent::afterValidate();
	}
}
