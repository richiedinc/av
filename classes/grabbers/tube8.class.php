<?php

class VGrab_tube8
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_tube8() {
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
		$id_arr = explode('/',rtrim($this->url,'/'));
		$vid = end($id_arr);
		$vid = intval(ltrim($vid,'video'));
		return $vid;
	}	
	
    function getVideoTitle() {
		preg_match('/title" content="(.*?)"/', $this->page, $matches);
		if ( isset($matches['1']) ) {
			return htmlspecialchars_decode(strip_tags(stripslashes(str_replace(" - Tube8","", $matches['1']))), ENT_QUOTES);
		}
    }
	
	function getVideoDescription() {
        return false;
	}
	
	function getVideoTags() {
        preg_match_all('/<li class=\'video-tag\' data-esp-node=\'tag\'>(.*?)">(.*?)</', $this->page, $matches);		
        if ( isset($matches['2']) ) {
            return implode(', ', $matches['2']);
        }
	}

    function getVideoCategory() {
        preg_match_all('/data-esp-node="category">(.*?)</', $this->page, $matches);		
        if ( isset($matches['1']) ) {
            return implode(',', $matches['1']);
        }
    }
 
    function getVideoUrl() {
        if (preg_match_all('/"quality_(.*?)":(.*?),/', $this->page, $matches_url)) {
			foreach ($matches_url[2] as $k => $v) {
				if (strpos($v, 'https') !== false) {
					$videos[$matches_url[1][$k]] = str_replace('"', '', stripslashes($v));
				}
			}			
			ksort($videos);
			return $videos;
		} else {
			return false;
		}
	}
}

?>