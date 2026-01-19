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
