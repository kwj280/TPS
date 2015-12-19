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

require 'logger.php';
require 'station.php';
class alert{
    public $polygon=array();
    public $title=array();
    public $provider="";
    public $updated="";
    public $image="";
    public $active = null;
    public $text=array();
    public $alertAuthority=array();
    public $expires="";
    public $id="";
    public $severity="";
    public $areas=array();
    public $href="";
    
    public function __construct() {
        $this->expires = new \DateTime();
        $this->updated = new \DateTime();
    }
    
    public function image($value=null){
        if($value){
            $this->image = $value;
        }
        return $this->image;
    }
    
    public function alertAuthority($value=null){
        if($value){
            if(is_array($value)){
                $value = array_pop($value);
            }
            array_push($this->alertAuthority,$value);
            #$this->title = $value;
        }
        return $this->alertAuthority;
    }
    
    public function areas($value=null){
        if($value){
            $this->areas .= $value;
        }
        return $this->areas;
    }
    
    public function expires($value=null){
        if($value){
            $this->expires = $value;
        }
        return $this->expires;
    }
    
    public function name($value=null){
        if($value){
            $this->name = $value;
        }
        return $this->name;
    }
    
    public function title($value=null){
        if($value){
            if(is_array($value)){
                $value = array_pop($value);
            }
            array_push($this->title,$value);
            #$this->title = $value;
        }
        return $this->title;
    }
    
    public function updated($value=null){
        if($value){
            $this->updated = $value;
        }
        return $this->updated;
    }
    
    public function text($value=null){
        if($value){
            if(is_array($value)){
                $value = array_pop($value);
            }
            array_push($this->text,$value);
            #$this->title = $value;
        }
        return $this->text;
    }
    
    public function id($value=null){
        if($value){
            $this->id = $value;
        }
        return $this->id;
    }
}


/**
 * Description of emergencyAlert
 *
 * @author James Oliver <support@ckxu.com>
 */
class emergencyAlert extends station{
    private $station = null;
    private $alerts = array();
    private $formatted = array();
    private $logger = Null;
    private $providers = array(
        "AEMA" => array(
            "feed" => "http://www.emergencyalert.alberta.ca/aeapublic/feed.atom",
            "logo" => "/images/AEMA.png",
            ),
        "NAAD/NPAS" => array(
            "feed" => "http://rss.naad-adna.pelmorex.com/",
            "logo" => "/images/NPAS.png",
            ),
    );
    public $location = "*";
    public $format = "json";
    private $exactMatchLocation = False;
    
    private function setParams(&$alert,&$previous,$alertDate){
        if(is_string($previous)){
            $previous = $this->alerts[$previous];
            assert(sizeof($previous)>0, 
                    "Could not retrieve previous alert");
        }
        $previous->title($alert->title);
        $previous->text($alert->text);
        $previous->alertAuthority($alert->alertAuthority());
        $previous->areas = array_merge($previous->areas,$alert->areas);
        if($alertDate < $previous->updated){
            return True;
        }
        return True;
    }

    public function __construct($station=Null, $locations=Null, 
            $sources=Null, $severity="all") {
        if($sources != Null && is_array($sources) ){
            $this->$providers = $sources;
        }
        $this->location = explode(",", $locations);
        $this->logger = new \TPS\logger();
        if($station){
            $this->station = new \TPS\station($station);
        }
    }
    
    public function locations($value){
        $this->location = $value;
    }
    
    public function exactMatchLocation($bool){
        assert(is_bool($bool), "non boolean value provided");
        $this->exactMatchLocation = $bool;
    }
    
    private function setAlertAlertSeverity(&$alert){
        $result = "Warning";
        if($alert->severity){
            return $alert->severity;
        }
        $term = end($alert->title);
        if(strpos($term,'Test') !== FALSE){
            $result = "Test";
        }
        elseif(strpos($term,'information')!==FALSE 
                || strpos($term,"advisory")!==FALSE
                || strpos($term,"special weather")!==FALSE
                || strpos($term,"bulletin météorologique spécial")!==FALSE){
            $result = "Information";
        }
        elseif(strpos($term, 'watch')){
            $result = "Watch";
        }
        // Otherwise return Warning (Unknown reason to downgrade)
        
        $alert->severity = $result;
        return $alert->severity;
    }
    
    private function setActive(&$alert){
        if(preg_match("/(?<=\s)(in effect)/", end($alert->title))){
            $alert->active = true;
        }
        elseif (preg_match("/(?<=\s)(ended)/", end($alert->title))) {
            $alert->active = false;
        }
    }
    
