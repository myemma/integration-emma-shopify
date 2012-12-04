<?php
  /* Sessions */
  session_id();
  session_start();  
 include('lib/shopify_api.php');
// Make a MySQL Connection

mysql_connect(DBHOST, DBUSER, DBPASSWORD) or die(mysql_error());
mysql_select_db(DBNAME) or die(mysql_error()); 
 if($_POST)
 {
    mysql_query("CREATE TABLE IF NOT EXISTS `emma_details` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `shop_id` varchar(99) NOT NULL,
      `emma_api_key` varchar(9999) NOT NULL,
      `emma_username` varchar(9999) DEFAULT NULL,
      `emma_password` varchar(9999) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `shop_id` (`shop_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
    mysql_query("INSERT INTO emma_details 
    (shop_id, emma_api_key,emma_username,emma_password) VALUES(".$_POST['shop_id'].",'".$_POST['emma_api_key']."','".$_POST['emma_username']."','".$_POST['emma_password']."') 
    ON DUPLICATE KEY UPDATE 
    emma_api_key = '".trim($_POST['emma_api_key'])."',
    emma_username = '".trim($_POST['emma_username'])."',
    emma_password = '".trim($_POST['emma_password'])."'
    ")   ;  
    header("Location: config_groups.php");
 }
 else
 {
      if (!isset($_SESSION['shop']) || !isset($_SESSION['token']) || (isset($_GET['shop'])&& $_GET['shop']!=$_SESSION['shop'])) 
      {
        $url = (isset($_GET['shop'])) ? mysql_escape_string($_GET['shop']) : '';
        $token = (isset($_GET['t'])) ? mysql_escape_string($_GET['t']) : '';	
        $api = new Session($url, $token, API_KEY, SECRET);
        if ($api->valid()){  
            $_SESSION['shop'] = $url;
            $_SESSION['token'] = $token;      
            $shop_details=$api->shop->get();
            $_SESSION['shop_id']=$shop_details['id'];   
            if(!isset($shop_details['errors']))
            {        
                // Create a MySQL table in the selected database
                mysql_query("CREATE TABLE IF NOT EXISTS `shop_details` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `shop_id` varchar(99) NOT NULL,
                  `shop_url` varchar(9999) NOT NULL,
                  `shop_owner` varchar(9999) DEFAULT NULL,
                  `shop_email` varchar(9999) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `shop_id` (`shop_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;")
                 or die(mysql_error());  
                mysql_query("INSERT INTO shop_details 
                (shop_id, shop_url,shop_owner,shop_email) VALUES(".$shop_details['id'].",'".$_SESSION['shop']."','".$shop_details['shop-owner']."','".$shop_details['email']."') 
                ON DUPLICATE KEY UPDATE 
                shop_url = '".$_SESSION['shop']."',
                shop_owner = '".$shop_details['shop-owner']."',
                shop_email = '".$shop_details['email']."'
                ") 
                or die(mysql_error());   
                if(mysql_insert_id())
                {
                    $current_url="http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
                    $base_url=str_replace("index.php",'',$current_url);  
                    $base_url_noparams=preg_replace('/\\?.*/', '', $base_url);                    
                    $fields=array(
                        "topic" => 'orders/create',
                        "address" => $base_url_noparams.'emma/sync.php?shop_id='.$shop_details['id'],
                        "format" => 'json',
                    );
                    $api->webhook->create($fields);  
                }
            }  
            else    
            {
                header("Location: login.php");
            }           
        }
        else
        {
            header("Location: login.php");
        }    
      }  
      else
      {
        $url = (isset($_SESSION['shop'])) ? mysql_escape_string($_SESSION['shop']) : '';
        $token = (isset($_SESSION['token'])) ? mysql_escape_string($_SESSION['token']) : '';  
        $api = new Session($url, $token, API_KEY, SECRET);
      }
      //echo $_SESSION['shop'];
      $token = $_SESSION['token'];
        $api = new Session($url, $token, API_KEY, SECRET);
        //var_dump($api);
        //if the Shopify connection is valid
        //echo $api->valid();
        if ($api->valid()){
            $response=($api->shop->get());
            $result=mysql_query("SELECT * from emma_details where shop_id='".$_SESSION['shop_id']."'");
            $emma_details=(mysql_fetch_array($result));
            $emma_api_key=$emma_username=$emma_password='';
            //var_dump($emma_details);
            if($emma_details)
            {
                $emma_api_key=$emma_details['emma_api_key']?$emma_details['emma_api_key']:'';
                $emma_username=$emma_details['emma_username']?$emma_details['emma_username']:'';
                $emma_password=$emma_details['emma_password']?$emma_details['emma_password']:'';
            }
           
            if(!isset($response['errors']))
            {
    ?>
    <!doctype html>
    <html lang="en-us" dir="ltr">
      <head>
      <meta charset="utf-8">
        <title>Emma Settings</title>
        
        <link href="css/uni-form.css" media="screen" rel="stylesheet"/>
        <link href="css/default.uni-form.css" title="Default Style" media="screen" rel="stylesheet"/>
        <link href="css/demo.css" media="screen" rel="stylesheet"/>
        <link href="css/css.css" media="screen" rel="stylesheet" type="text/css" /> 
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
              <li id="current"><a href="index.php"><span>Home</span></a></li>
              <li><a href="config_groups.php" id="current"><span>Configure Groups</span></a></li>
              <li><a href="logout.php"><span>Logout</span></a></li>

                                </ul>
                        </div>
              <h1><a href=""><img src="img/emma_logo.jpeg" alt=""/></a></h1>

            <form action="#" method="post" class="uniForm">
              <fieldset>
                <h3>Emma API Details</h3>
                <div class="ctrlHolder">
                  <label for=""><em>*</em>Account ID</label>
                  <input name="emma_api_key" id="emma_api_key" data-default-value="API Key" size="35" maxlength="50" type="text" class="textInput required" value="<?php echo $emma_api_key ?>"/>
                  <p class="formHint">The API key for your Emma account.<br>Get a valid API key from your <a href="https://app.e2ma.net/app2/billing/settings/">Emma API Dashboard</a></p>
                </div>
                
                <div class="ctrlHolder">
                  <label for=""><em>*</em>Public API Key</label>
                  <input name="emma_username" id="emma_username" data-default-value="Username" size="35" maxlength="50" type="text" class="textInput required" value="<?php echo $emma_username ?>"/>
                  <p class="formHint">Username/Public Key for your Emma API. <br>Generate Emma Username from your <a href="https://app.e2ma.net/app2/billing/settings/">Emma API Dashboard</a></p>
                </div>
              
                <div class="ctrlHolder">
                  <label for=""><em>*</em>Private API Key</label>
                  <input name="emma_password" id="emma_password" data-default-value="" size="35" maxlength="50" type="password" class="textInput required" value="<?php echo $emma_password ?>" />
                  <p class="formHint">Password/Private Key for your Emma API. <br>Generate Emma Password from your <a href="https://app.e2ma.net/app2/billing/settings/">Emma API Dashboard</a></p>
                </div>
                <input type="hidden" name="shop_id" value="<?php echo $_SESSION['shop_id']?>">

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
}      
?>