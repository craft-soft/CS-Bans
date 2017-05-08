<?php

class m170508_215155_create_flagged_table extends CDbMigration
{
    private $tableName = '{{flagged}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'server_ip' => 'varchar(25)',
            'player_ip' => 'varchar(16)',
            'player_id' => 'varchar(32)',
            'player_nick' => 'varchar(100)',
            'admin_ip' => 'varchar(16)',
            'admin_id' => 'varchar(32)',
            'admin_nick' => 'varchar(100)',
            'reason' => 'varchar(100)',
            'created' => 'int(11) UNSIGNED',
            'length' => 'int(11)',
        ]);
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}