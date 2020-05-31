<?php

namespace App\Http\Controllers;

use App\Result;
use Illuminate\Http\Request;

class SeedController extends Controller
{
    const ARTICLES_NUMBER = 150;

    public function actionAsync(){
        $site = $_POST['site'];
        if($site == 'agerpress') {
            $this->parseAgerpres();
        }
        if($site == 'adevarul') {
            $this->parseAdevarul();
        }

        if($site == 'aktual') {
            $this->parseAktual();
        }

        if($site == 'economica') {
            $this->parseEconomica();
        }

        if($site == 'hotnews') {
            $this->parseHotnews();
        }

        if($site == 'news') {
            $this->parseNews();
        }

        if($site == 'antena3') {
            $this->parseAntena3();
        }
    }

    public function actionAsyncArticle(){
        $site = $_POST['site'];
        if($site == 'agerpress') {
            $this->actionAgerpress();
        }
        if($site == 'adevarul') {
            $this->actionAdevarul();
        }

        if($site == 'antena3') {
            $this->actionAntena3();
        }

        if($site == 'aktual') {
            $this->actionAktual();
        }

        if($site == 'economica') {
            $this->actionEconomica();
        }

        if($site == 'hotnews') {
            $this->actionHotnews();
        }

        if($site == 'news') {
            $this->actionNews();
        }
    }

    public static function parseDoom($url)
    {
        $ch = curl_init();
        $timeout = 100000;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        return $dom;

    }

    public static function checkUrl ($url)
    {

        $models = Result::where('link', $url)->get();

        if (count($models) > 0) {
            return false;
        }

        return true;
    }

    public static function insert($model) {
        $result = new Result();

        if(!isset($model['description'])) {
            $model['description'] = $model['title'];
        }

        $result->title = $model['title'];
        $result->description = $model['description'];
        $result->link = $model['link'];

        $result->save();
    }

    /**
     * adevarul.ro
     */
    public function parseAdevarul()
    {

        $links = [];
        $doom = self::parseDoom('https://adevarul.ro/news/');
        $count = 1;
        foreach ($doom->getElementsByTagName('article') as $article) {
            if($article->getAttribute('class') == 'deschidere-main deschidere-light wrap')
                foreach ($article->getElementsByTagName('a') as $link) {
                    if(self::checkUrl('https://adevarul.ro' . $link->getAttribute('href'))) {
                        array_push($links,'https://adevarul.ro' . $link->getAttribute('href'));
                    }
                    break 1;
                }
            else {
                foreach ($article->getElementsByTagName('a') as $key => $link) {
                    if( $key == 1 ) {
                        $count++;
                        if(self::checkUrl('https://adevarul.ro' . $link->getAttribute('href')) && $count <= self::ARTICLES_NUMBER) {
                            array_push($links,'https://adevarul.ro' . $link->getAttribute('href'));
                            break 1;
                        } else {
                            break 2;
                        }
                    }

                }
            }

        }

        echo json_encode($links);

    }

    public function actionAdevarul()
    {
        $url = $_POST['url'];
        $article = self::parseDoom($url);

        foreach ($article->getElementsByTagName('img') as $img) {
            if ($img->getAttribute('id') == 'relatedContent[mainImage][url]') {
                $image = 'https://adevarul.ro' . $img->getAttribute('src');
            }
        }

        $model['title'] = $article->getElementsByTagName('h1')->item(0)->nodeValue;

        foreach ($article->getElementsByTagName('h2') as $p) {
            if ($p->getAttribute('class') == 'articleOpening') {
                $model['description'] = $p->nodeValue;
                break;
            }
        }

        $model['link'] = $url;

        self::insert($model);
    }

    /**
     * Aktual24.ro
     */

    public function parseAktual()
    {
        $links = [];
        $doom = self::parseDoom('https://www.aktual24.ro/');
        $count = 1;

        foreach ($doom->getElementsByTagName('h1') as $article) {
            if ($article->getAttribute('id') == 'hero-post') {
                foreach ($article->getElementsByTagName('a') as $a) {
                    if (self::checkUrl($a->getAttribute('href'))) {
                        array_push($links,$a->getAttribute('href'));
                        break;
                    }
                }
                break;
            }
        }

        foreach ($doom->getElementsByTagName('article') as $article) {
            foreach ($article->getElementsByTagName('a') as $a) {
                if (self::checkUrl($a->getAttribute('href')) && $count < self::ARTICLES_NUMBER) {
                    array_push($links,$a->getAttribute('href'));
                    $count++;
                    break 1;
                } else {
                    break 2;
                }
            }
        }

        echo json_encode($links);

    }

