<?php

class m170508_213641_create_bans_table extends CDbMigration
{
    private $tableName = '{{bans}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'bid' => 'pk',
            'player_ip' => 'varchar(16)',
            'player_id' => 'varchar(32)',
            'player_nick' => 'varchar(32)',
            'admin_ip' => 'varchar(16)',
            'admin_id' => 'varchar(32)',
            'admin_nick' => 'varchar(32)',
            'ban_type' => 'varchar(8) DEFAULT "S"',
            'ban_reason' => 'varchar(128)',
            'cs_ban_reason' => 'varchar(128)',
            'ban_created' => 'int(11) UNSIGNED',
            'ban_length' => 'smallint(5)',
            'server_ip' => 'varchar(25)',
            'server_name' => 'varchar(64)',
            'ban_kicks' => 'int(11) DEFAULT "0"',
            'expired' => 'tinyint(1) DEFAULT "0"',
            'imported' => 'tinyint(1) DEFAULT "0"',
        ]);
        $this->createIndex("amx_bans_ind1", $this->tableName, [
            'player_ip',
            'player_id',
            'player_nick',
            'admin_ip',
            'admin_id',
            'admin_nick',
            'ban_type',
            'ban_reason',
            'cs_ban_reason',
            'ban_created',
            'server_name',
            'expired',
            'imported',
        ]);
    }

    public function down()
    {
        $this->dropIndex("amx_bans_ind1", $this->tableName);
        $this->dropTable($this->tableName);
    }
}
