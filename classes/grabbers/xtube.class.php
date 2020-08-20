<?php

class VGrab_xtube
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_xtube() {
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
        preg_match('/name="video_id" value="(.*?)"/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			$vid = $matches['1'];
			return $vid;
		}
    }
	
    function getVideoTitle() {
        preg_match('/"title":"(.*?)"/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			$title = current(explode(' | ', $matches['1']));
			return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
		}
    }
	
	function getVideoDescription() {
		return false;
	}
	
	function getVideoTags() {
		return false;
	}

    function getVideoCategory() {
        preg_match('/Categories:<\/dt>(.*?)<\/div>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {			
            $category_string = $matches['1'];
            preg_match_all('/">(.*?)<\/a>/', $category_string, $matches_category);
            if ( isset($matches_category['1']) ) {
                return implode(', ', $matches_category['1']);
            }
        }
    }
 
    function getVideoUrl() {
		preg_match('/sources":{(.*?)}/', $this->page, $matches);
		if ( isset($matches['1']) ) {
			preg_match_all('/"(.*?)":"(.*?)"/', $matches['1'], $matches_url);
			foreach ($matches_url[2] as $k => $v) {
				$videos[$matches_url[1][$k]] = str_replace("25&hash","2525&hash",stripslashes($v));
			}
			ksort($videos);			
			return $videos;
		}
    }
}

?>