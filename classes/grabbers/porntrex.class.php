<?php

class VGrab_porntrex
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_porntrex() {
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
        preg_match('/watch\/(.*?)\//', $this->url, $matches);
		if ( isset($matches[1]) ) {
			$vid = $matches[1];
			return $vid;
		}
	}		
	
    function getVideoTitle() {
        preg_match('/<title>(.*?)<\/title>/', $this->page, $matches);		
        if ( isset($matches['1']) ) {
			$title = trim($matches['1']);
			return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
		}
    }
	
	function getVideoDescription() {
		return '';
	}
	
	function getVideoTags() {
        preg_match('/name="keywords" content="(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {
			$matches['1'] = str_replace('-, ', '', strtolower(trim(htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES))));
			$matches['1'] = str_replace(', ', ',', $matches['1']);
			return str_replace(',', ', ', $matches['1']);
        }
	}

    function getVideoCategory() {
        preg_match('/name="keywords" content="(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {
			return str_replace('-, ', '', strtolower(trim(htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES))));
        }
    }
 
    function getVideoUrl() {
		if (preg_match('/flashvars = {(.*?)};/', $this->page, $matches_vs)) {
			if (preg_match_all('/_url(.*?)\'https:\/\/(.*?)\'(.*?)text: \'(.*?)\'/', $matches_vs['1'], $matches_url)) {
				foreach ($matches_url[2] as $k => $v) {
					if (strpos($v, 'get_file') !== false) {
						$videos[$matches_url[4][$k]] = "https://".stripslashes($v);
					}
				}			
				return $videos;
			} else {
				return false;
			}
		}
	}
}

?>