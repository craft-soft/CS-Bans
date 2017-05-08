<?php

class m170508_214408_create_comments_table extends CDbMigration
{
    private $tableName = '{{comments}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'name' => 'varchar(32)',
            'comment' => 'text',
            'email' => 'varchar(64)',
            'addr' => 'varchar(16)',
            'date' => 'int(11) UNSIGNED',
            'bid' => 'int(11)',
        ]);
        $this->addForeignKey(
            "amx_comments_ibfk1",
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
        $this->dropForeignKey("amx_comments_ibfk1", $this->tableName);
        $this->dropTable($this->tableName);
    }
}