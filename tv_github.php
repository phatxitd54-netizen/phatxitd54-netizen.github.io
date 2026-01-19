<?php
// Single-file IPTV Player (Load from GitHub raw URL) + Proxy + Per-channel page

$playlist_url = "https://raw.githubusercontent.com/ngvhiem/IPTV/main/IPTV.m3u";

$content = @file_get_contents($playlist_url);
if (!$content) {
    die("Không tải được playlist!");
}

$lines = explode("\n", $content);
$channels = [];
$current = null;

foreach ($lines as $line) {
    $line = trim($line);

    if (strpos($line, "#EXTINF") === 0) {
        preg_match('/,(.*)$/', $line, $matches);
        $name = $matches[1] ?? "No name";

        preg_match('/group-title="([^"]+)"/', $line, $g);
        $group = $g[1] ?? "Khác";

        preg_match('/tvg-logo="([^"]+)"/', $line, $l);
        $logo = $l[1] ?? "";

        $current = [
            "name" => $name,
            "group" => $group,
            "logo" => $logo
        ];
    }
    else if ((strpos($line, "http") === 0 || strpos($line, "rtp") === 0 || strpos($line, "udp") === 0) && $current) {
        $current["url"] = $line;
        $channels[] = $current;
        $current = null;
    }
}

// ===== PROXY MODE =====
if (isset($_GET['proxy'])) {
    $url = $_GET['proxy'];
    header("Content-Type: application/octet-stream");
    readfile($url);
    exit;
}

// ===== WATCH MODE =====
if (isset($_GET['watch'])) {
    $id = intval($_GET['watch']);
    if (!isset($channels[$id])) die("Không tồn tại kênh!");

    $channel = $channels[$id];
    ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($channel['name']) ?></title>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://cdn.dashjs.org/latest/dash.all.min.js"></script>
</head>
<body style="background:#000;color:white;font-family:Arial">

<h2><?= htmlspecialchars($channel['name']) ?></h2>
<video id="player" controls autoplay width="100%"></video>

<script>
let url = "?proxy=<?= urlencode($channel['url']) ?>";
let video = document.getElementById("player");

if (url.includes(".m3u8")) {
    if (Hls.isSupported()) {
        let hls = new Hls();
        hls.loadSource(url);
        hls.attachMedia(video);
    } else {
        video.src = url;
    }
} else if (url.includes(".mpd")) {
    let player = dashjs.MediaPlayer().create();
    player.initialize(video, url, true);
} else {
    video.src = url;
}
</script>

<p><a href="./" style="color:white">Back</a></p>
</body>
</html>
<?php
    exit;
}

// ===== HOME MODE =====
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>TV Online</title>
<style>
body{background:#111;color:white;font-family:Arial}
.channel{display:flex;align-items:center;padding:10px;border-bottom:1px solid #333}
.channel img{height:40px;margin-right:10px}
a{color:white;text-decoration:none}
</style>
</head>
<body>

<h2>TV Online</h2>

<?php foreach($channels as $i => $c): ?>
    <a href="?watch=<?= $i ?>">
        <div class="channel">
            <?php if($c['logo']): ?>
                <img src="<?= htmlspecialchars($c['logo']) ?>">
            <?php endif; ?>
            <?= htmlspecialchars($c['name']) ?>
        </div>
    </a>
<?php endforeach; ?>

</body>
</html>
