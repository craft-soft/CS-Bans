<?php

class m170508_211422_create_amxadmins_table extends CDbMigration
{
    private $tableName = '{{amxadmins}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'username' => 'varchar(32)',
            'password' => 'varchar(32)',
            'access' => 'varchar(25)',
            'flags' => 'varchar(5)',
            'steamid' => 'varchar(32)',
            'nickname' => 'varchar(32)',
            'icq' => 'int(9)',
            'ashow' => 'tinyint(1) DEFAULT "1"',
            'created' => 'int(11) UNSIGNED NOT NULL',
            'expired' => 'int(11) UNSIGNED NOT NULL',
            'days' => 'smallint(5) UNSIGNED NOT NULL',
        ]);
        $this->createIndex('amx_amxadmins_ind1', $this->tableName, ['username', 'password']);
        $this->createIndex('amx_amxadmins_ind2', $this->tableName, 'ashow');
    }

    public function down()
    {
        $this->dropIndex('amx_amxadmins_ind1', $this->tableName);
        $this->dropIndex('amx_amxadmins_ind2', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
