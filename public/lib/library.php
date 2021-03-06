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

/**
 * @author James Oliver <support@ckxu.com>
 * @abstract library interface
 * @version 1.0
 */
use PhpParser\Builder\Trait_;

require_once 'station.php';

class library extends station{

    public $playlist = null;

    public function __construct($callsign=null){
        parent::__construct($callsign);
        $this->playlist = new \TPS\playlist();
    }
    #protected $RefCode;

    /**
     *
     * @global mysqli $mysqli
     * @param type $RefCode Reference Code for Album
     * @return array Array of websites, False on Error
     * @version 0.1
     */
    public function getWebsitesByRefCode($RefCode){
        $this->mysqli;
        $websites = array();
        $selectWebsites = "Select band_websites.URL, band_websites.Service, band_websites.date_available, band_websites.date_discontinue"
                    . " from band_websites where band_websites.ID=?;";
        if($bands = $this->mysqli->prepare($selectWebsites)){
            $bands->bind_param('i',$RefCode);
            $bands->execute();
            $bands->bind_result($url,$service,$available,$discontinue);
            while($bands->fetch()){
                $websites[$service]=array(
                    "url" => $url,
                    "available" => $available,
                    "discontinue" => $discontinue);
            }
            $bands->close();
        }
        else{
            error_log($this->mysqli->errno.": ".$this->mysqli->error);
            return FALSE;
        }
        return $websites;
    }

    /**
     * @abstract returns static associative array of genres
     * @todo implement database storage
     * @return array
     * @version 0.1
     */
    public function getLibraryGenres(){
        $genres = array(
                "RP" => "0--- Rock/Pop",
                "FR" => "1--- Folk/Roots",
                "HM" => "2--- Heavy/Punk/Metal",
                "EL" => "3--- Electronic",
                "HH" => "4--- Hip-Hop",
                "WD" => "5--- World",
                "JC" => "6--- Jazz/Classical",
                "EX" => "7--- Experimental",
                "OT" => "8--- Other",
            );
        return $genres;
    }


