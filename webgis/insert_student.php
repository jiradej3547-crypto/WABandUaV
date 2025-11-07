<?php
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$dbname = "mini_data";
$user = "postgres";
$pass = "postgres";

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$pass");
if (!$conn) {
    echo json_encode(["error" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"]);
    exit;
}

// รับค่าจาก GET
$id = isset($_GET['id']) ? $_GET['id'] : null;
$name = isset($_GET['name']) ? $_GET['name'] : null;
$year = isset($_GET['year']) ? $_GET['year'] : null;
$major = isset($_GET['major']) ? $_GET['major'] : null;
$dept = isset($_GET['dept']) ? $_GET['dept'] : null;
$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
$school = isset($_GET['school']) ? $_GET['school'] : null;
$subdistrict = isset($_GET['subdistrict']) ? $_GET['subdistrict'] : null;
$district = isset($_GET['district']) ? $_GET['district'] : null;
$province = isset($_GET['province']) ? $_GET['province'] : null;
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;

// ตรวจสอบค่าครบ
if (!$id || !$name || !$year || !$major || !$dept || !$faculty || !$school || !$subdistrict || !$district || !$province || $lat === null || $lng === null) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

// เพิ่มข้อมูลลงฐานข้อมูล
$sql = "
INSERT INTO student_point 
(id, name, year, major, dept, faculty, school, subdistrict, district, province, geom)
VALUES (
    '" . pg_escape_string($id) . "',
    '" . pg_escape_string($name) . "',
    '" . pg_escape_string($year) . "',
    '" . pg_escape_string($major) . "',
    '" . pg_escape_string($dept) . "',
    '" . pg_escape_string($faculty) . "',
    '" . pg_escape_string($school) . "',
    '" . pg_escape_string($subdistrict) . "',
    '" . pg_escape_string($district) . "',
    '" . pg_escape_string($province) . "',
    ST_SetSRID(ST_MakePoint($lng, $lat), 4326)
)
RETURNING id, name, year, major, dept, faculty, school, subdistrict, district, province, ST_AsGeoJSON(geom) AS geojson;
";

$result = pg_query($conn, $sql);
if (!$result) {
    echo json_encode(["error" => pg_last_error($conn)]);
    exit;
}

// แปลงผลลัพธ์เป็น GeoJSON
$row = pg_fetch_assoc($result);
$geometry = json_decode($row['geojson']);
$feature = [
    "type" => "Feature",
    "geometry" => $geometry,
    "properties" => [
        "id" => $row['id'],
        "name" => $row['name'],
        "year" => $row['year'],
        "major" => $row['major'],
        "dept" => $row['dept'],
        "faculty" => $row['faculty'],
        "school" => $row['school'],
        "subdistrict" => $row['subdistrict'],
        "district" => $row['district'],
        "province" => $row['province']
    ]
];

echo json_encode($feature, JSON_UNESCAPED_UNICODE);
?>
