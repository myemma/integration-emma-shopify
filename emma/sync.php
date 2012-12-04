<?php
session_id();
session_start();
include('../lib/shopify_api.php');
include('../lib/EMMAAPI.class.php');
$json = file_get_contents('php://input');
$obj = json_decode($json);
$accepts_marketing=$obj->buyer_accepts_marketing;
$buyer_email=$obj->email;
    /*$myFile = "testFile.txt";
    $fh = fopen($myFile, 'w') or die("can't open file");
    fwrite($fh, print_r($obj,true));
    
    fclose($fh);*/
//fwrite($fh, print_r($buyer_email,true));
///
    mysql_connect(DBHOST, DBUSER, DBPASSWORD) or die(mysql_error());
    mysql_select_db(DBNAME) or die(mysql_error());
    $result=mysql_query("SELECT * from emma_details ed LEFT JOIN emma_group_details gd on ed.shop_id=gd.shop_id where ed.shop_id='".$_GET['shop_id']."'");
    $emma_details=(mysql_fetch_array($result));
    $emma=new EMMAAPI($emma_details['emma_api_key'], $emma_details['emma_username'],$emma_details['emma_password']);     
    //echo "<pre>".print_r($emma_details,true)."</pre>";
$subscribed_email=array();    
if($accepts_marketing)
{    
        $subscribed_email[]=array(
            'email'=>$buyer_email
        );        
    $groups=json_decode($emma_details['groups']);
    foreach($groups as $val)
    {

        $group_ids[]=(integer)($val);
    }        

    $response=$emma->import_member_list($subscribed_email, 'emma_sync_import_shopify', 1, $group_ids);
    /*$myFile = "testFile.txt";
    $fh = fopen($myFile, 'w') or die("can't open file");
    fwrite($fh, print_r($subscribed_email,true));
    fwrite($fh, print_r($emma_details,true));
    fclose($fh);*/
}
else
{
   $emma_user_details=$emma->get_member_detail_by_email($buyer_email);
   if($emma_user_details)
   {
        $emma->remove_member_from_all_groups($emma_user_details->member_id);   
       /* $myFile = "testFile.txt";
        $fh = fopen($myFile, 'w') or die("can't open file");   
        fwrite($fh, print_r($emma_user_details,true));
        fclose($fh);*/
   }
}