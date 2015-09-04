<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="author" content="http://tanvietblog.com" />
  <title>Công cụ lấy link nhạc trực tiếp từ MP3 Zing</title>

  <link rel="stylesheet" type="text/css" href="css/styles.css" />

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js"></script>

  <!-- Đoạn mã dùng để đếm số lượt xem tại tanvietblog.com -->
  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-35269794-1']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>

  <script type="text/javascript">
    $(document).ready(function() {

      $('button').click(function() {
        var link = $('#getLink').val();
        var website = $(location).attr('href');

        if (!$.trim(link).length) {
          alert('Vui lòng nhập đường link!');
          return false;
        } else if (!isUrlValid(link)) {
          alert('Đường link không hợp lệ. Vui lòng thử lại!');
          return false;
        } else {
          // Hiển thị loading
          $('.loading').css('display', 'block');
          $('#results').html('').hide();
          $('#error').html('').hide();

          var url = website + 'get_link.php';
          $.post(
            url,
            { link: link },
            function(result) {

              // Ẩn nút loading và hiển thị kết quả tìm được
              $('.loading').css('display', 'none');

              // Chuyển kết quả từ String sang JSON
              result = $.parseJSON(result);

              // Hiển thị lỗi (nếu có)
              if (result.msg !== '') {
                $('#error').html(result.msg).show();
                return;
              }

              var linkData = $.parseJSON(result.data);
              var siteBase = 'http://mp3.zing.vn';
              var imageBase = 'http://image.mp3.zdn.vn';

              $('#results').show();

              // Hiển thị nội dung
              if (result.type === 'song-xml' || result.type === 'album-xml') {
                for (var i = 0; i < linkData.data.length; i++) {
                  var thumbnail, mv_link, lyric;
                  var artist_list = [];
                  var download = [];
                  var item = linkData.data[i];

                  // Nghệ sỹ
                  for (var j = 0; j < item.artist_list.length; j++) {
                    artist_list.push('<a href="' + siteBase + item.artist_list[j].link + '" target="_blank">' + item.artist_list[j].name + '</a>');
                  }

                  // Link download
                  for (var j = 0; j < item.source_list.length; j++) {
                    download.push('<a href="' + item.source_base + '/' + item.source_list[j] + '"  target="_blank">Download</a>');
                  }

                  // Ảnh đại diện
                  thumbnail = item.cover !== '/null' ? (imageBase + item.cover) : null;

                  // MV
                  mv_link = item.mv_link ? (siteBase + item.mv_link) : null;

                  // Lyric
                  lyric = (item.lyric !== 'http://static.mp3.zdn.vn/lyrics/') ? item.lyric : null;

                  // Nội dung hiển thị
                  $('#results').append('<p><strong>THÔNG TIN:</strong></p>');

                  if (thumbnail) {
                    $('#results').append('<img src="' + thumbnail + '"/>');
                  }

                  $('#results').append('<p><strong>Tên bài hát: </strong>' + item.name + '</p>');

                  $('#results').append('<p><strong>Nghệ sỹ: </strong>' + artist_list.join(' ft. ') + '</p>');

                  $('#results').append('<p><strong>Chất lượng: </strong>' + item.qualities.join(', ') + '</p>');

                  if (lyric) {
                    $('#results').append('<p><strong>Lời bài hát: </strong><a href="' + lyric + '" target="_blank">' + lyric + '</a></p>');
                  }

                  if (mv_link) {
                    $('#results').append('<p><strong>Music Video (MV): </strong><a href="' + mv_link + '" target="_blank">' + mv_link + '</a></p>');
                  }

                  $('#results').append('<p><strong>Tải về: </strong>' + download.join(', ') + '</p>');

                  $('#results').append('<div class="row"></div>');
                }
              } else if (result.type === 'video-xml') {
                for (var i = 0; i < linkData.data.item.length; i++) {
                  var download = [];
                  var item = linkData.data.item[i];

                  // Link download
                  for (var j = 0; j < item.source.length; j++) {
                    download.push('<a href="' + item.source[j] + '"  target="_blank">' + item.quality[j] + '</a>');
                  }

                  // Nội dung hiển thị
                  $('#results').append('<p><strong>THÔNG TIN:</strong></p>');

                  $('#results').append('<p><strong>Tên bài hát: </strong><a href="' + item.titleLink + '" target="_blank">' + item.title + '</a></p>');

                  $('#results').append('<p><strong>Nghệ sỹ: </strong><a href="' + item.artistLink + '" target="_blank">' + item.artist + '</a></p>');

                  $('#results').append('<p><strong>Tải về với chất lượng: </strong>' + download.join(' | ') + '</p>');

                  $('#results').append('<div class="row"></div>');
                }

                if (linkData.data.suggest && linkData.data.suggest.item) {
                  $('#results').append('<p><strong>CÓ THỂ BẠN MUỐN XEM:</strong></p>');

                  for (var i = 0; i < linkData.data.suggest.item.length; i++) {
                    var item = linkData.data.suggest.item[i];

                    $('#results').append('<img class="suggest-thumbnail" src="' + item.thumbnail + '"/>');

                    $('#results').append('<p><strong>Tên bài hát: </strong><a href="' + siteBase + item.link + '" target="_blank">' + item.title + '</a></p>');

                    $('#results').append('<p><strong>Nghệ sỹ: </strong>' + item.performer + '</p>');

                    $('#results').append('<div class="row"></div>');
                  }
                }
              }
            }
          );
        }
      });

      function isUrlValid(url) {
        return /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g.test(url);
      }
    });
    </script>

