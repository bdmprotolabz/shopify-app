<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);
ob_start();
session_start();
require_once('include/config.php');
require_once('include/shopify.php');
// echo '<pre>';
// echo 'session'.'<br>';
// print_r($_SESSION);

// echo 'get method'.'<br>>';
// print_r($_GET);
$ar= [];
	  $hmac = $_GET['hmac'];
	  $charge_id = $_GET['charge_id'];
	  unset($_GET['charge_id']);
	  unset($_GET['hmac']);
	  foreach($_GET as $key=>$value)
	   {
		    $key=str_replace("%","%25",$key);
		    $key=str_replace("&","%26",$key);
		    $key=str_replace("=","%3D",$key);
		    $value=str_replace("%","%25",$value);
		    $value=str_replace("&","%26",$value);
		    $ar[] = $key."=".$value;
	    }
	   $str = join('&',$ar);
	   $ver_hmac =  hash_hmac('sha256',$str,SHOPIFY_SECRET,false);
	  if($ver_hmac==$hmac)
		{

			$domain = $_SESSION['shop'];
			$token = $_SESSION['token'];
			$shopifyClient = new ShopifyClient($domain,$token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
			if(isset($charge_id) && $charge_id != "" )
			{
				//activate merchant charge id
               	$activate_recurring_application = array(
				"recurring_application_charge" => array(
				"id" => $charge_id,
				"name" => "Guide protection",
				"api_client_id" => rand(1000000, 9999999),
				"price" => "0.00",
				"status" => "accepted",
				"return_url" => $domain."/admin/apps/guide-protection",
				"billing_on" => date('Y-m-d'),
				"test" => true,
				"activated_on" => null,
				"trial_ends_on" => null,
				"cancelled_on" => null,
				"trial_days" => 0,
				"decorated_return_url" => base_url."/subscription.php?charge_id=" . $charge_id
		        ));
		        $recurring_application = $shopifyClient->call('POST',"/admin/api/2019-10/recurring_application_charges/".$charge_id."/activate.json",$activate_recurring_application);
		        //register uninstall webhook
		        $uninstall_webhook = array(
		        "webhook"=>array(
		        "topic"=> "app/uninstalled",
		        "address"=> base_url."webhook/uninstall_app.php",
		        "format"=> "json"
		        ));
		        $webhook_response = $shopifyClient->call('POST',"/admin/api/2020-07/webhooks.json",$uninstall_webhook);
		        
		        //script tag
		        
		        $script_tag=array(
		          "script_tag"=> array(
		            "event"=> "onload",
		            "src"=> base_url."scripts/protection_layout.js"
		          ));
		        $script_tag_response = $shopifyClient->call('POST',"/admin/api/2020-07/script_tags.json",$script_tag);

		        //create webhook for order creation 
		        $create_order=array(
		        "webhook" => array(
		        "topic" => "orders/create",
		        "address" => base_url."webhook/order_creation_webhook.php",
		        "format" => "json"
		        )
		        );
		        $create_order_response=$shopifyClient->call('POST',"/admin/api/2020-07/webhooks.json",$create_order);

		        //fulfilment create webhook 

		        $create_fullfilment = array(
		        "webhook" => array(
		        "topic" => "fulfillments/create",
		        "address" => base_url."webhook/full_filment_update.php",
		        "format" => "json"
		        )
		        );

		        $create_fullfill_response=$shopifyClient->call('POST',"/admin/api/2020-07/webhooks.json",$create_fullfilment);
                
		        //update fullfilment webhook

		        $fullfilment_update=array(
		        "webhook" => array(
		        "topic" => "fulfillments/update",
		        "address" => base_url."webhook/full_filment_update.php",
		        "format" => "json"
		        )
		        );
		        $update_fullfill_response=$shopifyClient->call('POST',"/admin/api/2020-07/webhooks.json",$fullfilment_update);
		        
		        //create product and delete existing product 
		        $query="SELECT product_json FROM protection_rules";
		        $obj = mysqli_query($con,$query);
		        
		        if(mysqli_num_rows($obj)==1)
		        {
		        $ProdutJson = mysqli_fetch_assoc($obj);
		        }
		        $getProduct=$shopifyClient->call('GET',"/admin/api/2020-07/products.json"); 
		        foreach ($getProduct as $key => $value) {
		           if($value['handle']=='guide-protection')
		           {
		            $Prduct_id=$value['id'];
		            break; 
		           }
		        }
		        if(isset($Prduct_id) && $Prduct_id!="")
		        {
		        $deleteProduct=$shopifyClient->call("DELETE","/admin/api/2020-07/products/".$Prduct_id.".json"); 
		        }
		        $create_product=json_decode($ProdutJson['product_json'],true);
		        $create_product_response=$shopifyClient->call('POST',"/admin/api/2020-07/products.json",$create_product);

		        // create smart collection or update existing
		        $get_smart_collections=$shopifyClient->call("GET","/admin/api/2020-07/smart_collections.json"); 
		         
		         foreach ($get_smart_collections as $key => $value) {
		             if($value['handle']=='all')
		             {
		                $collection_id=$value['id'];
		                $collectionRules=$value['rules'];
		                break;
		             }
		         }
		         if(isset($collection_id) && $collection_id != "")
		         {
		            $add_new_rule=array(
		                          "column"=> "type",
		                          "relation"=> "not_equals",
		                          "condition"=> "Guide protection");
		            array_push($collectionRules,$add_new_rule);
		            $updateCollection = array("smart_collection"=> array(
		                  "id" => $collection_id,
		                  "rules"=> $collectionRules
		                ));
		                $update_Collection_response = $shopifyClient->call('PUT',"/admin/api/2020-07/smart_collections/".$collection_id.".json",$updateCollection);
		                //print_r($update_Collection_response);
		         }
		         else
		         {
		            $CreateCollection= array("smart_collection"=> array(
		            "title" => "All",
		            "rules" => array(array(
		              "column" => "type",
		                "relation" => "not_equals",
		                "condition" => "Guide protection"
		                 ))));    
		                   $Create_Collection_response = $shopifyClient->call('POST',"/admin/api/2020-07/smart_collections.json",$CreateCollection);
		                  //  print_r($Create_Collection_response);
		         }
                 
                 //get merchant shop id
                 $shopInfo=$shopifyClient->call('GET',"/admin/api/2020-07/shop.json");

                 //insert data in database
                    $check_shop="SELECT * FROM `merchant_details` WHERE merchant_domain='".$domain."'";
                  //  echo $check_shop;
			        $shop_result=mysqli_query($con,$check_shop);
			        if(mysqli_num_rows($shop_result)>0)
			        {
			           $update_shop_detail="UPDATE `merchant_details` SET `merchant_token` = '".$token."', `installation_date` = '".date("Y-m-d h:i:s")."',`merchant_status`=1,`merchant_charge_id` = '".$charge_id."' Where merchant_domain = '".$domain."'";
			           mysqli_query($con,$update_shop_detail);
			        } 
			        else
			        {
			            $insert_shop_detail="INSERT INTO merchant_details (merchant_id, merchant_domain ,merchant_shop_id, merchant_token,installation_date,merchant_charge_id,merchant_status)
			            VALUES('','".$domain."','".$shopInfo['id']."','".$token."','".date("Y-m-d h:i:s")."','".$charge_id."','1')";
			            mysqli_query($con,$insert_shop_detail);
			        }
		         echo '<script>window.top.location.href="https://'.$domain.'/admin/apps/guide-protection/guide_protection/merchant_instructions";</script>';
			}
		}
		else
		{
		echo 'not verified';
		}

