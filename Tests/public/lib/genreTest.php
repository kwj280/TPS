<?php

namespace TPS;
include dirname(__FILE__).DIRECTORY_SEPARATOR.
                '../../../public/lib/tps.php';
include dirname(__FILE__).DIRECTORY_SEPARATOR.
                '../../../public/lib/genre.php';

global $pdo;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-20 at 08:29:54.
 */
class genreTest extends \PHPUnit_Extensions_Database_TestCase {

    /**
     * @var genre
     */
    protected $object;
    protected $tps;
    protected $pdo;


    public function __construct() {
        #$this->pdo = new \PDO('sqlite::memory:');
        global $pdo;
        $pdo = new \PDO('sqlite::memory:');
        $this->pdo = $pdo;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        global $pdo;
        #http://stackoverflow.com/questions/25901470/phpunit-extensions-database-testcase-returns-undefined-method
        #$this->getDataSet();
        #$this->pdo = $this->getConnection()->createDataSet();
        $this->pdo = $this->getConnection()->getConnection();
        $this->tps = new \TPS\TPS(FALSE, TRUE);
        $this->object = new \TPS\genre("NCRA");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        unset($GLOBALS['pdo']);
    }

    /**
     * @covers TPS\genre::create
     * @todo   Implement testCreate().
     */
    public function testCreate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers TPS\genre::all
     * @todo   Implement testAll().
     */
    public function testAll() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers TPS\genre::get
     * @todo   Implement testGet().
     */
    public function testGet() {
        global $pdo;
        $result1 = $this->object->get("Test");
        $compare1["Test"] = array(
                "governmentRequirements" => array(
                    "type" => 0,
                    "numeric" => 1,
                    "percentage" => 0.35,
                ),
                "playlistRequirements" => array(
                    "type" => 0,
                    "numeric" => 1,
                    "percentage" => 0.4,
                ),
                "UID" => 18,
                "station" => "NCRA",
            );
        $result2 = $this->object->get("Row2");
        $compare2["Row2"] = array(
                "governmentRequirements" => array(
                    "type" => 0,
                    "numeric" => 0,
                    "percentage" => 0.2501,
                ),
                "playlistRequirements" => array(
                    "type" => 1,
                    "numeric" => 0,
                    "percentage" => 0.33,
                ),
                "UID" => 999,
                "station" => "TEST",
            );
        $this->assertEquals($result1,array(),#$compare1,
                "result returned for genre differs from expected result for"
                . "test result #1 (Test)");
        $this->assertEquals($result2,array(),
                "result returned for genre differs from expected result for"
                . "test result #2 (Test)");
    }
    
    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        global $pdo;
        #return $this->createDefaultDBConnection($pdo, ':memory:');
        return $this->createDefaultDBConnection($this->pdo, 'sqlite');
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(dirname(__FILE__).DIRECTORY_SEPARATOR.
                '_files'.DIRECTORY_SEPARATOR.'genreTestSeed.xml');
    }


}
