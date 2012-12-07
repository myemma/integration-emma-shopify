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
	/* GET VARIABLES */
	$url = (isset($_GET['shop'])) ? mysql_escape_string($_GET['shop']) : '';
	$token = (isset($_GET['t'])) ? mysql_escape_string($_GET['t']) : '';	
	$api = new Session($url, $token, API_KEY, SECRET);
	//var_dump($api);
	//if the Shopify connection is valid
	if ($api->valid()){
		if (isEmpty($token)){
			header("Location: " . $api->create_permission_url());
		}else{
		  $shop = $api->shop->get();
		  $_SESSION['shop_id']=$shop['id'];  
		  if (!isset($shop['error'])){
		    $_SESSION['shop'] = $url;
		    $_SESSION['token'] = $token;
		    header("Location: index.php");
		  }
		}
	}else{
?>
<!doctype html>
<html lang="en-us" dir="ltr">
  <head>
  <meta charset="utf-8">
    <title>Login/Install</title>
    
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
          <h1><a href=""><img src="img/emma-logo.png" alt=""/></a></h1>
        <form action="login.php" method="get" class="uniForm">
                
          <fieldset>
          <h3>Login/Install</h3>
          <p>The first time that you login to Emma Shopify App, you will be asked for permission to install it on your Shopify store.</p> 
          
            <div class="ctrlHolder">
              <label for=""><em>*</em>Shopify store URL:</label>
              <input name="shop" id="shop" data-default-value="yourshop.myshopify.com" size="45" type="text" class="textInput required validateUrl"/>
              <p class="formHint">Ex: www.yourshop.com or yourshop.myshopify.com</p>
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
 
