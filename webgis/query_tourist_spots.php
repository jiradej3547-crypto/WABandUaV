<?php
header("Content-Type: application/json; charset=UTF-8");
$host = "localhost";
$dbname = "mini_data";
$user = "postgres";
$pass = "postgres";
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$pass");

$lat = $_GET['lat'];
$lng = $_GET['lng'];
$distance = $_GET['distance'];

$sql = "
SELECT jsonb_build_object(
  'type', 'FeatureCollection',
  'features', jsonb_agg(
    jsonb_build_object(
      'type', 'Feature',
      'geometry', ST_AsGeoJSON(geom)::jsonb,
      'properties', to_jsonb(t) - 'geom'
    )
  )
) AS geojson
FROM (
  SELECT * FROM tourist_spots
  WHERE ST_DWithin(
    geom::geography,
    ST_SetSRID(ST_MakePoint($lng, $lat), 4326)::geography,
    $distance
  )
) AS t;
";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo $row['geojson'];
?>