    private function parseAtomAlert(&$alert, &$alertObj){
        foreach($alert->children('http://www.georss.org/georss') as $geo){
            array_push($alertObj->polygon,end($geo));
        }
        $idStr = $alert->id;
        $id = explode("/",$idStr);
        if(is_array($id) && sizeof($id)){
            $id = end($id);
        }
        else{
            
        }
        $alertObj->id = $id;
        preg_match("/(?<=Expires:\s)([\d:\-T]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/", 
                $alert->summary, $matches);
        if(sizeof($matches)>0){
            try {
                if(sizeof($matches[0])>0){
                    $expires = new \DateTime($matches[0]);
                    $now = new\DateTime();
                    if($now > $expires){
                        # expired, no need to process
                        return true;
                    }
                    $alertObj->expires($expires);
                }
            } catch (Exception $exc) {
            }
        }
        $updated = new \DateTime($alert->updated);
        $alertObj->updated($updated);
        $title = $alert->title;
        $alertObj->title($title);
        $alertObj->alertAuthority($alert->author->name);
        $this->setAlertAlertSeverity($alertObj);
        
        preg_match("/(?<=Area:\s)([^\n\<]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/", 
                $alert->summary, $areas);
        if(sizeof($areas)<1){
            $areas = $alert->summary;
            if(!in_array($this->location,$area)){
                // does not include given locations
                return FALSE;
            }
        }
        else{
            $areas = explode(',', $areas[0]);
            $validArea = False;
            foreach ($areas as $area){
                foreach ($this->location as $location) {
                    if($location == "*" || strpos($area,$location) !== false){
                        if($this->exactMatchLocation && $area != $location){
                            continue;
                        }
                        // does not include given locations
                        $validArea = TRUE;
                        break;
                    }
                }
            }
            if(!$validArea){
                return True;
            }
        }
        $alertObj->areas = $areas;
        
        preg_match("/(?<=Description:\s)([^\n\<]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/", 
                $alert->summary, $description);
        if(sizeof($description)<1){
            $description = $alert->summary;
        }
        if(!is_string($description)){
            $alertObj->text($description[0]);
        }
        else{
            $alertObj->text($description);
        }
        
        foreach($alert->link->attributes() as $key => $value){
            if($key == "href"){
                $alertObj->href = end($value);
            }
        }
        
        $alertDate = new \DateTime($alert->updated);
        $previousAlerts = array_map(function($x) use ($alertDate){
            if($x->updated == $alertDate){
                return $x;
            }
        },$this->alerts);
        if(key_exists($id,$this->alerts) || sizeof($previousAlerts)>0){
            foreach ($previousAlerts as $previous) {
                if($previous 
                        && sizeof($alertObj->areas) == 
                        sizeof($previous->areas)){
                    return $this->setParams($alertObj, $previous, $alertDate);
                }
            }
        }
        $this->setActive($alertObj);
        if(!sizeof($id)){
            array_push($this->alerts, $alertObj);
        }
        else{
            $this->alerts[(string)$id] = $alertObj;
        }
    }
    
    private function parseAtomAlerts($source, $provider, $logo){
        //$xml = file_get_contents($source);
        //$entries = new \SimpleXmlElement($xml);
        $entries = simplexml_load_file($source);
        $entries->registerXPathNamespace('prefix', 'http://www.w3.org/2005/Atom');
        $results = $entries->xpath("//prefix:entry");
        foreach ($results as $entry) {
            $alertObj = new \TPS\alert();
            $alertObj->image($logo);
            $alertObj->provider = $provider;
            $this->parseAtomAlert($entry, $alertObj);
        }
        
    }
    
    protected function checkAlert($provider, $data){
        if(!key_exists("feed",$data) || !key_exists("logo", $data)){
            throw new Exception("Missing key value from alert data");
        }
        $source = $data["feed"];
        $logo = $data["logo"];
        $type = "atom";
        if(key_exists("type", $data)){
            $type = strtolower($data['type']);
        }
        if($type == "atom"){
            try {
                $this->parseAtomAlerts($source, $provider, $logo);
            } catch (Exception $exc) {
                $exc->getTraceAsString();
            }
        }        
    }

    protected function checkAlerts($provider=Null){
        foreach ($this->providers as $key => $value) {
            if ($provider != Null && !in_array($key,$provider)){
                continue;
            }
            try {
                $this->checkAlert($key, $value);
            } catch (Exception $exc) {
                $trace = $exc->getTraceAsString();
                $this->logger->error("Check alerting partner failed", $trace);
            }
        }
    }
    
    protected function formatAlert($provider){
        
    }

    protected function formatAlerts(){
        $this->formatted = $this->alerts;
    }
    
    protected function printAlert($index){
        
    }
    
    protected function printAlerts(){
        
    }

    public function run(){
        $this->checkAlerts();
        $this->formatAlerts();
        if($this->format == "json"){
            return json_encode($this->formatted);
        }
        else{
            return $this->printAlerts();
        }
    }
}