    /**
     * @abstract Library codes are prepended to a barcode and used
     * for indexing and management within a physical library.
     * @todo store in DB
     * @return array
     * @version 1.0
     */
    public function listLibraryCodes(){
        $codes = array(
            "0" => array(
                "Title"=>"Rock/Pop",
                "Genre"=>"RP",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Rock",
                    "21"=>"Pop",
                    "21"=>"Alternative",
                    "21"=>"Electronic Pop",
                    "21"=>"Funk",
                    "23"=>"Acoustic Versions",
                    "24"=>"Easy Listening",
                    )
                ),
            "1" => array(
                "Title"=>"Folk/Roots",
                "Genre"=>"FR",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Folk Rock",
                    "21"=>"Singer-Songwriter",
                    "21"=>"Blues Rock",
                    "22"=>"Country",
                    "32"=>"Traditional Folk",
                    "34"=>"Traditional Blues",
                    )
                ),
            "2" => array(
                "Title"=>"Heavy",
                "Genre"=>"HM",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Metal",
                    "21"=>"Punk",
                    "21"=>"Hardcore",
                    "21"=>"Hard Rock",
                    )
                ),
            "4" => array(
                "Title"=>"Hip-hop/Rap",
                "Genre"=>"HH",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Hip-hop/Rap",
                    )
                ),
            "5" => array(
                "Title"=>"World",
                "Genre"=>"WD",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "35"=>"World Pop",
                    "35"=>"Traditional World",
                    "35"=>"3rd Language",
                    )
                ),
            "6" => array(
                "Title"=>"Jazz/Classical",
                "Genre"=>"JC",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "31"=>"Classical",
                    "34"=>"Jazz",
                    )
                ),
            "7" => array(
                "Title"=>"Experimental",
                "Genre"=>"EX",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>9,
                ),
                "SubGenres" => array(
                    "36"=>"Avant composition",
                    "36"=>"Noise",
                    "36"=>"Field Recordings",
                    "36"=>"Acousmatic",
                    )
                ),
            "8" => array(
                "Title"=>"Other",
                "Genre"=>"OT",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>9,
                ),
                "SubGenres" => array(
                    "12"=>"Spoken Word",
                    "12"=>"Comedy",
                    "12"=>"Radio Drama",
                    "11"=>"News",
                    "35"=>"Religious",
                    )
                ),
            "9" => array(
                "Title"=>"System",
                "Genre"=>null,
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>3,
                ),
                "SubGenres" => array(
                    "45"=>"Tech Test"
                )
            )
        );
        return $codes;
    }

    public function getLibraryCodeByGenre($Genre) {
        $list = $this->listLibraryCodes();
        $comVal='Genre';
        foreach ($list as $key => $value) {
            if ($value[$comVal] == $Genre){
                return $key;
            }
        }
        return False;
    }

    public function getLibraryCodeValueByGenre($Genre) {
        $list = $this->listLibraryCodes();
        $comVal='Genre';
        foreach ($list as $key => $value) {
            if ($value[$comVal] == $Genre){
                return $value;
            }
        }
        return False;
    }

    public function getLibraryCodeByRefCode($RefCode) {
        $album = $this->getAlbumByRefcode($RefCode, TRUE)[0];
        return $this->getLibraryCodeByGenre($album['genre']);
    }

    public function createBarcode($refcode){
        if ($stmt = $this->mysqli->prepare(
                    "UPDATE library SET Barcode=? WHERE RefCode=?")){
            $stmt->bind_param("ii",$Barcode,$RefCode);
            $stmt->execute();
            $stmt->close();
        }
        else{
            throw new Exception(
                      "Could not create barcode ".$this->mysqli->error);
        }
    }

    public function getBarcodeByRefCode($RefCode){
        $attr = $this->getAlbumByRefcode($RefCode, True);
        if(is_null($attr['barcode'])){
            $barcode = $this->createBarcode($RefCode);
        }
        else{
            $barcode = $attr['barcode'];
        }
        return $barcode;
    }

    /**
     * @abstract gets static associative array of covernment codes
     * @todo store in DB
     * @return string
     * @version 0.1
     */
    public function getGovernmentCodes(){
        $govCat = array(
                // CRTC Categories http://www.crtc.gc.ca/eng/archive/2010/2010-819.HTM
                "21" => "Pop, rock and dance",
                "11" => "News",
                "12" => "Spoken word-other",
                "22" => "Country and country-oriented",
                "23" => "Acoustic",
                "24" => "Easy listening",
                "31" => "Concert",
                "32" => "Folk and folk-oriented",
                "33" => "World beat and international",
                "34" => "Jazz and blues",
                "35" => "Non-classic religious",
                "36" => "Experimental Music",
                "41" => "Musical themes, bridges and stingers",
                "42" => "Technical tests",
                "43" => "Musical station identification",
                "44" => "Musical identification of announcers, programs",
                "45" => "Musical promotion of announcers, programs",
                "51" => "Commercial announcement",
                "52" => "Sponsor Identification",
                "53" => "Promotion with sponsor mention",
            );
        return $govCat;
    }

    /**
     *
     * @abstract return Media Fromats for library
     * @todo store in db
     * @return array
     * @version 0.1
     */
    public function getMediaFormats(){
        $formats = array(
                "CD" => "Compact Disc",
                "Digital"=>"Digital",
                "12in" => "12\"",
                "10in" => "10\"",
                "7in" => "7\"",
                "Cass" => "Cassette",
                "Cart"=>"Fidelipac (cart)",
                "MD" => "Mini Disc",
                "Other"=>"Other"
            );
        return $formats;
    }

    /**
     *
     * @abstract return artist locale for library
     * @todo store in db
     * @return array
     * @version 0.1
     */
    public function getLocales(){
        $formats = array(
            "International" => "International",
            "Country" => "Country",
            "Province" => "Province",
            "Local" => "Local"
        );
        return $formats;
    }

    /**
     * @abstract provide schedule information is associative array
     * @todo implement database storage
     * @return string
     */
    public function getScheduleBlocks(){
        $blocks = array(
            NULL => "Select",
            "D" => "Daytime [06:00-21:00]",
            "N" => "Nighttime [21:00-06:00]",
        );
        return $blocks;
    }

    /**
     *
     * @abstract return label information based on id
     * @global mysqli $mysqli
     * @param int $labelid label identification number
     * @return boolean|array
     */
    public function getLabelbyId($labelid){
        $this->mysqli;
        $result = array();
        /*elseif(!$exact){
            $refcode="%{$refcode}%";
        }*/
        if($stmt = $this->mysqli->prepare("SELECT LabelNumber, Name, Location, Size,"
                . "name_alias_duplicate as alias, updated, verified FROM recordlabel"
                . " WHERE LabelNumber=?")){
            $stmt->bind_param('i',$labelid);
            $stmt->execute();
            $stmt->bind_result($LabelNumber,$name,$location,$size,$alias,$updated,
                    $verified);
            while($stmt->fetch()){
                array_push($result, array(
                    'labelNumber'=>$LabelNumber,'name'=>$name,'location'=>$location,
                    'size'=>$size,'alias'=>$alias,'updated'=>$updated,'verified'=>$verified,
                ));
            }
            $stmt->close();
        }
        else{
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    /**
     * set status of refcode(s)
     * @param array $refcodes
     * @param int $status
     * @return boolean
     */
    protected function setStatus($refcodes, $status){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        $refcode = NULL;
        $stmt = $this->db->prepare("UPDATE library SET status=:status"
            . " WHERE RefCode=:refcode");
        $stmt->bindParam(":refcode", $refcode, \PDO::PARAM_STR);
        $stmt->bindParam(":status", $status, \PDO::PARAM_INT);
        foreach($refcodes as $refcode){
            $stmt->execute();
        }
        return true;
    }

    /**
     * enable library entries by refcode
     * @param type $refcodes
     * @return type
     */
    public function enable($refcodes){
        return $this->setStatus($refcodes, 1);
    }

    /**
     * disable library entries by refcodes
     * @param type $refcodes
     * @return type
     */
    public function disable($refcodes){
        return $this->setStatus($refcodes, 0);
    }

    /**
     * Change an Attribute
     * @param type $refcodes
     * @param type $attribute
     * @param type $value
     * @return boolean
     */
    public function attribute($refcodes, $value, $attribute){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        //$quote = $this->db->quote($attribute);
        #$quote = "`"+$attribute+"`";
        $quote = $attribute;
        if(strpos($quote, ";")){
            throw new \Exception("Invalid Request");
        }
        $refcode = NULL;
        $stmt = $this->db->prepare("UPDATE library SET $quote=:value"
            . " WHERE RefCode=:refcode");
        $stmt->bindParam(":refcode", $refcode, \PDO::PARAM_STR);
        $stmt->bindParam(":value", $value);
        foreach($refcodes as $refcode){
            $stmt->execute();
        }
        return true;
    }

    public function pendingPlaylist(){
        return $this->playlistFetch('PENDING');
    }

    public function completePlaylist(){
        return $this->playlistFetch('COMPLETE');
    }

    public function rejectedPlaylist(){
        return $this->playlistFetch('FALSE');
    }

    private function playlistFetch($flag){
        $stmt = $this->db->prepare(
                "SELECT * FROM library"
            . " WHERE playlist_flag=:flag and "
            . " status=1");
        $stmt->bindParam(":flag", $flag);
        $result = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($result,$row);
            }
        }
        return $result;
    }

    public function playlistStatus($refcodes, $value){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        //$quote = $this->db->quote($attribute);
        #$quote = "`"+$attribute+"`";
        $quote = $value;
        if(strpos($quote, ";")){
            throw new \Exception("Invalid Request");
        }
        $refcode = NULL;
        $stmt = $this->db->prepare("UPDATE library SET playlist_flag=:value"
            . " WHERE RefCode=:refcode");
        $stmt->bindParam(":refcode", $refcode, \PDO::PARAM_STR);
        $stmt->bindParam(":value", $value);
        $status = [];
        foreach($refcodes as $refcode){
            $status[$refcode] = $stmt->execute();
        }
        return $status;
    }

    /**
     *
     * @abstract get album information from library by RefCode
     * @global \TPS\mysqli $mysqli
     * @param string $refcode
     * @param boolena #exact
     * @return boolean|array
     */
    public function getAlbumByRefcode($refcode,$exact=FALSE){
        $this->mysqli;
        $result = array();
        if($refcode===Null){
            $refcode='%';
        }
        /*elseif(!$exact){
            $refcode="%{$refcode}%";
        }*/
        if($stmt = $this->mysqli->prepare("SELECT Barcode,year,datein,dateout,RefCode,artist,album,"
                . "`format`,variousartists,`condition`,genre,`status`,labelid,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,governmentCategory,"
                . "scheduleCode "
                . "FROM library where "
                . "Refcode = ?")){
            $stmt->bind_param('s',$refcode);
            $stmt->execute();
            $stmt->bind_result($barcode,$year,$datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,$labelid,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$govCat,$scCode);
            while($stmt->fetch()){
                array_push($result, array(
                    'barcode'=>$barcode,'year'=>$year,
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'labelid'=>$labelid,
                    'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                    'release_date'=>$release_date,
                    'note'=>$note,'playlist_flag'=>$playlist_flag,
                    'governmentCategory'=>$govCat,'scheduleCode'=>$scCode,
                ));
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    /**
     * More general search of the library that matches on any
     * of the following columns: artist, album, note, Locale, or Genre
     * @abstract Get all key library information based on \
     * given input and return in json format \
     * all values that match the paramaters. \
     * Similar to SeathcLibraryWithAlbum but broader search
     * @todo convert to wrapper for searchLibraryWithAlbum
     * @global \TPS\mysqli $mysqli
     * @param string $term
     * @param boolean $exact
     * @param string $sort columns name to sort result by
     * @return boolean|array
     */
    public function searchLibrary($term,$exact=False,$page=1,$limit=1000, $sort='RefCode', $reverse=false){
        //$this->mysqli;
        $tps = new \TPS\TPS();
        $tps->sanitizePagination($page, $limit);
        $result = array();
        if(!$exact){
            $term="%{$term}%";
        }
        $cols = $tps->listDatabaseColumns('library');
        if(!in_array($sort, $cols, true)){
            $sortIndex = array_find($sort, $cols);
            if(!$sortIndex){
                throw new \Exception("requested column does not exist in specified table");
            }
            else{
                $oldSort = (string)$sort;
                $sort = $cols[$sortIndex];
                $this->log->warn("table column `$oldSort` requested but wrong case found, will use `$sort`",
                    200, 'library search');
            }
        }
        $direction = $reverse?"DESC":"";
        $saniCol = "";
        try{
            $saniCol = $this->mysqli->real_escape_string($sort);
        }
        catch (\Exception $e){
            # PDO
            $saniCol = $this->db->quote($sort);
        }
        if($stmt = $this->mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
                . "`format`,variousartists,`condition`,genre,`status`,labelid,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,year "
                . "FROM library where "
                . "artist like ? or album like ? or note like ? or"
                . " Locale like ? or genre like ? ORDER BY `library`.`$saniCol`, `library`.`RefCode` "
                . "$direction limit ?,?")){
            $stmt->bind_param('sssssii', $term, $term, $term, $term, $term, $page, $limit);
            $stmt->execute();
            $stmt->bind_result($datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,$labelid,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$year);
            while($stmt->fetch()){
                array_push($result, array(
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'labelid'=>$labelid,
                    'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                    'release_date'=>$release_date,
                    'note'=>$note,'playlist_flag'=>$playlist_flag,'year'=>$year,
                ));
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    public function countSearchLibrary($term="",$exact=False){
        //$this->mysqli;
        $tps = new \TPS\TPS();
        $tps->sanitizePagination($page, $limit);
        $result = 0;
        if(!$exact){
            $term="%{$term}%";
        }
        if($stmt = $this->mysqli->prepare("SELECT count(*)"
                . "FROM library where "
                . "artist like ? or album like ? or note like ? or"
                . " Locale like ? or genre like ?")){
            $stmt->bind_param('sssss',$term,$term,$term,$term,$term);
            $stmt->execute();
            $stmt->bind_result($count);
            while($stmt->fetch()){
                $result+=$count;
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    /**
     * @abstract Get all key library information based on
     * given input and return in json format
     * all values that match the paramaters
     * Was GetLibraryFull()
     * @global \TPS\mysqli $mysqli
     * @param type $artist
     * @param type $album
     * @return boolean|array
     */
    function searchLibraryWithAlbum($artist, $album=NULL,$exact=FALSE){
        $this->mysqli;
        $result = array();
        $artist = urldecode($artist);
        if($album){
            $album = urldecode($album);
        }
        if($artist===Null){
            $artist='%';
        }
        elseif(!$exact){
            $artist="%{$artist}%";
        }
        if($album===Null){
            $album='%';
        }
        elseif(!$exact){
            $album="%{$album}%";
        }
        if($stmt = $this->mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
                . "`format`,variousartists,`condition`,genre,`status`,labelid,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,year "
                . "FROM library where "
                . "artist like ? and album like ?")){
            $stmt->bind_param('ss',$artist,$album);
            $stmt->execute();
            $stmt->bind_result($datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,$labelid,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$year);
            while($stmt->fetch()){
                array_push($result, array(
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'labelid'=>$labelid,
                    'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                    'release_date'=>$release_date,
                    'note'=>$note,'playlist_flag'=>$playlist_flag,'year'=>$year
                ));
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    /**
     * @abstract provide basic list of all albums
     * @global \TPS\mysqli $mysqli
     * @return string|array
     * @todo add pagination
     */
    public function ListAll(){
        $this->mysqli;
        if(is_null($this->mysqli)){
            return '';#$mysqli = $GLOBALS['db'];
        }
        $result = [];
        $library = $this->mysqli->query(
                "SELECT RefCode,artist,album,status FROM library");
        while($result_temp = $library->fetch_array(MYSQLI_ASSOC)){
            array_push($result, $result_temp);
        }
        return $result;
    }

    /**
     * Takes a RefCode and returns an array with all library information included
     * @global \TPS\mysqli $mysqli
     * @param type $term
     * @return array|boolean
     * @todo Optomize
     */
    public function GetFullAlbum($term){
        $this->mysqli;
        $maxResult = 100;
        $selectAlbum = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite,if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber left join band_websites on library.RefCode=band_websites.ID where "
                . "library.refcode = ? order by library.datein asc limit ?;";
        $selectWebsites = "Select band_websites.URL, band_websites.Service, band_websites.date_available, band_websites.date_discontinue"
                . " from band_websites where band_websites.ID=?;";
        $params = array();
        if($stmt = $this->mysqli->prepare($selectAlbum)){
            $stmt->bind_param('si',$term,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                    $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID);
            while($stmt->fetch()){
                $params['album'] = array( // this is ok as if the review ID is null there will also be no other entries as ID is a PK
                        "RefCode"=>$RefCode,
                        "hasWebsite"=>$hasWebsite,
                        "hasReview"=>$reviewed,
                        "format"=>$format,
                        "year"=>$year,
                        "album"=>$album,
                        "artist"=>$artist,
                        "CanCon"=>$canCon,
                        "datein"=>$datein,
                        "playlist"=>$playlist_flag,
                        "genre"=>$genre,
                        "locale"=>$locale,
                        "variousArtists"=>$variousArtists,
                        "label"=>array(
                            "Name"=>$recordLabel,
                            "Id"=>$labelid,
                        ),
                    );
            }
            $stmt->close();
        }
        else{
            $params['error']=$mysqli->error;
        }
        if($bands = $this->mysqli->prepare($selectWebsites)){
            $websites = array();
            $bands->bind_param('i',$term);
            $bands->execute();
            $bands->bind_result($url,$service,$available,$discontinue);
            while($bands->fetch()){
                $websites[$service]=array(
                    "url" => $url,
                    "available" => $available,
                    "discontinue" => $discontinue);
            }
            $bands->close();
        }
        else{
            error_log($this->mysqli->errno.": ".$this->mysqli->error);
            $params['error']=$this->mysqli->error;
        }
        $params['websites']=$websites?:NULL;
        return $params;
    }

    public function createAlbum($artist, $album, $format, $genre, $labelNum, $locale, $CanCon, $playlist,
                                $governmentCategory, $schedule, $note="", $accepted=1, $variousartists=False,
                                $datein=null, $release_date=null, $print=1){
        if(is_null($datein)){
            $datein = date("Y-m-d");
        }
        if(!$stmt3 = $this->mysqli->prepare("INSERT INTO library(datein,artist,album,variousartists,
            format,genre,status,labelid,Locale,CanCon,release_date,year,note,playlist_flag,
            governmentCategory,scheduleCode)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")){
            $stmt3->close();
            header("location: ./?q=new&e=".$stmt3->errno."&s=3&m=".$stmt3->error);
        }
        if(!is_null($release_date)){
            $year = date('Y',strtotime($release_date));
        }
        else{
            $year = NULL;
        }
        if(!$stmt3->bind_param(
            "sssissiisisssiss",
            $datein,
            $artist,
            $album,
            $variousartists,
            $format,
            $genre,
            $accepted,
            $labelNum,
            $locale,
            $CanCon,
            $release_date,
            $year,
            $note,
            $playlist,
            $governmentCategory,
            $schedule
        )){
            $stmt3->close();
            return $this->mysqli->error;
        }

        if(!$stmt3->execute()){
            error_log("SQL-STMT Error (SEG-3):[".$this->mysqli->errno."] ".$this->mysqli->error);
            $error = [$this->mysqli->errno,$this->mysqli->error];
            $stmt3->close();
            return $this->mysqli->error;
        }
        else{
            $url = "";
            $service = "";
            $id_last = $stmt3->insert_id;
            $stmt3->close();
            if($stmt4=$this->mysqli->prepare("INSERT INTO band_websites (ID,URL,Service) VALUES (?,?,?)")){
                $stmt4->bind_param("iss",$id_last,$url,$service);
                $services=[
                    "twitter"=>filter_input(INPUT_POST, 'twitter',FILTER_SANITIZE_URL),
                    "facebook"=>filter_input(INPUT_POST, 'facebook',FILTER_SANITIZE_URL),
                    "bandcamp"=>filter_input(INPUT_POST, 'bandcamp',FILTER_SANITIZE_URL),
                    "soundcloud"=>filter_input(INPUT_POST, 'soundcloud',FILTER_SANITIZE_URL),
                    "website"=>filter_input(INPUT_POST, 'website',FILTER_SANITIZE_URL)
                ];
                if(strpos($services["bandcamp"], "soundcloud.com")&&(is_null($services['soundcloud'])||$service['soundcloud']==''))
                {
                    // if soundcloud is in the bandcamp URL, reassign it to soundcloud
                    $services["soundcloud"] = $services["bandcamp"];
                    $services["bandcamp"] = NULL;
                }
                foreach($services as $key=>$value){
                    $url=$value;
                    $service=$key;
                    if($value!=""&&!is_null($value)){
                        if(!$stmt4->execute()){
                            error_log($this->mysqli->error);
                        }
                    }
                }
            }
            else{
                error_log($this->mysqli->error);
            }

            if(strtolower(substr($artist,-1))!='s'){
                $s = "s";
            }
            else{
                $s="";
            }
            if($print==1){
                $_SESSION['PRINTID'][]=$id_last;
            }
        }
        return $id_last;
    }
}
