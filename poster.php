<?php

/*
  Proxy to get video poster from YouTube
*/

$id = $_GET['id'] ?? '';

// Check if the ID has a valid format
if (!preg_match('/^[a-zA-Z0-9-_]+$/', $id)) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
  die();
}

// High resolution poster and alternative lower resolution poster
$url_max = "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg";
$url_alt = "https://i.ytimg.com/vi/{$id}/hqdefault.jpg";

$handle = curl_init();
curl_setopt($handle, CURLOPT_HEADER, false);
curl_setopt($handle, CURLOPT_VERBOSE, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_URL, $url_max);
$output = curl_exec($handle);
$retcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

// If max resolution poster is not available, try the alternative version
if ($retcode == 404) {
  curl_setopt($handle, CURLOPT_URL, $url_alt);
  $output = curl_exec($handle);
  $retcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
}

// Send poster image to client if available
if ($retcode == 200) {
  $infos = curl_getinfo($handle);
  header('Content-type: ' . $infos['content_type']);
  echo $output;
} else {
  header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
}

curl_close($handle);
