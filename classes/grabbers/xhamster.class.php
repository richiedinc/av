<?php

class VGrab_xhamster
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_xhamster() {
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
		return false;
	}	
	
    function getVideoTitle() {
        preg_match('/"title":"(.*?)"/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			$title = current(explode(':', $matches['1']));
			return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
		}
    }
	
	function getVideoDescription() {
        preg_match('/ab-info controls-info__item xh-helper-hidden"> <p>(.*?)</', $this->page, $matches);
        if ( isset($matches['1']) ) {
			return htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES);
        }
	}
	
	function getVideoTags() {
		return '';
	}

    function getVideoCategory() {
	    if(preg_match('/data-ts-categories="(.*?)"/', $this->page, $matches_category)) {
            if ( isset($matches_category['1']) ) {
                return $matches_category['1'];
            }
		}
    }
 
    function getVideoUrl() {
		preg_match('/"sources":{"mp4":{(.*?)}/', $this->page, $matches);
		if ( isset($matches['1']) ) {
			preg_match_all('/"(.*?)":"(.*?)"/', $matches['1'], $matches_url);
			foreach ($matches_url[2] as $k => $v) {
				$videos[$matches_url[1][$k]] = stripslashes($v);
			}
			ksort($videos);			
			return $videos;
		}
    }
}

?>