</head>

<body>
  <div id="wapper">

    <div id="header">
      <ul id="main_nav">
        <li><a href="http://tanvietblog.com/" title="Trang chủ" target="_blank">HOME</a></li>
        <li>
          <a href="http://tanvietblog.com/category/lap-trinh-web" title="" target="_blank">LẬP TRÌNH WEB</a>
          <ul>
            <li><a href="http://tanvietblog.com/category/kien-thuc-chung" title="" target="_blank">KIẾN THỨC CHUNG</a></li>
            <li><a href="http://tanvietblog.com/category/php" title="" target="_blank">PHP</a></li>
            <li><a href="http://tanvietblog.com/category/wordpress" title="" target="_blank">WORDPRESS</a></li>
            <li><a href="http://tanvietblog.com/category/jquery" title="" target="_blank">JQUERY</a></li>
            <li><a href="http://tanvietblog.com/category/css" title="" target="_blank">CSS</a></li>
          </ul>
        </li>
        <li>
          <a href="http://tanvietblog.com/category/lap-trinh-di-dong" title="" target="_blank">LẬP TRÌNH DI ĐỘNG</a>
          <ul>
            <li><a href="http://tanvietblog.com/category/iphone" title="" target="_blank">IPHONE</a></li>
            <li><a href="http://tanvietblog.com/category/android" title="" target="_blank">ANDROID</a></li>
          </ul>
        </li>
        <li><a href="http://tanvietblog.com/about-me" title="" target="_blank">ABOUT ME</a></li>
        <li><a href="http://tanvietblog.com/contact" title="" target="_blank">CONTACT</a></li>
        <li><a href="http://tanvietblog.com/sitemap" title="" target="_blank">SITEMAP</a></li>
      </ul>

      <div id="logo"><a href="http://tanvietblog.com" id="homepage" target="_blank" title="Online Web Development & Mobile Tutorials" alt="Online Web Development & Mobile Tutorials">Online Web Development & Mobile Tutorials</a></div>

    </div><!-- //header -->

    <div id="content">
      <h1>[Web] Công cụ lấy link nhạc trực tiếp từ MP3 Zing</h1>

      Demo cho bài viết <a href="http://tanvietblog.com/2013/06/09/web-lay-link-nhac-truc-tiep-tu-mp3-zing">[Web] Lấy link nhạc trực tiếp từ MP3 Zing</a>

      <br /><br />

      Nhập đường link<br />
      <input id="getLink" type="text" value="" size="50"/>

      <p>
        Ví dụ:
          <br />+ Bài hát - http://mp3.zing.vn/bai-hat/Say-You-Do-Tien-Tien/ZW70EIUE.html
          <br />+ Album - http://mp3.zing.vn/album/Dale-Pitbull/ZWZBFAFC.html
          <br />+ Video - http://mp3.zing.vn/video-clip/Buong-Vu-Thao-My-Kimmese/ZW7IC0EB.html
      </p>

      <button>Lấy link</button><br /><br />

      <div class="loading"></div>

      <div id="results"></div>

      <div id="error"></div>
    </div><!-- //content -->

  </div>
</body>
</html>