<?php
header("Content-Type: application/x-mpegURL; charset=UTF-8");

// LINK M3U GỐC (BỊ ẨN)
$sourceUrl = "https://raw.githubusercontent.com/HAO1801/HAO/refs/heads/main/4";

// Lấy nội dung M3U
$ch = curl_init($sourceUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Nếu lỗi
if ($data === false || empty($data) || $httpCode !== 200) {
    echo "#EXTM3U\n# Lỗi: Không tải được danh sách kênh\n";
    exit;
}

// Không sửa link MPD — chỉ proxy M3U
echo $data;
