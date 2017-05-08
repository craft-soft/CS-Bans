<?php

class m170508_220259_create_reasons_table extends CDbMigration
{
    private $tableName = '{{reasons}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'reason' => 'varchar(64)',
            'static_bantime' => 'int(11)',
        ]);
        $this->createIndex("amx_reasons_ind1", $this->tableName, 'reason');
    }

    public function down()
    {
        $this->dropIndex("amx_reasons_ind1", $this->tableName);
        $this->dropTable($this->tableName);
    }
}