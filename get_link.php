<?php

  /**
   * @author Tấn Việt
   * @copyright 2015
   * @website http://tanvietblog.com
   */

  if (!empty($_POST['link'])) {
    $link = trim($_POST['link']);

    // Initialize the variables
    $results = array(
      'msg' => '',
      'type' => '',
      'data' => ''
    );

    // Get page source
    $html = getPageSource($link);

    // Parse the page source to get link song with its XML format
    $pattern = '/data-xml=\"(.*?)\"/';
    preg_match($pattern, $html, $matches);

    if (isset($matches[1]) && (substr($matches[1], 0, 23) === 'http://mp3.zing.vn/xml/')) {
      $url = $matches[1];
      $type = getLinkType($url);

      // Get link information with JSON format
      if ($type === 'song-xml' || $type === 'album-xml') {
        $url = str_replace('/xml/', '/html5xml/', $url);
        $results['type'] = $type;
        $results['data'] = getPageSource($url);
      } else if ($type === 'video-xml') {
        $results['type'] = $type;
        $results['data'] = getPageSource($url . '?format=json');
      }
    } else {
      $results['msg'] = 'Link bạn nhập vào không đúng hoặc bài hát (album, video) đã bị xóa khỏi hệ thống. Bạn vui lòng kiểm tra lại.';
    }

    echo json_encode($results);
  }

  /*=== HELPER ===*/

  /**
   * Using cURL to grab page content
   * @param  String $url The link need to grab
   * @return Object      The page content
   *
   * @note   Zing MP3 is using gzip
   */
  function getPageSource($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
  }

  /**
   * Determine the link type (Song/Album/Video)
   * The link with its XML format like this "http://mp3.zing.vn/xml/:linkType/:realId"
   *
   * @param  String $link The link to get its type
   * @return String       The type of the given link (song-xml, album-xml, video-xml)
   */
  function getLinkType($link) {
    $arr = explode('/', $link);
    return $arr[4];
  }
?>