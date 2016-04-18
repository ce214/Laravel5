<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/13
 * Time: 11:24
 */

require( __DIR__ . '/../etc/DB_config.php');
require( __DIR__ . '/../etc/global_defines.php');

$json = file_get_contents("php://input");
$arr = json_decode($json, true);
$date = $arr['date'];

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Retrieve the score data from MySQL
$query = "SELECT * FROM data WHERE date = '$date'";
$result = mysqli_query($link, $query);

while ($data_info = mysqli_fetch_array($result,MYSQL_ASSOC)){ //返回查询结果到数组
    $date = $data_info["date"]; //将数据从数组取出
	$music = $data_info['music'];

$json_data = json_encode($data_info, JSON_UNESCAPED_SLASHES);
echo $json_data;
}

//$json_data = json_encode($data_info);
//echo $json_data;

mysqli_free_result($result);
mysqli_close($link);


//echo 'json_text is ' . $arr;
