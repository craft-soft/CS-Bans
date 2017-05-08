<?php

class m170508_205938_create_usermenu_table extends CDbMigration
{
    private $tableName = '{{usermenu}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => 'pk',
            'pos' => 'int(11) NOT NULL DEFAULT "1"',
            'activ' => 'tinyint(1) NOT NULL DEFAULT "1"',
            'lang_key' => 'varchar(64)',
            'url' => 'varchar(64)',
            'lang_key2' => 'varchar(64)',
            'url2' => 'varchar(64)',
        ]);
        $this->createIndex('amx_usermenu_ind1', $this->tableName, ['pos', 'activ']);
        $this->insertMultiple($this->tableName, [
            [
                'pos' => 1,
                'activ' => 1,
                'lang_key' => '_HOME',
                'url' => '/site/index',
                'lang_key2' => '_HOME',
                'url2' => '/site/index',
            ],
            [
                'pos' => 2,
                'activ' => 1,
                'lang_key' => '_BANLIST',
                'url' => '/bans/index',
                'lang_key2' => '_BANLIST',
                'url2' => '/bans/index',
            ],
            [
                'pos' => 3,
                'activ' => 1,
                'lang_key' => '_ADMLIST',
                'url' => '/amxadmins/index',
                'lang_key2' => '_ADMLIST',
                'url2' => '/amxadmins/index',
            ],
            [
                'pos' => 4,
                'activ' => 1,
                'lang_key' => '_SERVER',
                'url' => '/serverinfo/index',
                'lang_key2' => '_SERVER',
                'url2' => '/serverinfo/index',
            ],
        ]);
    }

    public function down()
    {
        $this->dropIndex('amx_usermenu_ind1', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
