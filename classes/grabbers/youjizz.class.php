<?php

class VGrab_youjizz
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_youporn() {
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
		return str_replace('.html', '', end(explode('-', $this->url)));
	}		
	
    function getVideoTitle() {
        preg_match('/<title>(.*?)<\/title>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			$title = trim($matches['1']);
			return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
		}
    }
	
	function getVideoDescription() {
        preg_match('/"description" content="(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {
			return trim(htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES));
        }
	}
	
	function getVideoTags() {
		preg_match_all('/<li><a href="\/tags\/(.*?)">(.*?)</', $this->page, $matches_tag);
		if ( isset($matches_tag['2']) ) {
			$tag_links  = $matches_tag['2'];
			foreach ( $tag_links as $tag ) {					
				$tags[] = strtolower(trim($tag));
			}
			$tags = array_unique($tags);
			return implode(', ', $tags);
		}
	}

    function getVideoCategory() {
		preg_match_all('/<li><a href="\/tags\/(.*?)">(.*?)</', $this->page, $matches_tag);
		if ( isset($matches_tag['2']) ) {
			$tag_links  = $matches_tag['2'];
			foreach ( $tag_links as $tag ) {					
				$tags[] = strtolower(trim($tag));
			}
			$tags = array_unique($tags);
			return implode(', ', $tags);
		}
    }
 
    function getVideoUrl() {
        if (preg_match_all('/"quality":"(.*?)","filename":"(.*?)","(.*?)"name":"(.*?)"/', $this->page, $matches_url)) {		
			foreach ($matches_url[2] as $k => $v) {
				if (strpos($v, 'm3u8') === false) {
					$videos[$matches_url[4][$k]] = "https:".stripslashes($v);
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