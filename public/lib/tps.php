<?php
namespace TPS;
/* 
 * The MIT License
 *
 * Copyright 2015 James Oliver <support@ckxu.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.
        '../../TPSBIN'.DIRECTORY_SEPARATOR."functions.php";

class TPS{
    protected $mysqli;
    protected $db; //USE PDO database
    protected $mysqliDriver;
    protected $username;
    private $requirePDO;
    
    private function getDatabaseConfig($target=NULL,$xmlpath=NULL){
        if($xmlpath === NULL){
            $xmlpath = dirname(__FILE__).DIRECTORY_SEPARATOR."../../TPSBIN"
                    .DIRECTORY_SEPARATOR."XML"
                    .DIRECTORY_SEPARATOR."DBSETTINGS.xml";
        }
        if(!file_exists($xmlpath)){
            $xmlpath = get_include_path() . $xmlpath;
        }
        try{
            if(file_exists($xmlpath))
            {
                $dbxml = simplexml_load_file($xmlpath);
            }
            else{
                return FALSE;
            }
        } catch (Exception $ex) {
            error_log($ex);
            return FALSE;
        }
        if(is_null($target)){
            // Get the default (first) server
            foreach ($dbxml->SERVER as $server){
                if(strtolower($server->ACTIVE) == "true" 
                        || $server->ACTIVE == '1'){
                    $target = (string)$server->ID;
                    break;
                }
            }
        }
        $database=False;
        foreach ($dbxml->SERVER as $server){
            if((string)$server->ID === $target){
                if($server->RESOLVE === "URL")
                {
                  $database["DBHOST"] = $server->URL;
                }
                elseif($server->RESOLVE === "IPV4")
                {
                  $database["DBHOST"] = $server->IPV4;
                }
                else
                {
                    if($server->URL!=""){
                        $database["DBHOST"] = $server->URL;
                    }
                    else{
                        $database["DBHOST"] = $server->IPV4;
                    }
                }
                $database["USER"] = easy_decrypt(
                        ENCRYPTION_KEY,(string)$server->USER);
                $database["PASSWORD"] = easy_decrypt(
                        ENCRYPTION_KEY,(string)$server->PASSWORD);
                $database["DATABASE"] = (string)$server->DATABASE;
                $database["TYPE"] = $server->DBTYPE;
            }
        }
        return $database;
    }
    
    /**
     * @access public
     * @param int $pagination current page index
     * @param int $maxResult number of items to in response
     */
    public static function sanitizePagination(&$pagination,&$maxResult){
        if( !is_int($maxResult) || $maxResult > 1000 || $maxResult<1):
            $maxResult = 1000;
        endif;
        if( !is_int($pagination) || $pagination<1):
            $pagination = 1;
        endif;
        $floor = abs(($pagination*$maxResult))-($maxResult+1);
        $ceil = abs(($pagination*$maxResult));
        // Simply for security. should never happen
        if ($floor < 0):
            $floor=0;
        endif;
        if($ceil < 0):
            $ceil = abs($ceil);
        endif;
        $pagination = $floor;
        $maxResult = $ceil;
    }

    public function __construct($enableDbReporting=FALSE, $requirePDO=FALSE,
            $settingsTarget=NULL, $settingsPath=NULL) {
        global $mysqli;
        global $db;
        $mysqli=$mysqli?:$GLOBALS['mysqli'];
        $db=$db?:$GLOBALS['db'];
        $this->requirePDO = $requirePDO;
        if(!$mysqli || !$db){
            // Establish DB connection
            $database = NULL;
            if($database = $this->getDatabaseConfig($settingsTarget,
                    $settingsPath)){
                if($database['TYPE']=="PDO"){
                    $this->requirePDO = TRUE;
                }
                $databaseHost = $database['DBHOST'];
                $databaseName = $database['DATABASE'];
                if($this->requirePDO){
                    $this->db = new \PDO(
                            "mysql:host=$databaseHost;dbname=$databaseName",
                        $database['USER'], $database['PASSWORD']);
                    $this->mysqli = $this->db;
                }
                else{
                    $this->db = new \PDO(
                            "mysql:host=$databaseHost;dbname=$databaseName",
                        $database['USER'], $database['PASSWORD']);
                    $this->mysqli = new \mysqli(
                        $database['DBHOST'], 
                        $database['USER'], 
                        $database['PASSWORD'], 
                        $database['DATABASE']
                        );
                }
                if(!$this->mysqli->connect_error){
                    $GLOBALS['mysqli'] = $this->mysqli;
                }
                if(!$this->db->errorCode()){
                    $GLOBALS['db'] = $this->db;
                }
            }
            else{
                $this->mysqli = $mysqli?:NULL;
                $this->db = $db?:NULL;
            }
        }
        else{
            $this->mysqli = $mysqli;
            $this->db = $db;
        }
        if(!$this->mysqliDriver){
            $this->mysqliDriver = new \mysqli_driver();
            if($enableDbReporting){
                $this->mysqliDriver->report_mode = MYSQLI_REPORT_ALL;
            }
            else{
                $this->mysqliDriver->report_mode = MYSQLI_REPORT_ERROR;
            }
        }
    }
    
    protected function updateParent(){
        /**
         * Nothing to update, start updating children
         */
        return update();
    }
    
    private function update(){
        return TRUE;
    }
    
    public function getStations(){
        $callsign = null;
        $name = null;
        if($con = $this->mysqli->prepare(
                "SELECT callsign, stationname FROM station")){
            $stations = array();
            $con->execute();
            $con->bind_result($callsign,$name);
            while($con->fetch())
            {
                $stations[$callsign]=$name;
            }
            return $stations;
        }
        else{
            return false;
        }
        
    }
    /*
     * should be used to create top level params such as DB connection
     */
}
