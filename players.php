<?php
$player = $_GET['player'];

$players = array('flowplayer', 'videojs');

if (empty($player) || !in_array($player, $players)) {
    $player = 'native';
}

$datasources = array(
    'mp4' => array(
        'type' => 'video',
        'sources' => array('video/mp4' => 'http://stream.flowplayer.org/bauhaus/624x260.mp4'),
    ),
    'webm' => array(
        'type' => 'video',
        'sources' => array('video/webm' => 'http://stream.flowplayer.org/bauhaus/624x260.webm'),
    ),
    'ogg' => array(
        'type' => 'video',
        'sources' => array('video/ogg' => 'http://stream.flowplayer.org/bauhaus/624x260.ogv'),
    ),
    'flv' => array(
        'type' => 'video',
        'sources' => array('video/x-flv' => 'http://stream.flowplayer.org/flowplayer-700.flv'),
    ),
    'rtmp' => array(
        'displayname' => 'RTMP/HLS',
        'attributes' => array('data-rtmp' => 'rtmp://stream.blacktrash.org/cfx/st/'),
        'type' => 'video',
        'sources' => array(
            'application/x-mpegURL' => 'http://media.blacktrash.org/stsp.m3u8',
            'video/x-flv' => 'mp4:stsp',
        ),
    ),
    'mp3' => array(
        'type' => 'audio',
        'sources' => array('audio/mpeg' => 'http://releases.flowplayer.org/data/fake_empire.mp3'),
    ),

);

function get_player_instance($data) {
    $attrs = array();
    if (isset($data['attributes'])) {
        foreach ($data['attributes'] as $attrname => $attrvalue) {
            $attrs[] = "$attrname=\"$attrvalue\"";
        }
    }
    $output = "<div class=\"player_${data['name']}\" style=\"width: 624px; height: 260px;\" " . implode(' ', $attrs) . ">\n";
    $output .= "    <${data['type']} id=\"player_${data['name']}\" controls width=\"624\" height=\"260\" preload=\"metadata\">\n";
    foreach ($data['sources'] as $mime => $src) {
        $output .= "        <source type=\"$mime\" src=\"$src\">\n";
    }
    $output .= "    </${data['type']}>\n";
    $output .= "</div>";
    return $output;
}

function output_player_instance($data) {
    $output = get_player_instance($data);
    echo $output;
}

function output_player_instance_code($data) {
    $output = get_player_instance($data);
    echo htmlspecialchars($output);
}

function flowplayer_head() {
    $head = <<<EOF
        <title>Flowplayer HTML5</title>
        <link rel="stylesheet" href="//releases.flowplayer.org/5.4.6/skin/functional.css">
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//releases.flowplayer.org/5.4.6/flowplayer.min.js"></script>
EOF;
    echo $head;
}

function videojs_head() {
    $head = <<<EOF
        <title>Video.JS</title>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//vjs.zencdn.net/4.4/video.js"></script>
        <link href="//vjs.zencdn.net/4.4/video-js.css" rel="stylesheet">
EOF;
    echo $head;
}

function native_head() {
    $head = <<<EOF
        <title>Native</title>
EOF;
    echo $head;
}

function flowplayer_script() {
    $head = <<<EOF
        <script>
            $(function () {
                var exts = ['mp4', 'webm', 'ogg', 'flv', 'rtmp'];

                for(var i = 0; i < exts.length; i++) {
                    $(".player_" + exts[i]).flowplayer();
                    var api = $(".player_" + exts[i]).data("flowplayer");
                    $(".player_" + exts[i]).next().text('Engine in use: ' + api.engine);
                }
            });
        </script>
EOF;
    echo $head;
}

function videojs_script() {
    $head = <<<EOF
        <script>
            $(function () {
                var exts = ['mp4', 'webm', 'ogg', 'flv', 'mp3'];

                for(var i = 0; i < exts.length; i++) {
                    $("#player_" + exts[i]).attr('class', 'video-js vjs-default-skin vjs-big-play-centered');
                    videojs($("#player_" + exts[i])[0], {}, function(){});
                }
            });
        </script>
EOF;
    echo $head;
}

function native_script() {
    echo '';
}
?>
<!doctype html>
<html>
    <head>
       <meta charset="utf-8">
       <?php call_user_func($player . '_head'); ?>
       <style>
           .player_instance {
               border: 1px solid;
               padding: 1em;
               margin: 0.5em;
           }
           .player_instance .info {
               margin: 1em 0 0 0;
           }
       </style>
       <link rel="stylesheet" href="http://yandex.st/highlightjs/8.0/styles/default.min.css">
       <script src="http://yandex.st/highlightjs/8.0/highlight.min.js"></script>
       <script>
           hljs.initHighlightingOnLoad();
       </script>
    </head>
    <body>
        <h2><?php echo ucfirst($player); ?></h2>
    <?php
    foreach ($datasources as $name => $data) {
        $data['name'] = $name;
        if (!isset($data['displayname'])) {
            $data['displayname'] = $name;
        } ?>
        <div class="player_instance">
            <p><?php echo strtoupper($data['displayname']);?></p>
            <?php output_player_instance($data); ?>
            <p class="info">&nbsp;</p>
            <pre><code><?php output_player_instance_code($data); ?></code></pre>
        </div>
    <?php
    } ?>

    <!--<div class="player_instance">
        <p>MP3</p>
        <div class="flowplayer_mp3">
            <audio controls>
                <source type="audio/mpeg" src="http://releases.flowplayer.org/data/fake_empire.mp3">
            </audio>
        </div>
        <p class="info">&nbsp;</p>
    </div>--->
    <?php call_user_func($player . '_script'); ?>
    </body>
</html>
