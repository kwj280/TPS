<?php

/*
 * The MIT License
 *
 * Copyright 2016 J.oliver.
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

namespace TPS;

/**
 * Description of playlist
 *
 * @author J.oliver
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR."station.php";
class notification extends station{
    //put your code here
    
//    public function __construct(
//            $enableDbReporting = FALSE, $requirePDO = FALSE,
//            $settingsTarget = NULL, $settingsPath = NULL) {
//        parent::__construct($enableDbReporting, $requirePDO, $settingsTarget,
//                $settingsPath);
//    }

    public function __construct($callsign)
    {
        parent::__construct($callsign);
    }

    public function listUserNotifications($userName="*", $permission=1){
        #@todo: expand to accept array
        $stmt = $this->db->prepare("SELECT * FROM notification "
                . "WHERE (`station`=:station OR `station`='*') AND "
                . "CASE WHEN (:userName != '' AND :userName != '*') THEN "
                . "`userName`=:userName ELSE (`userName` = '*' OR `userName` IS null) END "
                . "AND `permissionRequired`<=:permission");
        $stmt->bindParam(":userName", $userName);
        $stmt->bindParam(":station", $this->callsign);
        $stmt->bindParam(":permission", $permission);
        $result = array();
        if(!$stmt->execute()){
            throw new \Exception($stmt->errorInfo());
        }
        else{
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                #print array_keys($row);
                $row3 =$row;
                $row2 = json_encode($row);
                error_log($row2);
                array_push($result, $row3);
            }
        }
        return $result;
    }

    public static function convertToMessageFormat($data){
        $result = array();
        foreach ($data as $value){
            $internal=array();
            $internal['image'] = array(
                'url'=>\TPS\util::get($value, 'url', "https://placehold.it/50x50"),
                'alt'=>\TPS\util::get($value, 'urlAlt', "Generic Placeholder"),
            );
            $internal['user'] = array(
                'name'=>\TPS\util::get($value, 'userName', 'System')
            );
            $internal['time'] = \TPS\util::get($value, 'time', date('now'));
            $internal['content'] = \TPS\util::get($value, 'message', "No Content Provided");
            array_push($result, $internal);
        }
        return $result;
    }
    
    public function setExpiry($playlistIds, $date){
       if(!is_array($playlistIds)){
            $playlistIds = array($playlistIds);
        }
        $param = NULL;
        $stmt = $this->db->prepare("UPDATE playlist SET `Expire`=:date"
            . " WHERE PlaylistId=:param");
        $stmt->bindParam(":param", $param);
        $stmt->bindParam(":date", $date);
        foreach($playlistIds as $param){
            if(!$stmt->execute()){
                throw new Exception($stmt->errorInfo());
            }
        }
        return true;
    }
    
    public function setStart($playlistIds, $date){
        if(!is_array($playlistIds)){
            $playlistIds = array($playlistIds);
        }
        $param = NULL;
        $stmt = $this->db->prepare("UPDATE playlist SET Activate=:date"
            . " WHERE PlaylistId=:param");
        $stmt->bindParam(":param", $param);
        $stmt->bindParam(":date", $date);
        foreach($playlistIds as $param){
            if(!$stmt->execute()){
                throw new Exception($stmt->errorInfo());
            }
        }
        return true;
    }
}
