<?php
header("Content-Type: application/json; charset=UTF-8");

// เชื่อมต่อฐานข้อมูล
$host = "localhost";
$dbname = "mini_data";
$user = "postgres";
$pass = "postgres";
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$pass");

if (!$conn) {
    echo json_encode(["error" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"]);
    exit;
}

// รับค่า name ที่จะลบ
$name = $_GET['name'] ?? '';

if (!$name) {
    echo json_encode(["error" => "ข้อมูลไม่ครบ"]);
    exit;
}

// ลบข้อมูลจากตาราง
$sql = "DELETE FROM tourist_spots WHERE ชื่อ = $1";
$result = pg_query_params($conn, $sql, array($name));

if ($result) {
    // ส่งกลับข้อมูลทั้งหมดหลังลบ
    $res = pg_query($conn, "SELECT * FROM tourist_spots");
    $data = pg_fetch_all($res);
    echo json_encode($data);
} else {
    echo json_encode(["error" => "ไม่สามารถลบข้อมูลได้"]);
}
?>
