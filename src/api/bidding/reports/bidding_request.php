<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../Auth/Session.php');
require_once('../../../bidding/Requirements/Requirements.php');


include_once(dirname(__FILE__).'/../../../../vendor/dompdf/lib/html5lib/Parser.php');
include_once(dirname(__FILE__).'/../../../../vendor/dompdf/src/Autoloader.php');

Dompdf\Autoloader::register();

use Dompdf\Dompdf;
use Bidding\Index as Bid;
use Bidding\Particulars as Particulars;
use Bidding\Requirements as Requirements;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;


$clean_str=new CleanStr();
$logs = new Logs($DB);
$Part = new Particulars($DB);
$Ses = new Session($DB);
$Index = new Bid($DB);
$Req= new Requirements($DB);

if (!isset($_GET['id'])) exit;

$id = htmlentities(htmlspecialchars($_GET['id']));

$data = $Index->view($id);

if (!$data[0]) {
	exit;
}

$name = strtoupper($data[0]->profile_name);
$date = date('m-d-Y');
$bidding_name = strtoupper($data[0]->name);

$parts = $Part->lists_by_parent($data[0]->id, true);

// Particulars
$partsSec = '';
$letters = 'A';

$funds_per_particulars = [];
$funds_per_particulars['total_amount'] = [];

for($x = 0; $x < count($parts); $x++ ) {
	$partName = strtoupper($parts[$x]->name);
	$req_details = ($Req->lists_by_parent($parts[$x]->id));

	// add to list
	$funds_per_particulars[$parts[$x]->name] = [];
	$funds_per_particulars[$parts[$x]->name]['funds'] = [];
	$funds_per_particulars[$parts[$x]->name]['requirements'] = [];
	$funds_per_particulars[$parts[$x]->name]['amount'] = [];
	

	$partsSec.= "  		

  		<section style='text-align:left;margin-left:30px;'>
  			<br/><br/><br/><br/>
  			<p>
	  			<b>{$letters}. {$partName}</b>
	  		</p>";

	  	// requirements
	  	if (isset($req_details[0])) {

	  		
	  		$funds_per_particulars[$parts[$x]->name]['requirements'][] = $req_details;

	  		$req_count = 0;

	  		for ($z = 0; $z < count($req_details); $z++) {
	  			// amount per Currency
	  			if (! isset($funds_per_particulars[$parts[$x]->name]['amount'][$req_details[$z]->budget_currency])) $funds_per_particulars[$parts[$x]->name]['amount'][$req_details[$z]->budget_currency] = 0;
	  			// funds
	  			$funds_per_particulars[$parts[$x]->name]['funds'][] = $req_details[$z]->funds;
	  			// total amount per particular
	  			$funds_per_particulars[$parts[$x]->name]['amount'][$req_details[$z]->budget_currency] += $req_details[$z]->budget_amount;


	  			// total amount 
	  			if (!isset($funds_per_particulars['total_amount'][$req_details[$z]->budget_currency])) $funds_per_particulars['total_amount'][$req_details[$z]->budget_currency] = 0;
	  			$funds_per_particulars['total_amount'][$req_details[$z]->budget_currency] += $req_details[$z]->budget_amount;


	  			$req_count ++;

	  			$partsSec.= " <div style='text-align:left;margin-left:25px;'>
			  		<p>
			  			<b>{$req_count}) {$req_details[$z]->name}</b>
			  		</p>";
			  		if ($req_details[$z]->specs) {

			  			for ($y = 0; $y < count($req_details[$z]->specs); $y++ ) {
			  				$partsSec.= " <p>
								&nbsp;&nbsp;&nbsp;&nbsp;{$req_details[$z]->specs[$y]->name} - {$req_details[$z]->specs[$y]->value} <br/>
					  		</p>";
			  			}

				  	}

			  	$partsSec.= " </div>";
	  		}

		 }
	 
	 $partsSec.= " </section>";
	 $letters ++;
}

//var_dump($data[0]);
$table= "
	<div align='center'>
