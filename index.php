<?php

// Get the 'id' parameter from the URL, or terminate the script if not provided.
$id = $_GET['id'] ?? die("Please Provide ID.");

// Get the user agent from the request, or terminate the script if not available.
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? die("Really Kid.");

// Define the initial URL with the provided ID for fetching the first response.
$initialUrl = 'https://apex2nova.com/premium.php?player=desktop&live=' . urlencode($id);

// Define referer values for requests.
$initialReferer = 'https://stream.crichd.sc/'; // First request referer.
$newReferer = 'https://apex2nova.com/'; // Second request referer.

// Regular expression pattern to extract the URL from the first response.
$pattern = '/return\(\[(.*)\]/';

/**
 * Function to send a GET request using cURL.
 *
 * @param string $url The URL to fetch.
 * @param string $Referer The referer header to use.
 * @param string $userAgent The user agent header to use.
 * @return string|false The response body or false on failure.
 */
function getReq($url, $Referer, $userAgent) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $Referer);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disables SSL verification (not recommended for production).
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Perform the first request to get the initial response.
$response = getReq($initialUrl, $initialReferer, $userAgent);

// Check if the response contains the expected pattern.
if (preg_match($pattern, $response, $matches)) {
    // Clean up the extracted URL.
    $cleanString = trim(str_replace(['return([', '","', '\/', '\\', ']'], ['', '', '/', '', ''], $matches[1]), '"');
    
    // Ensure proper formatting of the URL (removing duplicate slashes after "https:").
    $cleanString = preg_replace('#(?<=https:)/+#', '//', $cleanString);
    
    // Perform the second request with the extracted URL.
    $responseSecond = getReq($cleanString, $newReferer, $userAgent);
    
    if ($responseSecond !== false) {
        // Modify the extracted URL to adjust the path.
        $modifiedCleanString = preg_replace('#(/hls/)[^$]+#', '$1', $cleanString);
        
        // Replace occurrences of the original ID with the modified URL.
        $finalResponse = str_replace('https://', 'https://', str_replace($id, $modifiedCleanString . $id, $responseSecond));
        
        // Output the final processed response.
        echo $finalResponse;
    } else {
        die("Source error."); // Error handling if the second request fails.
    }

} else {
    die("No match found."); // Error handling if the expected pattern isn't found in the response.
}
