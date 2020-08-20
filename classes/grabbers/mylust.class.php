<?php

class VGrab_mylust
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_mylust() {
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
        preg_match('/videos\/(.*?)\//', $this->url, $matches);
		if ( isset($matches[1]) ) {
			$vid = $matches[1];
			return $vid;
		}
	}
    
    function getVideoTitle() {
        preg_match('/title"><h1 itemprop="name">(.*?)</', $this->page, $matches);
        if ( isset($matches['1']) ) {
			$title = str_replace(" - MyLust.com", "", $matches['1']);
            return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
        }
    }
	
	function getVideoDescription() {
        preg_match('/itemprop="description">(.*?)</', $this->page, $matches);
        if ( isset($matches['1']) ) {
			return htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES);
        }
	}
	
	function getVideoTags() {
		return '';
	}

    function getVideoCategory() {
        preg_match('/<div class="head">Categories<\/div> <div class="body">(.*?)<\/div>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {			
            $category_string = $matches['1'];
            preg_match_all('/">(.*?)<\/a>/', $category_string, $matches_category);
            if ( isset($matches_category['1']) ) {
                return implode(' ', $matches_category['1']);
            }
        }
    }
 
    function getVideoUrl() {
		preg_match('/<source(.*?)src="(.*?)"/', $this->page, $matches);
		if ( isset($matches['1']) ) {
			 $videos['Default'] = $matches['2'];
			return $videos;
		} else
			return false;
    }
}

?>