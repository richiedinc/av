<?php

class VGrab_gaytube
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_gaytube() {
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
        preg_match('/<title>(.*?)<\/title>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			$title = trim(str_replace(' Video - Gaytube.com', '', $matches['1']));			
			$title = trim(str_replace(' - Gaytube.com', '', $title));
			return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
		}
    }
	
	function getVideoDescription() {
        preg_match('/<div class="more-info-item description font-13">(.*?)<\/div>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			return htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES);
		}
	}
	
	function getVideoTags() {
        preg_match('/Tags: <\/span>(.*?)<\/div>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
            $tag_string = $matches['1'];
            preg_match_all('/">(.*?)<\/a>/', $tag_string, $matches_tag);
            if ( isset($matches_tag['1']) ) {
                $tag_links  = $matches_tag['1'];
                foreach ( $tag_links as $tag ) {					
                    $tags[] = $tag;
                }
                return implode(', ', $tags);
            }
        }
	}

    function getVideoCategory() {
		preg_match_all('/<div class="font-bold category-btn">(.*?)<\/div>/', $this->page, $matches_category);
		if ( isset($matches_category['1']) ) {
			$category_links  = $matches_category['1'];
			return implode(' ', $category_links);
		}
    }
 
    function getVideoUrl() {
		preg_match_all('/flashvars.quality_(.*?)="(.*?)"/', $this->page, $matches);
		if ( isset($matches['2']) && $matches['2'] != '') {
			foreach ($matches[2] as $k => $v) {
				if (trim($v) != '') {
					$videos[$matches[1][$k]] = $v;
				}
			}
			return $videos;
		} else
			return false;
    }
}

?>