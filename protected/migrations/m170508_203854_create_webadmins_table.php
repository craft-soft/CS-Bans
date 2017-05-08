<?php

class m170508_203854_create_webadmins_table extends CDbMigration
{
    private $tableName = '{{webadmins}}';
	public function up()
	{
	    $this->createTable($this->tableName, [
	        'id' => 'pk',
            'username' => 'varchar(32) NOT NULL',
            'password' => 'varchar(32) NOT NULL',
            'level' => 'int(11) NOT NULL',
            'logcode' => 'varchar(64) NOT NULL',
            'email' => 'varchar(64) NOT NULL',
            'last_action' => 'int(11) NOT NULL',
            'try' => 'tinyint(1) NOT NULL',
        ]);
	}

	public function down()
	{
		$this->dropTable($this->tableName);
	}
}