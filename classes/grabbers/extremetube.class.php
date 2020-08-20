<?php

class VGrab_extremetube
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_extremetube() {
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
		$id_arr  = explode('-',$this->url);
		$id_str  = end($id_arr);
		$vid = intval($id_str);
		return $vid;
	}	
    
    function getVideoTitle() {
        preg_match('/class="title-video-box float-left" title="(.*?)"/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			return htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES);
		}
    }
	
	function getVideoDescription() {
		return '';
	}
	
	function getVideoTags() {
        preg_match('/Tags:<\/div>(.*?)<\/div>/', $this->page, $matches);
        if ( isset($matches['1']) ) {
            $tag_string = $matches['1'];
            preg_match_all('/<a href="(.*?)">(.*?)<\/a>/', $tag_string, $matches_tag);
            if ( isset($matches_tag['2']) ) {
                foreach ( $matches_tag['2'] as $tag ) {					
                    $tags[] = strtolower($tag);
                }
                return implode(', ', $tags);
            }
        }
	}

    function getVideoCategory() {
        preg_match('/Categories:<\/div>(.*?)<\/div>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {			
            $category_string = $matches['1'];
            preg_match_all('/<a href="(.*?)">(.*?)<\/a>/', $category_string, $matches_category);
            if ( isset($matches_category['2']) ) {
                return implode(' ', $matches_category['2']);
            }
        }
    }
 
    function getVideoUrl() {
		preg_match('/flashvars = {(.*?)};/', $this->page, $matches);
		if ( isset($matches['1']) ) {
			preg_match_all('/"quality_(.*?)":"(.*?)"/', $matches['1'], $matches_url);
			foreach ($matches_url[2] as $k => $v) {
				$videos[$matches_url[1][$k]] = stripslashes($v);
			}			
			return $videos;
		}
    }
}

?>