<?php
header("Access-Control-Allow-Origin: *");
$q = $_GET['q'] ?? 'hd';
$q = ($q === 'sd') ? 'sd' : 'hd';
$stream = getStream($q);
if ($stream) {
    header("Location: " . $stream);
    exit;
}

echo "error";
function getStream($quality = 'hd') {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://www.sjtv.com.tw/live/sjtvlive",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) ".
                             "AppleWebKit/537.36 (KHTML, like Gecko) ".
                             "Chrome/147.0.0.0 Safari/537.36",
        CURLOPT_HTTPHEADER => [
            "Accept: text/html,application/xhtml+xml",
            "Accept-Language: zh-TW,zh;q=0.9,en;q=0.8",
            "Connection: keep-alive",
            "Upgrade-Insecure-Requests: 1"
        ]
    ]);
    $html = curl_exec($ch);
    curl_close($ch);
    if (!$html) return null;
    if ($quality === 'sd') {
        $pattern = '/livestream_720p\/playlist\.m3u8\?[^"]+/';
    } else {
        $pattern = '/livestream\/playlist\.m3u8\?[^"]+/';
    }
    preg_match($pattern, $html, $match);
    if (empty($match) && $quality === 'hd') {
        preg_match('/livestream_720p\/playlist\.m3u8\?[^"]+/', $html, $match);
    }
    if (!empty($match[0])) {
        return "https://live.sjtech.com.tw:8443/sjtv/" . $match[0];
    }
    return null;
}
?>
