<?php

class m170508_220527_create_reasons_to_set_table extends CDbMigration
{
    private $tableName = '{{reasons_to_set}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'setid' => 'int(11)',
            'reasonid' => 'int(11)',
        ]);
        $this->addForeignKey(
            "amx_reasons_to_set_ibfk1",
            $this->tableName,
            'setid',
            '{{reasons_set}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            "amx_reasons_to_set_ibfk2",
            $this->tableName,
            'reasonid',
            '{{reasons}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey("amx_reasons_to_set_ibfk1", $this->tableName);
        $this->dropForeignKey("amx_reasons_to_set_ibfk2", $this->tableName);
        $this->dropTable($this->tableName);
    }
}