    public function actionAktual()
    {
        $url = $_POST['url'];
        $article = self::parseDoom($url);

        $model['title'] = $article->getElementsByTagName('h1')->item(0)->nodeValue;

        foreach ($article->getElementsByTagName('p') as $key => $p) {
            if($key == 2) {
                foreach ($p->getElementsByTagName('strong') as $strong) {
                    $model['description'] = $strong->nodeValue;
                }
            }
        }

        $model['link'] = $url;

        self::insert($model);
    }

    /**
     * economica.net
     */

    public function parseEconomica()
    {
        $links = [];
        $doom = self::parseDoom('https://www.economica.net/cele-mai-noi-stiri.html');
        $count = 0;

        $divs = $doom->getElementsByTagName('div');

        foreach ($divs as $div) {
            if ($div->getAttribute('class') == 'col-md-8 vertical-separator') {
                foreach ($div->getElementsByTagName('div') as $article) {
                    if ($article->getAttribute('class') == 'row article-box ') {
                        $count++;
                        foreach ($article->getElementsByTagName('a') as $a) {
                            if(self::checkUrl($a->getAttribute('href')) && $count <= self::ARTICLES_NUMBER) {
                                array_push($links, $a->getAttribute('href'));
                                break 1;
                            } else {
                                break 3;
                            }
                        }
                    }
                }
            }
        }

        echo json_encode($links);
    }

    public function actionEconomica() {
        $url = $_POST['url'];
        $article = self::parseDoom($url);

        $model = [];

        foreach ($article->getElementsByTagName('h1') as $h1) {
            if($h1->getAttribute('class') == 'mb-5 font-weight-bold') {
                $model['title'] = $h1->nodeValue;
            }
        }

        foreach ($article->getElementsByTagName('div') as $div) {
            if($div->getAttribute('class') == 'articleDescription mb-3') {
                $model['description'] = $div->nodeValue;
            }
        }

        $model['link'] = $url;

        self::insert($model);
    }

    /**
     * hotnews.ro
     */

    public function parseHotnews()
    {
        $links = [];
        $count = 0;
        $doom = self::parseDoom('https://www.hotnews.ro/Ultima_ora');
        foreach ($doom->getElementsByTagName('div') as $div) {
            if ($div->getAttribute('style') == 'margin-top:4px;') {
                $box_articles = $div;
                break;
            }
        }

        foreach ($box_articles->getElementsByTagName('a') as $a) {
            if ( self::checkUrl($a->getAttribute('href')) && $count < self::ARTICLES_NUMBER) {
                array_push($links,$a->getAttribute('href'));
                $count ++;
            } else {
                break;
            }
        }

        echo json_encode($links);
    }

    public function actionHotnews()
    {
        $url = $_POST['url'];
        $article = self::parseDoom($url);
        $model['title'] = $article->getElementsByTagName('h1')->item(0)->nodeValue;

        $articleContent = $article->getElementById('articleContent');

        if(isset($articleContent->getElementsByTagName('strong')->item(0)->nodeValue))
            $model['description'] = $articleContent->getElementsByTagName('strong')->item(0)->nodeValue;
        else $model['description'] = '';

        $model['link'] = $url;

        self::insert($model);
    }

    /**
     * news.ro
     */

    public function parseNews()
    {
        $count = 0;
        $doom = self::parseDoom('https://www.news.ro/toate');
        $links = [];

        foreach ($doom->getElementsByTagName('article') as $article) {
            $link = 'https://www.news.ro' . $article->getElementsByTagName('a')->item(0)->getAttribute('href');
            if(self::checkUrl($link) && $count < self::ARTICLES_NUMBER) {
                array_push($links,$link);
                $count++;
            } else {
                break;
            }
        }

        echo json_encode($links);
    }

