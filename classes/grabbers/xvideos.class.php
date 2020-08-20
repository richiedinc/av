<?php

class VGrab_xvideos
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_xvideos() {
        $this->__construct();
    }

    function getPage($url) {
        $this->url  = $url;
        if ( $this->page = $this->curl->saveToString($url) ) {
            $this->page = trim($this->page);
            $this->page = str_replace("\n", '', $this->page);
            $this->page = str_replace("\r", '', $this->page);
            $this->page = preg_replace('/\s\s+/', ' ', $this->page);
            return true;
        }
        return false;
    }
    
	function getVideoID() {
        preg_match('/video(.*?)\//', $this->url, $matches);
		if ( isset($matches[1]) ) {
			$vid = $matches[1];
			return $vid;
		}
	}	
	
    function getVideoTitle() {
        preg_match('/og:title" content="(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {
            return htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES);
        }
    }
	
	function getVideoDescription() {
		return '';
	}
	
	function getVideoTags() {
		preg_match_all('/<a href="\/tags\/(.*?)"/', $this->page, $matches);
		if ( isset($matches['1']) ) {
			return implode(', ', $matches[1]);
		}
	}

    function getVideoCategory() {
        preg_match('/window.wpn_categories = "(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {			
            return str_replace(',',' ', $matches['1']);
        } else return '';
    }

 
    function getVideoUrl() {
        preg_match("/setVideoUrlLow\('(.*?)'\)/", $this->page, $matches_low);
        preg_match("/setVideoUrlHigh\('(.*?)'\)/", $this->page, $matches_high);
        if ( isset($matches_low['1']) ) {			
			$videos['Low quality'] = $matches_low['1'];
        } 
        if ( isset($matches_high['1']) ) {
			$videos['High quality'] = $matches_high['1'];
        } 		
		if (!empty($videos)) {
			return $videos;
		} else {
			return false;
		}
    }
}

?>