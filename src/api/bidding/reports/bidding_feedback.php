<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../auth/Session.php');
require_once('../../../bidding/Requirements/Requirements.php');


include_once(dirname(__FILE__).'/../../../../vendor/dompdf/lib/html5lib/Parser.php');
include_once(dirname(__FILE__).'/../../../../vendor/dompdf/src/Autoloader.php');

Dompdf\Autoloader::register();

use Dompdf\Dompdf;
use Bidding\Index as Bid;
use Bidding\Requirements as Requirements;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;


$clean_str=new CleanStr();
$Ses = new Session($DB);
$Index = new Bid($DB);
$Req = new Requirements($DB);

$date = date('M-d-Y');

# read feedback
if (!isset($_GET['id'])) exit;
$id = (int) $_GET['id'];
$feed = ($Req->get_feedback($id));
if (!isset($feed[0])) exit;

# author
if(!isset($feed[0]->author)) $feed[0]->author = [];

#amount
$amount = 0;
if (isset($feed[0]->amount)) {
    $amount = number_format($feed[0]->amount,2,'.',',');
}
#ratings
$ratings = ["price" => 'Price', 'quality' => 'QualityGoods/ Service Quality', 'time' => 'Delivery Time'];

$price = $time = $quality = 4;

foreach($feed[0]->ratings as $key => $value) {
    # price
    if($value->name === 'price')  $price = $value->value;
    # quality
    if($value->name === 'quality')  $quality = $value->value;
    # time
    if($value->name === 'time')  $time = $value->value;

  
}

# Original bidding request data
$data = $Index->view($feed[0]->bidding_id);

if (!$data[0]) {
	exit;
}





$table="
	<div align='center'>
