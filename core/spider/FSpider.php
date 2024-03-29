<?php
/**
 * Created by PhpStorm.
 * User: F
 * Date: 2019/7/16
 * Time: 19:33
 */

namespace core\spider;

use core\model\SpiderModel;

class FSpider
{
    //抓取地址
    public $url;

    //抓取深度
    public $deep = 3;

    //抓取的资源
    public $resource;

    //页面中的URL
    public $outUrl;

    public $model;


    public function __construct($argv)
    {
        //$this->model = new  SpiderModel;

        $this->url = $argv[1];

        $this->deep = (int)$argv[2] ?: $this->deep;

        $this->checkUrl();

        $this->grab();

    }

    /**
     * 检查输入网址
     * @return string
     */
    public function checkUrl()
    {
        $pattern = '/[a-z0-9]{1,}\.[a-z0-9]+\.[a-z]{2,6}|\/[\w\d\.\/\?\#\_\=]+/';

        if (!preg_match($pattern, $this->url)) {

            exit("请输入正确网址");

        }

        return $this->url;
    }


    public function grab()
    {
        //1.抓取首页
        $backHtml = $this->request($this->url);

        //2.获取首页URL
        $arr_url = $this->getUrl($backHtml);

        //2获取每一个url内容
        $this->getContent($arr_url);

    }

    /**
     * 发起请求
     * @param $requestUrl
     * @return bool|string
     */
    public function request($requestUrl)
    {
        $header = array("Host:127.0.0.1", "Accept:*/*");

        //$referer = parse_url($requestUrl)['host'];

        $ch = curl_init($requestUrl);

        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_REFERER, $requestUrl);

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36");

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        //curl_setopt($ch, CURLOPT_COOKIE, '');


        $this->resource = curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode != 200) {

            echo "请求网址不可用！\r\n";

        }

        return $this->resource;
    }

    /**
     * 取出html中的url
     * @param $backHtml
     * @return mixed
     */
    public function getUrl($backHtml)
    {
        $page_pattern = '/(http|https)\:\/\/[\d\w\.]+\/[\w\d\s\/]+\.((html|htm)(\?*)[a-z0-9\=\_\#]+)/i';

        preg_match_all($page_pattern, $backHtml, $this->outUrl);

        return $this->outUrl[0];
    }


    public function getContent($arr_url)
    {
        static $action = 0;

        $action++;

        $i = 0;

        $list_url = [];

        foreach ($arr_url as $key => $value) {

            $i++;

            echo "已抓取第{$i}个页面：" . $value . "\r\n";

            $backHtml = $this->request($value);

            //获取每一个页面的内容并入库
            $this->doInsertInfo($backHtml);

            //获取每个页面的URL
            $list_url = $this->getUrl($backHtml);


        }

        if ($action == $this->deep) {

            exit('已抓取{$this->deep}地址内所有内容，程序自动退出');

        }

        $this->getContent($list_url);

    }

    public function doInsertInfo($backHtml)
    {

        $backHtml = iconv('GBK', 'UTF-8//IGNORE', $backHtml);

        //$pattern_title = '/\<div\s+class\="post_content_main"\s+\D+\>\n\s+\<h1\>([\u4e00-\u9fa5]|\"|\"|\“|\”)+\s\<\/h1\>/i';
        $pattern_title = '/\<h1\>.*\>/';
        preg_match_all($pattern_title, $backHtml, $title);

        $pattern_content = '/\<div[\s]class\=\"post_text\"[^\>]*?>([\s\S]*?)\<\/div\>/';
        preg_match_all($pattern_content, $backHtml, $content);

        $pattern_img = '/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i';
        preg_match_all($pattern_img, @$content[0][0], $img);

        $pattern_time = '/[\d]{4}[\-][\d]{2}[\-][\d]{2}\s+[\d:]+/';
        preg_match_all($pattern_time, $backHtml, $add_time);

        $pattern_form = '/来源\:(\s+\D+)\<\/a\>/';
        preg_match_all($pattern_form, $backHtml, $from);

        $data = [
            'title' => strip_tags(@$title[0][0]) ?: '',
            'content' => strip_tags(preg_replace("/\s/", '', @$content[0][0])) ?: '',
            'url' => '',
            'img' => @$img[1][0] ?: '',
            'add_time' => @$add_time[0][0] ?: '',
            'from' => strip_tags(@$from[0][0]) ?: ''
        ];

        if (!empty($data)) {

            $this->model->insert($data);

        }

    }

    /**
     * 下载图片到指定目录
     * @param $url
     * @param string $path
     * @return string
     */
    public function downloadImage($url, $path = 'download/images')
    {
        if (!file_exists($path)) {

            mkdir($path, 0777, true);

        }

        $resource = $this->request($url);

        $filename = pathinfo($url, PATHINFO_BASENAME);

        $file_path = $path . '/' . date('YmdHis', time()) . '_' . $filename;

        $handle = fopen($file_path, 'a');

        fwrite($handle, $resource);

        fclose($handle);

        return $file_path;

    }

    public function downloadVideo($url, $path = 'download/video')
    {
        if (!file_exists($path)) {

            mkdir($path, 0777, true);

        }

        $resource = $this->request($url);

        $filename = pathinfo($url, PATHINFO_BASENAME);

        $file_path = $path . '/' . date('YmdHis', time()) . '_' . $filename;

        $handle = fopen($file_path, 'a');

        fwrite($handle, $resource);

        fclose($handle);

        return $file_path;
    }

}
