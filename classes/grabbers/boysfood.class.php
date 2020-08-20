<?php

class VGrab_boysfood
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_boysfood() {
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
			$title = str_replace(" - Free Porn Videos", "", $matches['1']);
			return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
        }
    }
	
	function getVideoDescription() {
        preg_match('/<div class="vp2" title="(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {
			return htmlspecialchars_decode(strip_tags(stripslashes($matches['1'])), ENT_QUOTES);
        }
	}
	
	function getVideoTags() {
		return '';
	}

    function getVideoCategory() {
		return '';
    }
 
    function getVideoUrl() {
        preg_match('/--> <source src="(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {
			$videos['Default'] = $matches['1'];
			return $videos;
        }
    }
}

?>