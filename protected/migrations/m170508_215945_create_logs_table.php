<?php

class m170508_215945_create_logs_table extends CDbMigration
{
    private $tableName = '{{logs}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'timestamp' => 'int(11) UNSIGNED',
            'ip' => 'varchar(16)',
            'username' => 'varchar(32)',
            'action' => 'varchar(32)',
            'remarks' => 'varchar(255)',
        ]);
        $this->createIndex("amx_logs_ind1", $this->tableName, [
            'timestamp',
            'ip',
            'username',
            'action',
            'remarks',
        ]);
    }

    public function down()
    {
        $this->dropIndex("amx_logs_ind1", $this->tableName);
        $this->dropTable($this->tableName);
    }
}
