<?php
header("Content-Type: application/json; charset=UTF-8");

// เชื่อมต่อ PostgreSQL
$host = "localhost";
$dbname = "mini_data";
$user = "postgres";
$pass = "postgres";

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$pass");
if (!$conn) {
    echo json_encode(["error" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"]);
    exit;
}

// ตารางทั้งหมดที่ต้องการดึง
$tables = ["agi64", "agi65", "agi66", "agi67"];
$allData = [];

foreach ($tables as $table) {
    $sql = "SELECT s_id, s_name, course, dept, faculty, school, lat, lon, tambon, amphoe, province FROM $table";
    $res = pg_query($conn, $sql);
    if ($res) {
        while ($row = pg_fetch_assoc($res)) {
            $allData[] = [
                "id" => $row['s_id'],
                "ชื่อ" => trim($row['s_name']),
                "หลักสูตร" => $row['course'],
                "ภาควิชา" => $row['dept'],
                "คณะ" => $row['faculty'],
                "จบจากโรงเรียน" => $row['school'],
                "ละติจูด" => floatval($row['lat']),
                "ลองติจูด" => floatval($row['lon']),
                "ตำบล" => $row['tambon'],
                "อำเภอ" => $row['amphoe'],
                "จังหวัด" => $row['province'],
                "ประเภท" => $table  // แยกปี
            ];
        }
    }
}

echo json_encode($allData);
pg_close($conn);
?>
