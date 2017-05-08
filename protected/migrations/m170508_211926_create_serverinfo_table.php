<?php

class m170508_211926_create_serverinfo_table extends CDbMigration
{
    private $tableName = '{{serverinfo}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'timestamp' => 'int(11) UNSIGNED',
            'hostname' => 'varchar(64)',
            'address' => 'varchar(25)',
            'gametype' => 'varchar(12)',
            'rcon' => 'varchar(32)',
            'amxban_version' => 'varchar(10)',
            'amxban_motd' => 'varchar(255)',
            'motd_delay' => 'tinyint(3) UNSIGNED',
            'amxban_menu' => 'int(10) UNSIGNED NOT NULL DEFAULT "1"',
            'reasons' => 'int(11)',
            'timezone_fixx' => 'tinyint(3) UNSIGNED',
        ]);
        $this->createIndex("amx_serverinfo_ind1", $this->tableName, 'address');
    }

    public function down()
    {
        $this->dropIndex("amx_serverinfo_ind1", $this->tableName);
        $this->dropTable($this->tableName);
    }
}
