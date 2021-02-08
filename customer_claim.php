<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
ob_start();
session_start();
require_once('../include/config.php');
if(isset($_GET['id']) && $_GET['id'] != "" )
{
   $verifyShop = $query="SELECT * FROM merchant_details WHERE merchant_shop_id='".$_GET['id']."'";
   $verifyShop=mysqli_query($con,$verifyShop);
      if(mysqli_num_rows($verifyShop)>0)
      {
       

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>Guide Protection Claim</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/main.css" rel="stylesheet" media="all">
    <style>
    .custom-style {
line-height: 50px;

-moz-box-shadow: inset 0px 1px 3px 0px rgba(0, 0, 0, 0.08);

-moz-border-radius: 5px;

padding: 0 20px;
font-size: 16px;
color: #666;
-webkit-transition: all 0.4s ease;
-o-transition: all 0.4s ease;
-moz-transition: all 0.4s ease;
transition: all 0.4s ease;
}
.required {
  color: red;
}
p.message.custom-style {
    line-height: 20px !important;
}
    </style>
</head>

<body>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                    <h2 class="title">Guide protection claim request</h2>
                    <form method="POST" id="request_claim" name="request_claim">
                        <input type="hidden" id="shop_id" name="shop_id" value="<?=$_GET['id']?>"/>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">first name</label>
                                    <input class="input--style-4" type="text" name="first_name">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">last name</label>
                                    <input class="input--style-4" type="text" name="last_name">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Email<span class="required">*</span></label>
                                    <input class="input--style-4" type="email" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Order no<span class="required">*</span></label>
                                    <input class="input--style-4" type="text" id="order_no" name="order_no">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Claim reason<span class="required">*</span></label>
                                    <div class="rs-select2 js-select-simple select--no-search">
                                    <select name="reason" id="reason">
                                    <option disabled="disabled" value="-1" selected="selected">Choose option</option>
                                    <option value="stolen">Stolen</option>
                                    <option value="lost">Lost</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                                <div class="select-dropdown"></div>
                            </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Phone Number</label>
                                    <input class="input--style-4" type="text" name="phone">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                        <div class="col-12">
                        <div class="input-group">
                            <label class="label">Description</label>   
                            <textarea class="input--style-4" name="claim_desc" cols="55" rows="3" id="claim_desc"></textarea>
                        </div>
                        </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                        <div class="p-t-15">
                            <button class="btn btn--radius-2 bg-gra-02" type="submit" id="send_claimform">Submit</button>
                        </div>
                            </div>
                        <div class="col-2">
                            <div class="p-t-15">
                             <p class="message custom-style"></p>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="js/global.js"></script>
    <script src="js/custom.js"></script>

</body>

</html>
<!-- end document-->
<?php
      }
      else
      {
        echo '<p><center>You not authorised to claim the order</center></p>';
      }
}
?>