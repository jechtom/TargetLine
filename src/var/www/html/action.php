<?php
$action = $_SERVER['QUERY_STRING'];

$counter_file = "img/_counter.txt";
$counter = 0;
if(file_exists($counter_file)) 
{
  $counter = intval(file_get_contents($counter_file));
}

$actionIsNew = (strpos($action, 'new') !== false);

if($actionIsNew) 
{
  $counter = $counter + 1;
}

$counter_str = sprintf("%05d", $counter);
$counter_rel = "img/i$counter_str.jpg";

if($actionIsNew)
{
  $output = shell_exec("fswebcam -r 1280x720 -S 15 --fps 30 $counter_rel");
  file_put_contents($counter_file, $counter);
}

$result = array( 'id' => $counter, 'src' => $counter_rel );

header('Content-type: application/json');
echo json_encode($result);
?>
