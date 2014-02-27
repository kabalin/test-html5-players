<?php
$player = $_GET['player'];

$players = array('flowplayer');

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
        'sources' => array('video/flash' => 'http://stream.flowplayer.org/flowplayer-700.flv'),
    ),
);

function get_player_instance($data) {
    $output = "<div class=\"player_${data['name']}\" style=\"width: 624px; height: 260px;\">\n";
    $output .= "    <${data['type']} controls width=\"624\" height=\"260\" preload=\"metadata\">\n";
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
    </head>
    <body>
        <h2><?php echo ucfirst($player); ?></h2>
    <?php
    foreach ($datasources as $name => $data) {
        $data['name'] = $name; ?>
        <div class="player_instance">
            <p><?php echo strtoupper($name);?></p>
            <?php output_player_instance($data); ?>
            <p class="info">&nbsp;</p>
            <pre><?php output_player_instance_code($data); ?></pre>
        </div>
    <?php
    } ?>

    <div class="player_instance">
        <p>RTMP/HLS</p>
        <div class="player_rtmp" style="width: 624px; height: 260px;" data-rtmp="rtmp://tes-ams.lancs.ac.uk/public/_definst_/">
            <video controls width="624" height="260" preload="metadata">
                <source type="application/x-mpegurl" src="http://tes-ams.lancs.ac.uk:8134/hls-public/users/cpadai/big_buck_bunny/big_buck_bunny_720p_1mbps.f4v.m3u8">
                <source type="video/flash" src="mp4:users/cpadai/big_buck_bunny/big_buck_bunny_720p_1mbps.f4v">
            </video>
        </div>
        <p class="info">&nbsp;</p>
    </div>
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