<?php

use Zver\SQLite;

class SQLiteTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Helper;

    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {

    }

    public function getTestDB()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'test.db';
    }

    public function testConnectAndCreate()
    {
        $this->assertFalse(empty(SQLite::connect($this->getTestDB())));
    }

    public function testExecuteAndInsert()
    {
        $sq = SQLite::connect($this->getTestDB());
        $this->assertFalse(empty(SQLite::connect($this->getTestDB())));

        $result = $sq->execute("drop table if EXISTS  testtable");
        $result = $sq->execute("create table if not EXISTS  testtable ( 'id' integer(10), 'name' char(32) )");

        $values = [];

        for ($i = 0; $i < 100; $i++) {

            $result = $sq->insert('testtable', [
                'id'   => rand(9, 9999),
                'name' => md5(rand(9, 9999)),
            ]);

            $values[] = [
                'id'   => rand(9, 9999),
                'name' => md5(rand(9, 9999)),
            ];
        }

        $sq->insertInTransaction('testtable', $values, false);

        /**
         * Select count and data
         */

        $data = $sq->fetch('select * from testtable');

        $this->assertTrue(count($data) == 200);

        $result = $sq->execute("drop table if EXISTS  testtable");
        $result = $sq->execute("vacuum");

        $sq->disconnect();

        @unlink($this->getTestDB());
    }

    public function testDisconnect()
    {
        $sq = SQLite::connect($this->getTestDB());
        $sq->disconnect();
        $this->assertTrue(true);
    }

}