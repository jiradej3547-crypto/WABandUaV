<?php
header("Content-Type: application/json; charset=UTF-8");

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$hostname_db = "localhost";
$database_db = "mini_data";
$username_db = "postgres";
$password_db = "postgres";
$port_db     = "5432";

// เชื่อมต่อฐานข้อมูล PostgreSQL
$conn = pg_connect("host=$hostname_db port=$port_db dbname=$database_db user=$username_db password=$password_db");

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    echo json_encode(["error" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"]);
    exit;
}

// ดึงข้อมูลทั้งหมดจากตาราง tourist_spots
$query = "SELECT * FROM tourist_spots";
$result = pg_query($conn, $query);

$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}

pg_close($conn);

// ส่งข้อมูลออกเป็น JSON
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
