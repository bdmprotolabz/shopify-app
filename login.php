    
    <?php
        ini_set("display_errors", 1);
        error_reporting(E_ALL);
        ob_start();
        session_start();
        require_once('include/config.php');
        require_once('include/shopify.php');
    if(isset($_GET['code']))
    {
       $shopifyClient = new ShopifyClient($_GET['shop'], "", SHOPIFY_API_KEY, SHOPIFY_SECRET);

        //session_unset();
        
        // Now, request the token and store it in your session.
        $_SESSION['token'] = $shopifyClient->getAccessToken($_GET['code']);
        $accessToken=$_SESSION['token'];
        $domain=$_GET['shop'];
        $shopURL= $_GET['shop'];
        if ($_SESSION['token'] != '')
            $_SESSION['shop'] = $_GET['shop'];
        $shopURL='https://'.$_GET['shop']; 

        //get shop id
        $shopifyClient = new ShopifyClient($domain,$accessToken, SHOPIFY_API_KEY, SHOPIFY_SECRET);  
        

        //create recurring charges
          $create_recuring_application = array(
        "recurring_application_charge" => array(
        "name" => "Guide protection",
        "price" => "0.00",
        "return_url" => base_url."install.php?".$_SERVER['QUERY_STRING'],
        //$shopURL.'/admin/apps/eleos-initiative-2/?'.$_SERVER['QUERY_STRING'],
        "capped_amount" => 10000,
        "terms" => "charges based on sold guide protection",
        "test"=> true
        ));
           $recuring_response=$shopifyClient->call('POST',"/admin/api/2020-07/recurring_application_charges.json",$create_recuring_application);
        
        echo '<script>window.top.location.href="'.$recuring_response['confirmation_url'].'";</script>';
    }
    else if (isset($_POST['shop']) || isset($_GET['shop'])) 
    {
       $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
        // get the URL to the current page
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        header("Location: " . $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, $pageURL));
    }
    else
    {
    ?>
    <p>Install this app in a shop to get access to its private admin data.</p> 
    <p style="padding-bottom: 1em;">
        <span class="hint">Don&rsquo;t have a shop to install your app in handy? <a href="https://app.shopify.com/services/partners/api_clients/test_shops">Create a test shop.</a></span>
    </p> 
    <form action="login.php" method="post">
      <label for='shop'><strong>The URL of the Shop</strong> 
        <span class="hint">(enter it exactly like this: myshop.myshopify.com)</span> 
      </label> 
      <p> 
        <input id="shop" name="shop" size="45" type="text" value="" /> 
        <input name="commit" type="submit" value="Install" /> 
      </p> 
    </form>
    <style>
    body {
    text-align: center;
    margin-top: 10%;
    background-image: url("https://mdbootstrap.com/img/Photos/Horizontal/Nature/full%20page/img(11).jpg");
    </style>
<?php } ?>