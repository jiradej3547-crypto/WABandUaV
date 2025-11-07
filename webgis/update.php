<?php
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost"; $dbname = "mini_data"; $user = "postgres"; $pass = "postgres";
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$pass");
if (!$conn) { echo json_encode(["error"=>"ไม่สามารถเชื่อมต่อฐานข้อมูลได้"]); exit; }

$oldName = $_GET['oldName'] ?? '';
$name = $_GET['name'] ?? '';
$type = $_GET['type'] ?? '-';
$tambon = $_GET['tambon'] ?? '-';
$amphoe = $_GET['amphoe'] ?? '-';
$province = $_GET['province'] ?? '-';
$open = $_GET['open'] ?? '-';
$close = $_GET['close'] ?? '-';
$phone = $_GET['phone'] ?? '-';
$lat = floatval($_GET['lat'] ?? 0);
$lng = floatval($_GET['lng'] ?? 0);

if (!$oldName || !$name || !isset($_GET['lat']) || !isset($_GET['lng'])) {
    echo json_encode(["error"=>"ข้อมูลไม่ครบ"]);
    exit;
}

// อัปเดต
$sql = 'UPDATE tourist_spots SET "ชื่อ"=$1, "ประเภท"=$2, "ตำบล"=$3, "อำเภอ"=$4, "จังหวัด"=$5,
        "เวลาเปิด"=$6, "เวลาปิด"=$7, "เบอร์โทร"=$8, "ละติจูด"=$9, "ลองติจูด"=$10
        WHERE "ชื่อ"=$11';

$result = pg_query_params($conn, $sql, [$name,$type,$tambon,$amphoe,$province,$open,$close,$phone,$lat,$lng,$oldName]);

if (!$result) {
    echo json_encode(["error"=>pg_last_error($conn)]);
    exit;
}

// ดึงข้อมูลกลับ
$resAll = pg_query($conn,'SELECT * FROM tourist_spots ORDER BY "ชื่อ"');
$allData=[];
while($row=pg_fetch_assoc($resAll)) $allData[]=$row;
echo json_encode($allData, JSON_UNESCAPED_UNICODE);

pg_close($conn);

?>
