<?php

class m170508_214840_create_files_table extends CDbMigration
{
    private $tableName = '{{files}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'name' => 'varchar(32)',
            'email' => 'varchar(64)',
            'addr' => 'varchar(16)',
            'upload_time' => 'int(11) UNSIGNED',
            'down_count' => 'int(11) UNSIGNED',
            'demo_file' => 'varchar(100)',
            'demo_real' => 'varchar(32)',
            'file_size' => 'int(11) UNSIGNED',
            'comment' => 'text',
            'bid' => 'int(11)',
        ]);
        $this->addForeignKey(
            "amx_files_ibfk1",
            $this->tableName,
            'bid',
            '{{bans}}',
            'bid',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey("amx_files_ibfk1", $this->tableName);
        $this->dropTable($this->tableName);
    }
}