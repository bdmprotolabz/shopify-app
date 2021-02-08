<?php 

include_once '../include/config.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
//include "includes/mail.php";
$service= $_GET["service"];

if($service=='checkorder' && $_POST)
{
    
        extract($_POST);
        $GetOrder_info="SELECT msi.merchant_domain,msi.merchant_shop_id,gpo.protection_id,gpo.order_number,gpo.customer_email,gpo.order_total_price,gpo.order_total_Amount_Insured,gpo.claimed_status FROM merchant_details as msi,guide_protection_orders as gpo WHERE gpo.merchant_domain=msi.merchant_domain AND msi.merchant_shop_id='".$_POST['shop_id']."' AND gpo.order_number='".$_POST['order_no']."' AND gpo.customer_email='".$_POST['email']."'AND gpo.order_total_Amount_Insured !=0";
        $order_detail=mysqli_query($con,$GetOrder_info);
        if(mysqli_num_rows($order_detail)>0)
        {

           $order_detail = mysqli_fetch_assoc($order_detail);
           if($order_detail['claimed_status']=='Claimed' || $order_detail['claimed_status']=='In process' || $order_detail['claimed_status']=='Rejected')
           {
              $response['status']=3;
              $response['message']="Claim already submitted";
           }
           else
           {
           $update_claim_status="UPDATE `guide_protection_orders` SET `claimed_status` = 'Claimed' Where protection_id = '".$order_detail['protection_id']."'";
                 mysqli_query($con,$update_claim_status);
           $insert_claim_detail="INSERT INTO claim_orders (protection_id,customer_name,customer_phone,claim_reason,claim_desc,order_number ,customer_email, order_total_Amount_Insured,merchant_id,merchant_domain,claimed_at,guide_note)
                  VALUES('".$order_detail['protection_id']."','".$first_name." ".$last_name."','".$phone."','".$reason."','".$claim_desc."','".$order_detail['order_number']."','".$order_detail['customer_email']."','".$order_detail['order_total_Amount_Insured']."','".$order_detail['merchant_shop_id']."','".$order_detail['merchant_domain']."','".date("Y-m-d h:i:s")."','')";
                  mysqli_query($con,$insert_claim_detail);     
             $response['redirect']="https://".$order_detail['merchant_domain']; 

          $response['status']=1;
         $to = $order_detail['customer_email'];
         $orderNumber = $order_detail['order_number'];
         $name = $first_name;
         $domain = $order_detail['merchant_domain'];
       // send grid mail 
             require '../include/sendgrid-php/vendor/autoload.php';
              $email = new \SendGrid\Mail\Mail();
              //customer email
              $email->setFrom("admin@guideprotection.com", "Guide Protection");
              $email->setSubject("Recived your claim");
              $email->addTo($to, $name);
              $email->addContent(
                  "text/html", "Hi <strong>".$name.",</strong><br><p>Not to worry, you made the right move by protecting your package with Guide Shipping Protection.<p><p>We're on it! One of our Guide reps will get back you in under 24 hours, prepared to resolve any issue with your package.<br>We look forward to resolving your claim and showing you how Guide is transforming shipping protection.</p>"
              );
              $sendgrid = new \SendGrid('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');
              try {
                  $mail_response = $sendgrid->send($email);
              } catch (Exception $e) {
                  echo 'Caught exception: '. $e->getMessage() ."\n";
              }
            // mail to admin
              require '../include/sendgrid-php/vendor/autoload.php';
              $email = new \SendGrid\Mail\Mail();
              $email->setFrom("admin@guideprotection.com", "Guide Protection");
              $email->setSubject("Someone claim on an order");
              $email->addTo('admin@guideprotection.com', 'Guide team');
              $email->addContent(
                  "text/html", "Hi <strong>Guide team,</strong><br><p>Team you have recived request a claim against the order id : <strong>".$orderNumber."</strong>. From store <strong>".$domain."</strong> and customer email id is : <strong>".$to."</strong><p>"
              );
              $sendgrid = new \SendGrid('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');
              try {
                  $mail_response = $sendgrid->send($email);
              } catch (Exception $e) {
                  echo 'Caught exception: '. $e->getMessage() ."\n";
              }
            }
        }
        else
        {
            $response['status']=0;
            
        }
       
        echo json_encode($response);
}

?>