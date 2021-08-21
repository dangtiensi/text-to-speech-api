<?php
/*
 * @github: https://github.com/dangtiensi
 */

if (!empty($_GET['text']))
{
    $type = $_GET['type'] ?? 'b';
    $g = new Speech();
    $data = $g->get($_GET['text'], $type);
    if (!empty($data['error']))
    {
        $data = $g->get('Tin nhắn lỗi. không đọc được.', $type);
    }
    header('Content-Type: audio/mpeg');
    header('Cache-Control: no-cache');
    header("Content-Transfer-Encoding: chunked"); 
    echo base64_decode($data['audioContent']);
}

// class
class Speech
{
    private $api_key = ''; // truy cập vào https://console.cloud.google.com/apis/credentials tạo key của mục "API Keys" để dán vào đây

    public function get(string $text, $opt = 'b')
    {
        $request = [
            'input'         => [
                'text'          => $text
            ],
            'voice'         => [
                'languageCode'  => 'vi-VN',
                'name'          => 'vi-VN-Wavenet-' . strtoupper($opt)
            ],
            'audioConfig'   => [
                'audioEncoding' => 'MP3',
                'pitch'         => 0.00,
                'speakingRate'  => 1.00
            ]
        ];
        return json_decode($this->getAPI($request), true);
    }

    private function getAPI(array $request = [])
    {
        return $this->cURL('https://content-texttospeech.googleapis.com/v1beta1/text:synthesize?alt=json&key=' . $this->api_key, $request);
    }

    private function cURL(string $url, array $post = [], string $cookie = '')
    {
        $ch = curl_init();
        $head[] = "Connection: keep-alive";
        $head[] = "Keep-Alive: 300";
        $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $head[] = "Accept-Language: en-us,en;q=0.5";
        $head[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14');
        curl_setopt($ch, CURLOPT_URL, $url);
        if (count($post) > 0)
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
};
?>
