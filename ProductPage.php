<?php

namespace app;

use GuzzleHttp\Client;

class ProductPage
{
    /**
     * @var string прнимает название txt файла с ссылками на товары
     */
    public $linksPath;
    /**
     * @var string принимает proxy ip:port
     */
    public $proxyIp;
    /**
     * @var array возвращает массив request_uri страниц товаров
     */
    public $productLinksArr;

    /**
     * Метод возвращает массив ссылок на товары
     * @param $linksPath
     * @return array|bool
     */
    public function getProductLink($linksPath)
    {
        $this->linksPath = $linksPath;
        $categoriesUrls = file('links/'.$this->linksPath);
        return $categoriesUrls;
    }


    /**
     * Метод возвращает массив, состоящий из guzzle request указанных ссылок
     * @param $linksPath
     * @param $proxyIp
     * @return array
     */
    public function getProductPage($linksPath, $proxyIp):array
    {
        $this->linksPath = $linksPath;
        $this->proxyIp = $proxyIp;

        $userAgents = [
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
            'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9.2.3) Gecko/20100401 Lightningquail/3.6.3',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
            'Mozilla/4.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)',
        ];
        foreach ($this->getProductLink($this->linksPath) as $productLink) {
            $productLink = str_replace('http://example.com/', '', $productLink);
            $productLink = substr($productLink,0, -2);
            $productLinksArr[] = $productLink;
            }

            for($i = 0; $i < count($productLinksArr); $i++){
                $this->client = new Client([
                    'base_uri' => 'http://example.com',
                    'timeout' => 0,
                    'connect_timeout' => 10,
                    'exceptions' => false,
                    'proxy' => $this->proxyIp,
                    'referer' => true,
                    'headers' => [
                        'User-Agent' => $userAgents[rand(0, count($userAgents) -1)],
                        'referer' => 'https://www.google.com/',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                        'Accept-Encoding' => 'gzip, deflate',
                        'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6,cs;q=0.5,la;q=0.4',
                        'Cache-Control' => 'max-age=0',
                        'Connection' => 'keep-alive',
                        'Host' => 'www.example.com',
                        'Upgrade-Insecure-Requests' => 1,
                    ]
                ]);
                $res[] = $this->client->request('GET', $productLinksArr[$i]);
            }
            return $res;
    }
}