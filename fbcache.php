<?php
//$link = mysql_connect('localhost', 'root', '99887766');
//if (!$link) {
//    die('Not connected : ' . mysql_error());
//}
//
//// make foo the current db
//$db_selected = mysql_select_db('roses_only_singapore', $link);
//if (!$db_selected) {
//    die ('Can\'t use foo : ' . mysql_error());
//}
//
//$categories = array();
//
//$result = mysql_list_tables('roses_only_singapore');
//while($row = mysql_fetch_array($result))
//{
//    $table_name = $row[0];
//    if(strrpos($table_name,"catalog_product_flat")!== false){
//        array_push($categories,$table_name);
//    }
//}
//
//foreach($categories as $category)
//{
//    $consulta_data = mysql_query("select * from $category") or die(mysql_error());
//    while($row = mysql_fetch_array($consulta_data))
//    {
//        $url = "http://rosesonly.com.sg/".$row["url_path"];
//        $debug_url = "http://developers.facebook.com/tools/debug/og/object?q=$url&fbrefresh=any";
////        $response = http_get($debug_url);
//        $ch = curl_init($debug_url);
//        $response = curl_exec ($ch);
//        curl_close ($ch);
//        echo $response."<br>";
//    }
//    echo "<br>";
//}

$url = "http://rosesonly.com.sg/occasion/proposals/presentation-styled-long-stemmed-roses-gift-box-519.html?back=http://rosesonly.com.sg//occasion/proposals.html";
$debug_url = "http://developers.facebook.com/tools/debug/og/object?q=$url&fbrefresh=any";
//        $response = http_get($debug_url);
$ch = curl_init($debug_url);
$response = curl_exec ($ch);
curl_close ($ch);
echo $response."<br>";


?>