<style>
	.ledger-table {
		cellspacing:0px;
		cellpadding:0px;
		border-collapse:collapse;
		font-size:14px;
		margin-left:20px;
		width:90%;
	}
	.ledger-table td{
		border:1px solid #ccc;	
	}
	.ledger-table th, .ledger-table td{
		border:1px solid #ccc;	
		padding:4px;
		text-align:center;
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
<br/>
<br/><br/>
<br/>


<table class='ledger-table'> 
	<thead>
		<tr>
			<th width='260px;'>Particulars</th>
			<th width='90px;'>Date Needed</th>
			<th  width='200px;'>Source of Fund</th>
			<th  width='110px;'>Estimated Cost</th>
		</tr>
	</thead>
	<tbody>";

	$class ='no-border';


	for($x = 0; $x < count($parts); $x++ ) {
		//$amount = number_format($funds_per_particulars[$parts[$x]->name]['amount'], 2, ',', ',');
		$class = '';
		// exclude last item
		if($x+1 < count($parts)) {
			$class = 'no-border';
		}

		$table .="<tr>
				<td class='{$class}'>
					{$parts[$x]->name}
				</td>
				<td class='{$class}'>
					{$parts[$x]->deadline}
				</td>
				<td class='{$class}'>";
				for($f = 0; $f < count($funds_per_particulars[$parts[$x]->name]['funds']); $f++ ) {
				
					$table .= "{$funds_per_particulars[$parts[$x]->name]['funds'][$f][0]->fund_type} - {$funds_per_particulars[$parts[$x]->name]['funds'][$f][0]->cost_center} - {$funds_per_particulars[$parts[$x]->name]['funds'][$f][0]->line_item}<br/>";
				}

		$table .="</td><td class='{$class}'>";

				foreach ($funds_per_particulars[$parts[$x]->name]['amount'] as $key => $value) {
					$amount = number_format($value, 2, ',', ',');
					$table .="<p><b>{$key} {$amount}</b></p>";
				}


			$table .="</td></tr>";
	}

		if (count($parts) < 5) {
			
			for($ex = 0; $ex < (5 - count($parts)); $ex++) {
				$class = 'no-border';

				if ($ex+1 == (5 - count($parts))) {
					$class = '';
				}
				$table .="<tr>
					<td class='{$class}'></td><td class='{$class}'></td><td class='{$class}'></td><td class='{$class}'></td>";
			}
		}
		// no data





$table .="			<tr>
			<td colspan='3'  style='height:50px;'></td>
			<td>";
				foreach ($funds_per_particulars['total_amount'] as $key => $value) {
					$amount = number_format($value, 2, ',', ',');
					$table .="<p><b>{$key} {$amount}</b></p>";	
				}
$table .="	</td>
		</tr>
	</tbody>
 <tbody>
 	
 </tbody>
 </table>
 <div style='text-align:left;margin-left:25px;'>
	 <p style='text-align:left;'>
	 	* Funds to be transferred to concerned Office/Department/Unit upon procurement of items
	 </p>

	<br/>
	 <span width='400px' style='float:left;'>
	 	Requested by:
	 </span>

	 <span width='400px' style='float:left;margin-left:350px;'>
	 	Certified Funds Available
	 </span>


	  <br/><br/>
	 <div style='float:left;text-align:center; height:40px;width:300px;'>
	 	<u><b>{$name}</b></u><br/>
	 	{$data[0]->position}
	 </div>

 	<div style='float:left;text-align:center; height:40px;width:300px;margin-left:100px;'>
	 	<u><b style='text-transform:uppercase;'>{$data[0]->certified_by}</b></u><br/>
	 	{$data[0]->certified_by_position}
	</div>

	 <p class='breaker'>
	 	&nbsp;&nbsp;&nbsp;
	 </p>

	 <span width='400px' style='float:left;'><br/>
	 	Recommending Approval :
	 </span>

	 <span width='400px' style='float:left;margin-left:350px;'><br/>
	 	Approved :
	 </span>

	 <p class='breaker'>
	 	&nbsp;&nbsp;&nbsp;
	 </p>

	
	<div style='float:left;text-align:center; height:100px;width:300px;'>
	 	<u> <b style='text-transform:uppercase;'>{$data[0]->recommended_by}</b></u><br/>
	 	{$data[0]->recommended_by_position}
	 </div>

 	<div style='float:left;text-align:center; height:100px;width:300px;margin-left:100px;'>
	 	<u><b style='text-transform:uppercase;'>{$data[0]->approved_by}</b></u><br/>
	 {$data[0]->approved_by_position}
	</div>



	 


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
</head>
<body>
  <!--<header>


  </header>-->
  <!--<footer>Page /</footer>-->
  <main class='page'>
  	<article>
  		<section style='width:100%;height:20px;'>
  			<div style='float:left;width:120px;margin-left:440px;'>
  				CBA Control No.
  			</div>
  			<div style='float:left;width:150px;border-bottom:1px solid #ccc;text-align:center;'>&nbsp;&nbsp;<b>#{$data[0]->id}</b></div>
  			  		</section>
  	</article>

  		<article class='text-center'>
	  	  	<p>SOUTHEAST ASIAN REGIONAL CENTER FOR GRADUATE STUDY 
			  	<br/>AND RESEARCH IN AGRICULTURE
			  	<br/>College, Laguna, 4031, Philippines
			  	<br/>
		  	</p><br/>
		  	<h3>BIDDING REQUEST</h3><br/>

		 </article>

	 

	  	<div style='float:left;margin-left:27px;text-align:left;width:72%;'>
	  		The Chairman <br/>
	  		SEARA Committee on BIDS and Awards <br/>
	  		College, Laguna

	  	</div>

	  	<div style='float:left;text-align:left;width:20%;'>
	  		<div style='float:left;text-align:center;width:100%;'>
				<div style='text-align:center;'>
					<div style='height:2px;'><p>{$date}</p></div>
					<div style='height:2px;border-bottom:1px solid #ccc;'>Date</div>
				</div>
			</div>

	  	</div>

	  	 <article class='text-center'  style='float:left;text-align:center;width:100%;'>
		<div style='float:left;text-align:center;width:100%;'>

			<div style='margin-left:80%;text-align:center;'>
				<div style='height:2px;'><p>{$date}</p></div>
				<div style='height:2px;border-bottom:1px solid #ccc;'>Date</div>
			</div>
		</div>

		 <p class='breaker'>
		 	&nbsp;&nbsp;&nbsp;
		 </p>
	</article>

	  	<br/>
	  	{$table}


  </main>

    <main class='page'>

  		<center style='padding-top:0px;'>
  			<b><br/>
  				{$bidding_name} <br/>
  				STANDARD MINIMUM SPECIFICATIONS
  			</b>
  		</center>

  		{$partsSec}


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