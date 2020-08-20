<?php

class VGrab_analdin
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_yobt() {
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
        preg_match('/<h2>(.*?)<\//', $this->page, $matches);
        if ( isset($matches['1']) ) {
            return html_entity_decode(htmlspecialchars_decode(strip_tags(stripslashes($matches['1']))));
        }
    }
	
	function getVideoDescription() {
        preg_match('/description" content="(.*?)"/', $this->page, $matches);
        if ( isset($matches['1']) ) {
            return html_entity_decode(htmlspecialchars_decode(strip_tags(stripslashes($matches['1']))));
        }
	}
	
	function getVideoTags() {
		preg_match_all('/\/tags\/(.*?)">(.*?)<\//', $this->page, $matches);
		if ( isset($matches['1']) ) {
			foreach($matches[2] as $v) {
				if (strlen($v)>1) {
					$tags[] = trim(strtolower($v));
				}
			}
			return implode(', ', $tags);
		}
	}

    function getVideoCategory() {
		if (preg_match('/Categories:<\/span>(.*?)<\/div>/', $this->page, $matches_cat)) {
			preg_match_all('/\/">(.*?)<\//', $matches_cat[1], $matches);
			if ( isset($matches['1']) ) {
				foreach($matches[1] as $v) {
					$categories[] = trim(strtolower($v));
				}
				return implode(' ', $categories);
			}
		}
    }
 
    function getVideoUrl() {
		if (preg_match('/var flashvars = \{(.*?)\}\;/', $this->page, $matches)) {
			if ( isset($matches['1']) ) {
				if (preg_match_all("/_url(.*?)'https:(.*?)'(.*?)_text: '(.*?)'/", $matches['1'], $matches_v)) {
					foreach ($matches_v[2] as $k => $v) {
						$videos[$matches_v[4][$k]] = "https:".$v;
					}				
					return $videos;
				}
			}
		}
		return false;
    }
}

?>