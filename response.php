<?php
// ini_set('max_input_time', 30000);
// ini_set('max_execution_time', 30000);

// Determine location's polygon ID
function searchForPoly($lat, $lng, $array) {
  foreach ($array as $key => $val) {
    if ($lat <= $val['0'][lat] and $lat > $val['1'][lat] and $lng <= $val['0'][lng] and $lng > $val['2'][lng]) {
      return $key;
    }
  }
  return null;
}

$month = $_POST["month"];
$years = $_POST["years"];
$polyCoord = $_POST['jsonPolyCoord'];
$polyCoord = json_decode($polyCoord, true);
$yearsSplit = explode('-', $years);
$yearStart = $yearsSplit[0];
$yearEnd = $yearsSplit[1];

// Connect to the database
$connection = mysqli_connect("localhost", "root", "letsgodonuts", "BIRDS_DB");

// // Calculate polygon IDs for each record
// $file = fopen("polys.txt","a+");


// $query = mysqli_query($connection, "SELECT * FROM MAIN WHERE MONTH = 12");
// while ($row = mysqli_fetch_assoc($query)) {
//   $polyId = searchForPoly($row[LATITUDE], $row[LONGITUDE], $polyCoord);
//   fwrite($file, $row[ID] . "," . $polyId . "\n");
// }
// fclose($file);

// // Calculate polygon's coordinates for SaTScan

// $file = fopen("polyss.txt","w");
// for($i = 0; $i < $sizePoly; ++$i) {
//   fwrite($file, $i . "," . $polyCoord[$i][0]['lat'] . "," . $polyCoord[$i][0]['lng'] . "\n");
// }
// fclose($file);

$query = mysqli_query($connection,
 "SELECT * FROM MAIN WHERE MONTH IN ($month) AND YEAR >= $yearStart AND YEAR <= $yearEnd");
// $query = mysqli_query($connection,
//  "SELECT * FROM MAIN WHERE MONTH IN ($month) AND YEAR >= $yearStart AND YEAR <= $yearEnd");

  // Create empty array to hold query results
$someArray = [];

  // Loop through query and push results into $someArray;
while ($row = mysqli_fetch_assoc($query)) {
  array_push($someArray, [
    'sci_name'   => $row['SCI_NAME'],
    'LATITUDE' => $row['LATITUDE'],
    'LONGITUDE' => $row['LONGITUDE'],
    'OBSERVATION_COUNT' => $row['COUNT'],
    'month' => $row['MONTH'],
    'year' => $row['YEAR'],
    'polyid' => $row['POLYGON'],
    'sci_order' => $row['SCI_ORDER']
    ]);
}

$size = count($someArray);
$sizePoly = count($polyCoord);

$countArr = array();
$countArr = array_pad($countArr, $sizePoly, 0);

$orderArr = array();
$orderArr = array_pad($orderArr, $sizePoly, array());

$speciesArr = array();
$speciesArr = array_pad($speciesArr, $sizePoly, array());

for($i = 0; $i < $size; ++$i) {
  $countArr[$someArray[$i]['polyid']] += $someArray[$i]['OBSERVATION_COUNT'];
  array_push($orderArr[$someArray[$i]['polyid']], $someArray[$i]['sci_order']);
  array_push($speciesArr[$someArray[$i]['polyid']], $someArray[$i]['sci_name']);
}

for($i = 0; $i < $sizePoly; ++$i) {
  $orderArr[$i] = count(array_unique($orderArr[$i]));
  $speciesArr[$i] = count(array_unique($speciesArr[$i]));
}


  // Convert the Array to a JSON String and echo it
$someJSON = json_encode(array('count'=>$countArr, 'orders'=>$orderArr, 'species'=>$speciesArr));

echo $someJSON;
?>