<?php
header("Content-Type: application/json; charset=UTF-8");

// LINK DANH SÁCH KÊNH GỐC (bị ẩn)
$sourceUrl = "https://raw.githubusercontent.com/HAO1801/HAO/refs/heads/main/4";

// Lấy dữ liệu
$ch = curl_init($sourceUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$data = curl_exec($ch);
curl_close($ch);

// Nếu lỗi
if ($data === false || empty($data)) {
    echo json_encode(["error" => "Không tải được danh sách kênh"]);
    exit;
}

// Xuất JSON ra cho JS
echo $data;