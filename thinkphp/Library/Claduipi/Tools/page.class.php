<?php

namespace Claduipi\Tools;

class page {

    public $zongji;
    public $num;
    public $url;
    public $limit;
    public $fenye;
    public $fenyetotal;
    public $fenyeurl;
    public $total;

    public function config($zongji, $num, $fenye, $fenyeurl = '') {
        $this->total = $zongji;
        $this->num = $num;
        $this->pageurl = $fenyeurl;
        $this->pagetotal = ceil($this->total / $this->num);
        $this->page = $this->get_url_page($fenye);
        $this->url = $this->geturl();
        $this->limit = $this->setlimit();
        
    }

    public function show($style = '', $ret = '') {
        $style = strtolower(trim($style));
        
        switch ($style) {
            case "one" :    //普通的样式			
                return $this->ordinary($ret);
                break;
            case "two" :
                return $this->pagelist($ret);
                break;
            default:
                return $this->pagelist($ret);
                break;
        }
    }

    private function ordinary($ret) {
        
        
        $Prev = $this->page - 1;
        $next = $this->page + 1;
        $html_l = "<ul id='Page_Ul'>";
        $html = '';
        if ($Prev != 0) {
            $html.="<li id='Page_Prev'><a href=\"{$this->url[0]}{$Prev}{$this->url[1]}\">上一页</a></li>";
        } else {
            $html.="<li id='Page_Prev'><a href=\"javascript:void(0);\">上一页</a></li>";
        }
        if ($next <= $this->pagetotal) {
            $html.="<li id='Page_Next'><a href=\"{$this->url[0]}{$next}{$this->url[1]}\">下一页</a></li>";
        } else {
            $html.="<li id='Page_Next'><a href=\"javascript:void(0);\">下一页</a></li>";
        }
        $html.="<li id='Page_One'><a href=\"{$this->url[0]}1{$this->url[1]}\">首页</a></li>";
        $html.="<li id='Page_End'><a href=\"{$this->url[0]}{$this->pagetotal}{$this->url[1]}\">尾页</a></li>";
        $html_r = "</ul>";
             
        if ($this->total == 0) {
            return;
        } else {
            if ($ret == 'li') {
                return $html;
            } else {
                return $html_l . $html . $html_r;
            }
        }
    }

    private function pagelist($ret) {
        
        $listnum = floor(7 / 2);
        $html_l = "<ul id='Page_Ul'>";
        $html = '';
        $html.="<li id='Page_Total'>{$this->total}条";
        $html.="<li id='Page_One'><a href=\"{$this->url[0]}1{$this->url[1]}\">首页</a></li>";
        if ($this->page == 1) {
            $html.="<li id='Page_Prev'><a href=\"{$this->url[0]}" . ($this->page) . $this->url[1] . "\">上一页</a></li>";
        } else {
            $html.="<li id='Page_Prev'><a href=\"{$this->url[0]}" . ($this->page - 1) . $this->url[1] . "\">上一页</a></li>";
        }
        for ($i = $listnum; $i >= 1; $i--) {
            $fenye = $this->page - $i;

            if ($fenye < 1) {
                continue;
            } else {
                $html.="<li class='Page_Num'><a href=\"{$this->url[0]}{$fenye}{$this->url[1]}\">{$fenye}</a></li>";
            }
        }

        $html.="<li class='Page_This'>{$this->page}</li>";

        for ($i = 1; $i <= $listnum; $i++) {

            $fenye = $this->page + $i;
            if ($fenye <= $this->pagetotal) {
                $html.="<li class='Page_Num'><a href=\"{$this->url[0]}{$fenye}{$this->url[1]}\">{$fenye}</a></li>";
            } else {
                continue;
            }
        }
        if ($this->page == $this->pagetotal) {
            $html.="<li id='Page_Next'><a href=\"{$this->url[0]}" . ($this->page) . $this->url[1] . "\">下一页</a></li>";
        } else {
            $html.="<li id='Page_Next'><a href=\"{$this->url[0]}" . ($this->page + 1) . $this->url[1] . "\">下一页</a></li>";
        }
        $html.="<li id='Page_End'><a href=\"{$this->url[0]}{$this->pagetotal}{$this->url[1]}\">尾页</a></li>";
        $html_r = "</ul>";

        if ($this->total == 0) {
            return;
        } else {
            if ($ret == 'li') {
                return $html;
            } else {
                return $html_l . $html . $html_r;
            }
        }
    }

    private function geturl() {
        $url = array(0 => '', 1 => '');
        //$urls = get_LOCAL_url();
        global $_yys;
        $urls = $_SERVER["REQUEST_SCHEME"].'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        $urls = trim($urls, '/');
        $parse = parse_url($urls);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $parses);
            unset($parses['p']);
            if (empty($parses)) {
                $urls = $parse['path'] . "?";
            } else {
                $urls = $parse['path'] . "?" . http_build_query($parses) . '&';
                $urls = str_ireplace("%2f", '/', $urls);
                $urls = str_ireplace("=&", '/&', $urls);
            }
        } else {
            $urls = $parse['path'] . "?";
        }
        $urls = preg_replace("#\/\/#", "/", $urls);
        $url[0] = $urls . 'p=';
        return $url;
    }

    private function get_url_page($fenye = 1) {

        $fenye = abs(intval($fenye));
        if (!$fenye)
            $fenye = 1;
        if ($fenye > $this->pagetotal) {
            $fenye = $this->pagetotal;
        }
        return $fenye;
    }

    private function get_this_url() {
        global $_yys;
        $urls = C("URL_DOMAIN") . '/' . $_yys['param_arr']['url'];
        $urls = explode('/', $urls);
        array_pop($urls);
        $urls = implode('/', $urls);
        return array($urls . '/p', "q");
    }

    private function setlimit() {
        return "LIMIT " . ($this->page - 1) * $this->num . "," . $this->num;
    }

    public function __get($value) {
        return $this->$value;
    }

}