    public function actionNews() {
        $url = $_POST['url'];
        $article = self::parseDoom($url);

        $model['title'] = $article->getElementsByTagName('h1')->item(0)->nodeValue;

        foreach ($article->getElementsByTagName('div') as $div) {
            if($div->getAttribute('class') == 'article-content') {
                $articleContent = $div;
                break;
            }
        }

        $model['description'] = $articleContent->getElementsByTagName('strong')->item(0)->nodeValue;

        $model['link'] = $url;

        self::insert($model);
    }

    public function parseAntena3()
    {
        $links = [];
        $doom = self::parseDoom('https://www.antena3.ro/actualitate/');
        $count = 1;

        foreach ($doom->getElementsByTagName('article') as $art) {
            foreach ($art->getElementsByTagName('a') as $link) {
                if($link->getAttribute('class') == 'thumb') {
                    if(self::checkUrl($link->getAttribute('href')) && $count <= self::ARTICLES_NUMBER) {
                        $href = 'https://www.antena3.ro' . $link->getAttribute('href');
                        array_push($links, $href);
                        $count++;
                        break 1;
                    }
                } else {
                    break 2;
                }
            }
        }

        echo json_encode($links);
    }

    public function actionAntena3()
    {
        $url = $_POST['url'];
        $article = self::parseDoom($url);

        $model['title'] = $article->getElementsByTagName('h1')->item(0)->nodeValue;

        foreach ($article->getElementsByTagName('div') as $div) {
            if ($div->getAttribute('class') == 'elements articol') {
                foreach ($div->getElementsByTagName('p') as $p) {
                    if ($p->getAttribute('class') == 'sapou') {
                        $model['description'] = $p->nodeValue;
                    }
                }
            }
        }

        $model['link'] = $url;

        self::insert($model);
    }

    /**
     * agerpres.ro
     */

    public function parseAgerpres()
    {
        $links = [];
        $doom = self::parseDoom('https://www.agerpres.ro/');
        $count = 1;

        foreach ($doom->getElementsByTagName('div') as $div) {
            if ($div->getAttribute('class') == 'last_news shadow update_last_news') {
                foreach ($div->getElementsByTagName('article') as $article) {
                    $rawLinks = $article->getAttribute('onclick');
                    $link = explode("'",$rawLinks)[count(explode('/',$rawLinks)) - 7];
                    $link = 'https://' . substr($link, 2);

                    if (self::checkUrl($link) && $count < self::ARTICLES_NUMBER+30) {
                        array_push($links,$link);
                        $count++;
                    }
                }
            }
        }

        echo json_encode($links);
    }

    public function actionAgerpress()
    {
        $url = $_POST['url'];
        $article = self::parseDoom($url);

        $model['title'] = $article->getElementsByTagName('h2')->item(6)->nodeValue;

        $model['description'] = '';

        foreach ($article->getElementsByTagName('div') as $div) {
            if ($div->getAttribute('class') == 'wrapper_description_articol') {
                foreach ($div->getElementsByTagName('strong') as $strong) {
                    $model['description'] = '<p>' . self::innerHTML($strong) . '</p>';
                    break 2;
                }
            }
        }
        if ($model['description'] == null) {
            $model['description'] = $model['title'];
        }

        $model['link'] = $url;

        self::insert($model);
    }

    public function parseMoreInfo(Request $request)
    {
        $article = self::parseDoom('https://www.antena3.ro/actualitate/pagina-'.$request['page']);

        foreach ($article->getElementsByTagName('ul') as $ul) {
            if ($ul->getAttribute('class') == 'cols3') {
                $articles = $ul;
                break;
            }
        }
        $model = [];
        foreach ($articles->getElementsByTagName('li') as $li) {
            if($li->getAttribute('class') != 'mobile_only') {
                $model['title'] = $li->getElementsByTagName('a')->item(1)->nodeValue;
                $model['description'] = $li->getElementsByTagName('a')->item(1)->nodeValue;
                $model['link'] = 'https://www.antena3.ro'.$li->getElementsByTagName('a')->item(1)->getAttribute('href');

                if (self::checkUrl($model['link'])) self::insert($model);
            }


        }

    }
}
