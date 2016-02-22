<?


function startswith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}



$site = $_GET['site'];
if(!($seasonStart = $_GET['start'])) $seasonStart = 'Jan  1';
if(!($seasonEnd = $_GET['end'])) $seasonEnd = 'Dec 31';
if(!($mult = $_GET['mult'])) $mult = 1;
if(!($useprov = $_GET['provisional'])) $useprov = false;


$seasonStart = date('z',strtotime($seasonStart)) + 1;
$seasonEnd = date('z',strtotime($seasonEnd)) + 1;

$url = "http://waterdata.usgs.gov/ak/nwis/dv?cb_00060=on&format=rdb&period=&begin_date=1900-01-01&site_no=$site&referred_module=sw";
$array = file($url);
$name = trim(str_replace('#', '', $array[14]));

$header = true;
$days = 0;
for($i=0;$i<count($array);$i++){
	if(strpos($array[$i],"USGS") === 0) {
	$data = preg_split('/\s+/', $array[$i]);
   	if( ((date('z',strtotime($data[2]))+1) <= $seasonStart) || ((date('z',strtotime($data[2]))+1) > $seasonEnd+1)) continue;
   		$data = preg_split('/\s+/', $array[$i]);

   		if(startswith($data[4],'A')) {
   			$flow[] = $data[3];
   			$dates[] = strtotime($data[2]);
   		}
   	}
}

$totaldays = count($dates);
$totalflo = count($flow);



rsort($flow);


$flowduration = array();
#$flowduration[0] = array(0,(float)$flow[0]*$mult);
$flowduration[] = array(1,(float)$flow[round(.01*$totalflo)]*$mult);
$flowduration[] = array(3,(float)$flow[round(.03*$totalflo)]*$mult);
for($i=0;$i<10;$i++){
  $array = array();
  $dur = ($i+1)*10;
  $flo = round($dur/100*$totalflo-1);
  $array[0] = $dur;
  $array[1] = (float)$flow[$flo]*$mult;
  $flowduration[]=$array;
  }
  $json['dur_data'] = $flowduration;
  $json['name'] = $name;

  echo json_encode($json);
?>
