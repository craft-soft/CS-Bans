<?php

class m170508_220419_create_reasons_set_table extends CDbMigration
{
    private $tableName = '{{reasons_set}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'setname' => 'varchar(32) NOT NULL',
        ]);
        $this->createIndex("amx_reasons_set_ind1", $this->tableName, 'setname', true);
    }

    public function down()
    {
        $this->dropIndex("amx_reasons_set_ind1", $this->tableName);
        $this->dropTable($this->tableName);
    }
}