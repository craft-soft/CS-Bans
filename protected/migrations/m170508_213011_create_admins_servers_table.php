<?php

class m170508_213011_create_admins_servers_table extends CDbMigration
{
    private $tableName = '{{admins_servers}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'admin_id' => 'int(11)',
            'server_id' => 'int(11)',
            'custom_flags' => 'varchar(25)',
            'use_static_bantime' => "enum('yes', 'no')",
        ]);
        $this->addPrimaryKey('amx_admins_servers_pk', $this->tableName, [
            'admin_id',
            'server_id',
        ]);
        $this->addForeignKey(
            'amx_admins_servers_ibfk1',
            $this->tableName,
            'admin_id',
            '{{amxadmins}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'amx_admins_servers_ibfk2',
            $this->tableName,
            'server_id',
            '{{serverinfo}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('amx_admins_servers_ibfk1', $this->tableName);
        $this->dropForeignKey('amx_admins_servers_ibfk2', $this->tableName);
        $this->dropPrimaryKey('amx_admins_servers_pk', $this->tableName);
        $this->dropTable($this->tableName);
    }
}