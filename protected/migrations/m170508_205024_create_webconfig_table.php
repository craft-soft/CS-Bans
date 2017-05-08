<?php

class m170508_205024_create_webconfig_table extends CDbMigration
{
    private $tableName = '{{webconfig}}';
	public function up()
	{
	    $this->createTable($this->tableName, [
	        'id' => 'pk',
            'cookie' => 'varchar(32)',
            'bans_per_page' => 'int(11)',
            'design' => 'varchar(32)',
            'banner' => 'varchar(64)',
            'banner_url' => 'varchar(128)',
            'default_lang' => 'varchar(12)',
            'start_page' => 'varchar(12)',
            'show_comment_count' => 'tinyint(1) DEFAULT "1"',
            'show_demo_count' => 'tinyint(1) DEFAULT "1"',
            'show_kick_count' => 'tinyint(1) DEFAULT "1"',
            'demo_all' => 'tinyint(1) DEFAULT "0"',
            'comment_all' => 'tinyint(1) DEFAULT "0"',
            'use_capture' => 'tinyint(1) DEFAULT "1"',
            'use_demo' => 'tinyint(1) DEFAULT "1"',
            'use_comment' => 'tinyint(1) DEFAULT "1"',
            'auto_prune' => 'tinyint(1) DEFAULT "0"',
            'max_file_size' => 'int(11)',
            'file_type' => 'varchar(64) DEFAULT "dem,zip,rar,jpg,gif"',
            'max_offences' => 'int(6) DEFAULT "10"',
            'max_offences_reason' => 'varchar(128) DEFAULT "max offences reached"',
        ]);
	}

	public function down()
	{
		$this->dropTable($this->tableName);
	}
}
