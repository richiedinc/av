<?php

class VGrab_redtube
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_redtube() {
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
		$id_arr = explode('/',$this->url);
		foreach ($id_arr as $val) {
			if(is_numeric($val)) {
				$vid = $val;
				break;
			}
		}
		return $vid;
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
		return '';
	}

    function getVideoCategory() {
		if (preg_match_all('/category-list-item-name">(.*?)</', $this->page, $matches)) {
			return implode(',', $matches[1]);
		} else {
			return false;
		}
    }
 
    function getVideoUrl() {
        if (preg_match_all('/quality":"(.*?)","videoUrl":"(.*?)"/', $this->page, $matches_url)) {
			foreach ($matches_url[2] as $k => $v) {
				$videos[$matches_url[1][$k]] = stripslashes($v);
			}			
			ksort($videos);
			return $videos;
		} else {
			return false;
		}
	}
}

?>