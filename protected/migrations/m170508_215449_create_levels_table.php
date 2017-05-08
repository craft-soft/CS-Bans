<?php

class m170508_215449_create_levels_table extends CDbMigration
{
    private $tableName = '{{levels}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'bans_add' => "enum('yes', 'no')",
            'bans_edit' => "enum('yes', 'no', 'own')",
            'bans_delete' => "enum('yes', 'no', 'own')",
            'bans_unban' => "enum('yes', 'no', 'own')",
            'bans_import' => "enum('yes', 'no')",
            'bans_export' => "enum('yes', 'no')",
            'amxadmins_view' => "enum('yes', 'no')",
            'amxadmins_edit' => "enum('yes', 'no')",
            'webadmins_view' => "enum('yes', 'no')",
            'webadmins_edit' => "enum('yes', 'no')",
            'websettings_view' => "enum('yes', 'no')",
            'websettings_edit' => "enum('yes', 'no')",
            'permissions_edit' => "enum('yes', 'no')",
            'prune_db' => "enum('yes', 'no')",
            'servers_edit' => "enum('yes', 'no')",
            'ip_view' => "enum('yes', 'no')",
        ]);
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}