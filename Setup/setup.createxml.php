<?php

if(!isset($_SESSION)){
    session_start();
}

/* 
 * http://stackoverflow.com/questions/3755952/save-array-as-xml
 * 
 * Generates XML Login Script for Dynamic Login.
 */

/*
 * DBTYPE used to be used for connections to MSSQL Servers, now deprecated.
 */


include_once '../TPSBIN/functions.php';

$URR=$_SESSION['user'];
$PDR=$_SESSION['password'];

$USR=easy_crypt(ENCRYPTION_KEY,$URR);
$PWD=easy_crypt(ENCRYPTION_KEY,$PDR);
        
$SERV = [
    "ID"=>  $_SESSION['callsign'] . rand(0, 999),
    "NAME"=> $_SESSION['brand'],
    "LOGO_PATH"=>"images/logo.png",
    "MENU_LOGO_PATH"=>"images/logo.png",
    "ACTIVE"=>1,
    "RESOLVE"=>"URL",
    "URL"=>$_SESSION['host'],
    "IPV4"=>$_SESSION['host'],
    "PORT"=>$_SESSION['port'],
    "DBTYPE"=>"MySQL",
    "DATABASE"=>$_SESSION['database'],
    "AUTH"=>$_SESSION['authtype'],
    "LDP_PORT"=>$_SESSION['ldap_port'],
    "LDP_SERVER"=>$_SESSION['ldap_server'],
    "LDP_BASE_DN"=>$_SESSION['ldap_dn'],
    "LDP_DOMAIN"=>$_SESSION['ldp_domain'],
    "USER"=>$USR,
    "PASSWORD"=>$PWD
    
];

// save
$doc = new DOMDocument('1.0');
$doc->formatOutput = true;
$root = $doc->createElement('SERVERS');
$root = $doc->appendChild($root);
$server = $doc->createElement('SERVER');
$root->appendChild($server);
foreach($SERV as $key=>$value)
{
   $em = $doc->createElement($key);       
   $text = $doc->createTextNode($value);
   $em->appendChild($text);
   $server->appendChild($em);

}
$doc->save('../TPSBIN/XML/DBSETTINGS.xml');
chmod('../TPSBIN/XML/DBSETTINGS.xml',0600);

// load
/*
 $arr = array();
 $doc = new DOMDocument();
 $doc->load('file.xml');
 $root = $doc->getElementsByTagName('root')->items[0];
 foreach($root->childNodes as $item) 
 { 
   $arr[$item->nodeName] = $item->nodeValue;
 }
 * */