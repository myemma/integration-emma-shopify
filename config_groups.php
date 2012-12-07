<?php
  /*
    Shopify PHP API Login
    
    Step 1: Create a new session.
    Step 2: If the there is no token, redirect to the application permission URL    
    Step 3: Once the application is installed and verified and no errors are returned set session params and redirect to index
  */

  /* Sessions */
  session_id();
  session_start();
	include('lib/shopify_api.php');
    include('lib/EMMAAPI.class.php');
    mysql_connect(DBHOST, DBUSER, DBPASSWORD) or die(mysql_error());
    mysql_select_db(DBNAME) or die(mysql_error());
    mysql_query("CREATE TABLE IF NOT EXISTS `emma_group_details` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `shop_id` varchar(99) NOT NULL,
      `groups` varchar(99999) NOT NULL,
      `sync_existing` varchar(9999) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `shop_id` (`shop_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");    
    $result=mysql_query("SELECT * from emma_details ed LEFT JOIN emma_group_details gd on ed.shop_id=gd.shop_id where ed.shop_id='".$_SESSION['shop_id']."'");
    $emma_details=(mysql_fetch_array($result));
    $emma=new EMMAAPI(trim($emma_details['emma_api_key']), trim($emma_details['emma_username']),trim($emma_details['emma_password']));     
   // echo "<pre>".print_r($emma_details,true)."</pre>";
    $url = (isset($_SESSION['shop'])) ? mysql_escape_string($_SESSION['shop']) : '';
    $token = (isset($_SESSION['token'])) ? mysql_escape_string($_SESSION['token']) : '';  
    $api = new Session($url, $token, API_KEY, SECRET);    
    if($_POST)
    {
        $emma_details['sync_existing']=$sync_existing=(isset($_POST['sync_existing_users'])&& $_POST['sync_existing_users'])?1:'';
        $emma_details['groups']=$groups=json_encode($_POST['emma_groups']);
        mysql_query("INSERT INTO emma_group_details 
        (shop_id,groups,sync_existing) VALUES(".$_SESSION['shop_id'].",'".$groups."','') 
        ON DUPLICATE KEY UPDATE 
        groups = '".$groups."',
        sync_existing = '".$sync_existing."'
        ")   ;    
        if($sync_existing)
        {
            $customers=$api->customers->get();
            $subscribed_emails=array();
            foreach($customers['customer'] as $key=> $customer)
            {
                if($customer['accepts-marketing'])
                {
                    $subscribed_emails[]=array(
                        'email'=>$customer['email']
                    );                
                }    
            }
            foreach($_POST['emma_groups'] as $val)
            {
            
                $group_ids[]=(integer)($val);
            }        
            
            $response=$emma->import_member_list($subscribed_emails, 'emma_sync_import_shopify', 1, $group_ids); 
        }
       //echo "<pre>".print_r($customers,true)."</pre>";
    }    
    if ($api->valid()){
        $response=($api->shop->get());
        if(!isset($response['errors']))
        {
            $decoded_groups_list=json_decode($emma_details['groups']);
            $groups=array();
           $groups=$emma->list_groups('g,t');
?>
<!doctype html>
<html lang="en-us" dir="ltr">
  <head>
  <meta charset="utf-8">
    <title>Configure&nbsp;Groups</title>
    
    <link href="css/uni-form.css" media="screen" rel="stylesheet"/>
    <link href="css/default.uni-form.css" title="Default Style" media="screen" rel="stylesheet"/>
    <link href="css/demo.css" media="screen" rel="stylesheet"/>
    <link href="css/css.css" media="screen" rel="stylesheet" type="text/css" /> 
    <link href="css/emma-shopify.css" media="screen" rel="stylesheet" type="text/css" /> 
    <!--[if lte ie 7]>
      <style type="text/css" media="screen">
        /* Move these to your IE6/7 specific stylesheet if possible */
        .uniForm, .uniForm fieldset, .uniForm .ctrlHolder, .uniForm .formHint, .uniForm .buttonHolder, .uniForm .ctrlHolder ul{ zoom:1; }
      </style>
    <![endif]-->

  </head>
  <body>
  <div id="" class="clearfix uniForm"> 
        <div id="tabsB">
            <ul>
            <li><a href="index.php"><span>Home</span></a></li>
            <li id="current"><a href="config_groups.php" id="current"><span>Configure Groups</span></a></li>
            <li><a href="logout.php"><span>Logout</span></a></li>

            </ul>
        </div>  
          <h1><a href=""><img src="img/emma-logo.png" alt=""/></a></h1>
        <form action="#" method="post" class="uniForm">
        <?php if($_POST){?>
            <div id="okMsg">
                <p>
                  You have successfully configured the groups !!!
                </p>
             </div> 
        <?php }?>        
      <fieldset>
        <h3>Configure Groups</h3>
        
        <div class="ctrlHolder" id="emma_groups">
          <p class="label"><strong>Emma Groups<br></strong></p>
          <ul id="emma_groups">
          <?php if($groups){
            foreach($groups as $group){
                $checked="";
                if(in_array($group->member_group_id ,$decoded_groups_list))
                {
                    $checked="checked";
                }
                ?>
                <li><label for="emma_groups_<?php echo  $group->member_group_id ?>"><input type="checkbox" name="emma_groups[]" <?php echo $checked?> id="emma_groups_<?php echo  $group->member_group_id ?>" class="color_selection" value="<?php echo  $group->member_group_id ?>"><b> &nbsp;<?php echo  $group->group_name ?></b></label></li>
            <?php }
            }else{?>
             Sorry,No groups found
            <?php }?>
          </ul>
          <p class="formHint">When an order is completed at your Shopify store, subscribed customers will be automatically added to the Emma Group(s) you select.</p>
        </div>
        <div class="ctrlHolder">
          <ul class="blockLabels">
            <li><label for=""><input type="checkbox" name="sync_existing_users" <?php echo $emma_details['sync_existing']?'checked ':''?>id="sync_existing_users" value="1"><b>&nbsp;Migrate Existing Users</b></label></li>
          </ul>
        </div>   
        
      </fieldset>
          
          <div class="buttonHolder">
            <button type="submit" class="primaryAction">Submit</button>
          </div>

        </form>
    </div>

    <script type="text/javascript" src="js/jquery.min.js" ></script>
    <script type="text/javascript" src="js/uni-form-validation.jquery.js" charset="utf-8"></script>
    <script>
      $(function(){
        $('form.uniForm').uniform({
          prevent_submit : true
        });
      });
    </script>
  </body>
</html>
<?php
    }
    else
    {
        header("Location: login.php");
    }
}

?>
