<?php

$id = $_GET['id'] ?? die("Please Provide ID.");
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? die("Really Kid.");

$initialUrl = 'https://apex2nova.com/premium.php?player=desktop&live=' . urlencode($id);
$initialReferer = 'https://stream.crichd.sc/';
$newReferer = 'https://apex2nova.com/';
$pattern = '/return\(\[(.*)\]/';

function getReq($url, $Referer, $userAgent) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $Referer);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$response = getReq($initialUrl, $initialReferer, $userAgent);

if (preg_match($pattern, $response, $matches)) {
    $cleanString = trim(str_replace(['return([', '","', '\/', '\\', ']'], ['', '', '/', '', ''], $matches[1]), '"');
    $cleanString = preg_replace('#(?<=https:)/+#', '//', $cleanString);
    $responseSecond = getReq($cleanString, $newReferer, $userAgent);
    
    if ($responseSecond !== false) {
        $modifiedCleanString = preg_replace('#(/hls/)[^$]+#', '$1', $cleanString);
        $finalResponse = str_replace('https://', 'https://', str_replace($id, $modifiedCleanString . $id, $responseSecond));
        echo $finalResponse;
    } else {
        die("Source error.");
    }

} else {
    die("No match found.");
}
