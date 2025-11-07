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

$lat     = isset($_GET['lat']) ? pg_escape_string($_GET['lat']) : 0;
$lng     = isset($_GET['lng']) ? pg_escape_string($_GET['lng']) : 0;
$name    = isset($_GET['name']) ? pg_escape_string($_GET['name']) : '';
$type    = isset($_GET['type']) ? pg_escape_string($_GET['type']) : '';
$tambon  = isset($_GET['tambon']) ? pg_escape_string($_GET['tambon']) : '';
$amphoe  = isset($_GET['amphoe']) ? pg_escape_string($_GET['amphoe']) : '';
$province= isset($_GET['province']) ? pg_escape_string($_GET['province']) : '';
$open    = isset($_GET['open']) ? pg_escape_string($_GET['open']) : '';
$close   = isset($_GET['close']) ? pg_escape_string($_GET['close']) : '';
$phone   = isset($_GET['phone']) ? pg_escape_string($_GET['phone']) : '';

$sql = "INSERT INTO tourist_spots 
        (ชื่อ, ประเภท, ตำบล, อำเภอ, จังหวัด, เวลาเปิด, เวลาปิด, เบอร์โทร, ละติจูด, ลองติจูด)
        VALUES 
        ('$name', '$type', '$tambon', '$amphoe', '$province', '$open', '$close', '$phone', $lat, $lng)";

$result = pg_query($conn, $sql);

if ($result) {
    $geojson = [
        "type" => "FeatureCollection",
        "features" => [[
            "type" => "Feature",
            "geometry" => [
                "type" => "Point",
                "coordinates" => [(float)$lng, (float)$lat]
            ],
            "properties" => [
                "ชื่อ" => $name,
                "ประเภท" => $type,
                "ตำบล" => $tambon,
                "อำเภอ" => $amphoe,
                "จังหวัด" => $province
            ]
        ]]
    ];
    echo json_encode($geojson, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["error" => pg_last_error($conn)]);
}
?>
