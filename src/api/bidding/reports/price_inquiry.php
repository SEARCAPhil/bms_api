<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../auth/Session.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../bidding/Invitations.php');
require_once('../../../auth/Session.php');
require_once('../../../suppliers/Accounts/Accounts.php');


include_once(dirname(__FILE__).'/../../../../vendor/dompdf/lib/html5lib/Parser.php');
include_once(dirname(__FILE__).'/../../../../vendor/dompdf/src/Autoloader.php');

Dompdf\Autoloader::register();

use Dompdf\Dompdf;
use Bidding\Index as Bid;
use Bidding\Particulars as Particulars;
use Bidding\Requirements as Requirements;
use Bidding\Invitations as Invitations;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;
use Suppliers\Accounts as Suppliers;



$clean_str=new CleanStr();
$Ses = new Session($DB);
$Part = new Particulars($DB);
$Req = new Requirements($DB);
$Inv = new Invitations($DB);
$Supp = new Suppliers($DB);

// ID
if (!isset($_GET['id'])) exit;
$id = (int) htmlentities(htmlspecialchars($_GET['id']));

// token 
if(!isset($_GET['token']))  exit;

// get privilege
// this is IMPORTANT for checking privilege
$token=htmlentities(htmlspecialchars($_GET['token']));
$current_session = $Ses->get($token);
if(!@$current_session[0]->token) exit;


// suppliers information
$suppliers_info = $Supp->view($current_session[0]->account_id);
if (!isset($suppliers_info[0])) exit;
$company_name = strtoupper($suppliers_info[0]->company);

// invitation details
$inv_details = $Inv->view($id);

// non emty invitation
if (!isset($inv_details[0])) exit;


// get all requirements details
$details = $Req->view($inv_details[0]->bidding_requirements_id);

$d = new DateTime($details[0]->date_created);
$date = $d->format('Y-m-d');

if (!$details[0]) exit;

// items
$items = ($Inv->lists_all_received_per_bidding($current_session[0]->company_id,$details[0]->bidding_id));
if (!isset($items[0])) exit;


$tr_specs = '';
foreach ($items as $key => $value) {
	$tr_specs.="
			<tr>
				<td style='width:200px;'>{$value->name}</td>
				<td>{$value->quantity} {$value->unit}</td>
			</tr>
		";
}





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
    p:last-child { page-break-after: never; }*/
    main.page{
    	margin-top:10px;
    	width:100%;
    	height:90%;
    }
    .text-center{
    	text-align:center;
    }
  </style>
  <style>
	.ledger-table {
		cellspacing:0px;
		cellpadding:0px;
		border-collapse:collapse;
		font-size:13px;
		margin-left:20px;
		width:90%;
	}
	.ledger-table td{
		border:1px solid #ccc;	
	}
	.ledger-table th, .ledger-table td{
		border:1px solid #ccc;	
		padding-left:15px;
	}
	.ledger-table tbody td {
		height:50px;
	}
	.breaker {
		text-align:left;
		margin-left:25px;
	}
	.no-border{
		border-bottom:1px solid #fff !important;
	}
</style>
</head>
<body>
  <!--<header>


  </header>-->
  <!--<footer>Page /</footer>-->
  <main class='page'>

  		<article class='text-center'>
	  	  	<p>SOUTHEAST ASIAN REGIONAL CENTER FOR GRADUATE STUDY 
			  	<br/>AND RESEARCH IN AGRICULTURE
			  	<br/>College, Laguna, 4031, Philippines
			  	<br/>
		  	</p><br/>


		 </article>


		   	<article>
		  		<section style='width:100%;height:20px;'>
		  			<div style='float:left;width:50px;margin-left:440px;'>
		  				Date :
		  			</div>
		  			
		  			<div style='float:left;width:150px;border-bottom:1px solid #ccc;text-align:center;'>{$date}</div>
		  		</section>
		  	</article>

		  	<article>
	  	  		<div style='float:left;width:290px;'> </div>
		  		<div style='float:left;'>
		  				Reference :
		  		</div>

		  		<div style='float:left;width:155px;border-bottom:1px solid #ccc;text-align:center;'> <b>{$id}</b></div>
		  		<br/><br/>
			 </article>



		  	<article class='text-center'>
	  	  		<h3>PRICE INQUIRY</h3><br/>
			 </article>



	 	<section style='height:200px;'>
		  	<div style='float:left;text-align:left;width:30%;'>
		  		To: ${company_name} <br/>
		  		<b>Deadline: {$details[0]->deadline}</b> 

		  	</div>


			<div style='float:left;text-align:justify;width:65%;font-size:13px;'>
				<small>
		  			<p>Gentleman : We solicit your lowest price, fastest delivery, and payment terms for the item/s specified below and in strict 
		  				accordance with the specification/s given. Any deviation in the specification/s must be covered by your detailed description
		  				of the substitution offered. Your price should be quoted properly.<br/>
		  			</p>
		  			<p>
		  				It must be clearly understood that this is not an order but is an inquiry by us and that therefore there will be no contract between us 
		  				unless and until any quotation so given by you is accepted by us in writing. We will be entitled to accept any or all portion  of the 
		  				said quotation which must remain open for acceptance within the period mentioned below.
		  			</p>
		  		</small>
		  		<br/>

		  	</div>
		</section>




		<div>
			<p style='font-size:13px;'>Quotation must be received on or before________________. Please submit your quotation on this form and furnish complete information as called for below.
				If you should have any further question regarding this inquiry, please communicate with __________________________.
			</p>

	  	</div>
	  	

	  	 <article>

			 <p class='breaker'>
			 	&nbsp;&nbsp;&nbsp;
			 </p>
		</article>

	  	<br/>

	  	 <article class='text-center'  style='float:left;text-align:center;width:100%;'>
	  	 	<table class='ledger-table'>
	  	 		<tr>
	  	 			<td style='width:200px;'><b>Item(s)</b> </td>
	  	 			<td style='width:200px;'><b>Quantity</b>  </td>
	  	 		</tr>
	  	 		{$tr_specs}
	  	 	</table>
	  	 </article>

	  


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
$dompdf->stream("price-inquiry.pdf", array("Attachment" => false));

exit(0);

?>