<style>
    .text-center {
        text-align:center;
    }
	.ledger-table, .partsec-table {
		cellspacing:0px;
		cellpadding:0px;
		border-collapse:collapse;
		font-size:14px;
		margin-left:20px;
		width:95%;
    }

    .ledger-table td {
        padding:5px;
    }
    
    .with-border{
        border-bottom:1px solid #1e1e1e;
        text-align:center;
	
	.breaker {
		text-align:left;
		margin-left:25px;
	}
	.no-border{
		border-bottom:1px solid #fff !important;
    }
    
    table.rating-scale {
        border: 1px solid #1e1e1e;
        width:90%;
        padding:10px;
    }

</style>
<br/>
<br/><br/>

<table class='ledger-table'> 
    <tr>
        <td width='350px;'>Requisitioner/End-user/Attendee:</td>
        <td class='with-border'> {$feed[0]->author[0]->profile_name}</td>
    </tr>
    <tr>
        <td width='350px;'>&nbsp;</td>
        <td><center><small>Signature over Printed Name</small></center></td>
    </tr>
    <tr>
        <td width='350px;'>Office/Department/Unit: </td>
        <td class='with-border'>{$feed[0]->author[0]->department}</td>
    </tr>
    <tr>
        <td width='350px;'>Supplier/Service Provider: </td>
        <td class='with-border'>{$feed[0]->company_name}</td>
    </tr>
    <tr>
        <td width='350px;'>Goods/Service Description: </td>
        <td class='with-border'>{$feed[0]->product_name}</td>
    </tr>
    <tr>
        <td width='350px;'>Date of Bidding Request:</td>
        <td class='with-border'>{$feed[0]->deadline }</td>
    </tr>
    <tr>
        <td width='350px;'>Delivery Date/Service Period: </td>
        <td class='with-border'></td>
    </tr>
    <tr>
        <td width='350px;'>Contract Amount:  </td>
        <td class='with-border'>{$feed[0]->currency} {$amount}</td>
    </tr>

 </table>

 <br/><br/>
 <p>
    <small><b>RATING OF PROCURED GOODS/SERVICES</b></small>
</p>


<div style='text-align:left;margin-left:25px;'>
    <table class='rating-scale'>
        <tr>
            <td width='100px'><b>SCALE: </b></td>
            <td width='100px'>1 - Poor</td>
            <td width='100px'>2 – Needs Improvement </td>
            <td  width='150px'>3 – Satisfactory </td>
            <td width='100px'>4 – Excellent </td>
        </tr>
        <tr>
            <td><b>CRITERIA : </b></td>
            <td>Price <u> <b>{$price}</b></u></td>
            <td width='230px'>Goods/Service Quality:<u> <b>{$quality}</b></u></td>
            <td>Delivery Time: <u> </b>{$time}</b></u></td>
        </tr>
    </table>   
</div>

<div style='text-align:left;margin-left:25px;border:1px solid #1e1e1e;margin-top:20px;padding:20px;'>
    <center>
        <small>
            <b class='text-center'>REASONS FOR RATING GIVEN BASED ON OBSERVATIONS/EXPERIENCE WITH
            PURCHASED GOODS/SERVICES
            </b><br/><br/>
            {$feed[0]->feedback}
        </small>
    </center>
</div>
 <div style='text-align:left;margin-left:25px;'>

    <br/><br/>
	 <span width='400px' style='float:left;'>
	 	<b>Noted By:</b>
	 </span>

	 <span width='400px' style='float:left;margin-left:350px;'>
	 	
	 </span>


 	<div style='float:left;text-align:center; height:40px;width:300px;margin-left:100px;'> </div>
	 <p class='breaker'>
	 	&nbsp;&nbsp;&nbsp;
	 </p>
</div>

<div style='fwidth:600px;height:30px;background:#fff;'>
     <span width='400px' style='float:left;text-align:center;margin-left:30px;'>
        {$data[0]->requested_by}
	 </span>
</div>

<div style='float:left;width:600px;height:100px;'>
     <span width='400px' style='float:left;text-align:center;height:20px;border-top:1px solid #1e1e1e;margin-left:30px;'>
	 	<b>Office/Department/Unit Head</b>
	 </span>
</div>
";

$html = "<html>
<head>
  <style>
    @page { margin: 35px 25px;}
    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align:center; }
    header p {
    	font-size:15px;
    }
    footer { position: fixed; bottom: -75px; left: 0px; right: 0px; height: 30px; text-align:center; font-size:12px; }
    /*p { page-break-after: always; }
    p:last-child { page-break-after: never; }*/\
    .first-page {
        margin: 0in;
        color: green;
        height: 100%;
        width: 100%;
        position:absolute;
        page-break-after: always;
    }
    main.page{
    	margin-top:10px;
    	width:100%;
    	height:90%;
    	page-break-before: always;
    }



    .text-center{
    	text-align:center;
    }
  </style>
</head>
<body>

  <main class='first-page'>
  	
  		<article class='text-center'>
	  	  	<p><b>Southeast Asian Regional Center for Graduate Study and Research in Agriculture</b>
			  	<br/>College, Los Baños, Laguna, 4031, Philippines
			  	<br/><br/>
		 </article>

         <article>
            <section style='width:100%;height:60px;'>
                <div style='float:left;width:130px;margin-left:420px;'>
                    <b>CBA Control No. :</b>
                </div>
                <div style='float:left;width:150px;border-bottom:1px solid #ccc;text-align:center;'>&nbsp;&nbsp;<b># {$feed[0]->id}</b></div>
            </section>
        </article>


        <article>
            <section style='width:100%;height:20px;text-align:center;'>
                <b>SEARCA COMMITTEE ON BIDS AND AWARDS FEEDBACK FORM</b>
            </section>     
        </article>


	  	<div style='float:left;margin-left:27px;text-align:left;width:72%;'></div>

	  	<div style='float:left;text-align:left;width:20%;height:0px;'>
	  		<div style='float:left;text-align:center;width:100%;'>
				<div style='text-align:center;'>
					<div style='height:2px;'><p><b>Date:</b> <u>{$date}</u></p></div>
				</div>
			</div>

	  	</div>

	  	{$table}


  </main>

</body>
</html>";

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

//page number
$dompdf->getCanvas()->page_text(280, 800, "Page {PAGE_NUM} of {PAGE_COUNT}", '', 10, array(0,0,0));

// Output the generated PDF to Browser
$dompdf->stream("gasoline.pdf", array("Attachment" => false));

exit(0);

?>