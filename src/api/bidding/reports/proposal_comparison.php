<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../Auth/Session.php');
require_once('../../../bidding/Requirements/Requirements.php');
require_once('../../../bidding/Proposals.php');


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
use Bidding\Proposals as Proposals;


$clean_str=new CleanStr();
$logs = new Logs($DB);
$Part = new Particulars($DB);
$Ses = new Session($DB);
$Index = new Bid($DB);
$Req = new Requirements($DB);
$Prop = new Proposals($DB);

$date = date('m-d-Y');

// view details
if (!isset($_GET['id']) || !isset($_GET['prop'])) exit;

$id = (int) htmlspecialchars(htmlspecialchars($_GET['id']));
$prop = htmlspecialchars(htmlspecialchars($_GET['prop']));
$details = $Req->view($id);

if(!isset($details[0])) exit;

$budget_amount = number_format($details[0]->budget_amount, 2, ',', ',');

$specs = '';


// consolidated values
$conso_suppliers = [];
$conso_amount = [];
$conso_discount = [];
$conso_orig_specs = [];
$conso_other_specs = [];
// all IDs to be compared
$props_ids = array_unique(explode(',', $prop));

for($a = 0; $a < count($props_ids); $a++) {

	$det = $Prop->view($props_ids[$a]);

	if(@$det[0]->id) {
	 	array_push($conso_suppliers,$det[0]->company_name);
	 	array_push($conso_amount,"{$det[0]->currency} {$det[0]->amount}");
	 	array_push($conso_discount,"{$det[0]->currency} {$det[0]->discount}");
	 	
	 	// original specifications
	 	foreach ($det[0]->orig_specs as $key => $value) {
	 		// store to comon parent
	 		if (!isset($conso_orig_specs[$value->bidding_requirements_specs_id])) {
	 			$conso_orig_specs[$value->bidding_requirements_specs_id] = [];
	 		}

	 		if (!isset($conso_orig_specs[$value->bidding_requirements_specs_id][$det[0]->company_name])) {
	 			$conso_orig_specs[$value->bidding_requirements_specs_id][$det[0]->company_name] = [];
	 		}
	 		// add to store
	 		$conso_orig_specs[$value->bidding_requirements_specs_id][$det[0]->company_name][] = $value;	
	 	}


	 	// other specification submitted by suppliers
	 	foreach ($det[0]->other_specs as $key => $value) {
	 		// store to comon parent
	 		if (!isset($conso_other_specs[$value->name])) {
	 			$conso_other_specs[$value->name] = [];
	 		}
	 		if (!isset($conso_other_specs[$value->name][$det[0]->company_name])) {
	 			$conso_other_specs[$value->name][$det[0]->company_name] = [];
	 		}

	 		// add to store
	 		$conso_other_specs[$value->name][$det[0]->company_name][] = $value;		
	 	}

	}	
}



$orig_specs = new \StdClass();

// suppliers name
$suppliers_th = '';
$amount_td = '';
$discount_td = '';
$other_specs = '';






// supplier name
foreach ($conso_suppliers as $key => $value) {
	$suppliers_th.="<th width='130px;'>{$value}</th>";
}

$remaining = (5 - count($conso_suppliers));

if ($remaining > 0) {
	for($b = 0; $b < $remaining; $b++) {
		$suppliers_th.="<th width='130px;'> </th>";	
	}
}




// amount
foreach ($conso_amount as $key => $value) {
	$amount_td.="<td>{$value}</td>";
}

$remaining = (5 - count($conso_amount));

if ($remaining > 0) {
	for($b = 0; $b < $remaining; $b++) {
		$amount_td.="<td> </td>";	
	}
}





// discount
foreach ($conso_discount as $key => $value) {
	$discount_td.="<td>{$value}</td>";
}

$remaining = (5 - count($conso_discount));

if ($remaining > 0) {
	for($b = 0; $b < $remaining; $b++) {
		$discount_td.="<td> </td>";	
	}
}



// original specs
for ($x = 0; $x < count($details[0]->specs); $x++) {	
	$specs.="<tr>
		<td style='text-align:left;'>
			{$details[0]->specs[$x]->name} - {$details[0]->specs[$x]->value} <br/>	
		</td>
		<td></td>";

	// specs
	$specs_td = '';

	$remaining = (5 - count($conso_orig_specs[$details[0]->specs[$x]->id]));

	foreach ($conso_suppliers as $key => $value) {
		if ($conso_orig_specs[$details[0]->specs[$x]->id][$value]) {

			foreach ($conso_orig_specs[$details[0]->specs[$x]->id][$value] as $key2 => $value2) {
				
				// mark specs with different value
				$color = ($value2->value != $details[0]->specs[$x]->value) ? 'red' : '';
					$specs_td.="<td style='color:{$color};'> {$value2->value} </td>";
				 
			}

		} else {
			$specs_td.="<td> </td>";
		}
	}


	if ($remaining > 0) {
		for($b = 0; $b < $remaining; $b++) {
			$specs_td.="<td> </td>";
		}
	}


	$specs.="{$specs_td}</tr>";
}



foreach ($conso_other_specs as $key => $value) {
	$key_name = ucwords($key);

	$other_specs.="<tr>
		<td style='text-align:left;'>{$key_name}</td><td></td>";

		$remaining = 5;

		foreach ($conso_suppliers as $key2 => $value2) {
			if (isset($conso_other_specs[$key][$value2])) {
				$other_specs.="<td>{$conso_other_specs[$key][$value2][0]->value}</td>";
			} else {
				$other_specs.="<td> </td>";
			}
			$remaining --;
			/*if ($conso_other_specs[$details[0]->specs[$x]->id][$value]) {

				foreach ($conso_orig_specs[$details[0]->specs[$x]->id][$value] as $key2 => $value2) {
					
					// mark specs with different value
					$color = ($value2->value != $details[0]->specs[$x]->value) ? 'red' : '';
						$specs_td.="<td style='color:{$color};'> {$value2->value} </td>";
					 
				}

			} else {
				$specs_td.="<td> </td>";
			}*/

		}

		/*foreach ($value as $key2 => $value2) {
			$other_specs.="<td>$value2->value</td>";
		}*/




	

		if ($remaining > 0) {
			for($b = 0; $b < $remaining; $b++) {
				$other_specs.="<td> </td>";
			}
		}


	$other_specs.="</tr>";
	
}



$table= "
	<div align='center'>
<style>
	.ledger-table {
		cellspacing:0px;
		cellpadding:0px;
		border-collapse:collapse;
		font-size:12px;
		margin-left:20px;
	}
	.ledger-table td{
		border:1px solid #ccc;	
	}
	.ledger-table th, .ledger-table td{
		border:1px solid #ccc;
		padding:4px;
		text-align:center;
	}
	.breaker {
		text-align:left;
		margin-left:25px;
	}
</style>



<table class='ledger-table'> 
	<thead>
		<tr>
			<th width='260px;' rowspan='2'>Specifications</th>
			<th width='60px;' rowspan='2'>Quantity</th>
			<th  width='200px;' colspan='5'>Participating Bidders</th>
		</tr>
		<tr>
			{$suppliers_th}

		</tr>
	</thead>
	<tbody>
		<tr>
			<td  style='text-align:left;'>
				Amount : {$details[0]->budget_currency} {$budget_amount} <br/>	
			</td>
			<td></td>
			{$amount_td}
		</tr>
		<tr>
			<td  style='text-align:left;'>
				DISCOUNT :  <br/>	
			</td>
			<td></td>
			{$discount_td}
		</tr>

		$specs

		<tr>
			<td  style='text-align:left;color:gray;'>
				Other Specs  
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

		$other_specs

		<tr>
			<td  style='text-align:left;' colspan='7'>
				We certify to the correctnes of entries of above bids. Based on our assessment, the most advantageous offer to the center is made by: 	
			</td>
		</tr>


	</tbody>
 <tbody>
 	
 </tbody>
 </table>
";

$html = "<html>
<head>
  <style>
    @page { margin: 35px 25px; }
    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align:center; }
    header p {
    	font-size:15px;
    }
    footer { position: fixed; bottom: -75px; left: 0px; right: 0px; height: 30px; text-align:center; font-size:12px; }
    /*p { page-break-after: always; }
    p:last-child { page-break-after: never; }*/
    main.page{

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

  	<article class='text-center'>
  	  	<p>SOUTHEAST ASIAN REGIONAL CENTER FOR GRADUATE STUDY 
		  	<br/>AND RESEARCH IN AGRICULTURE
		  	<br/>College, Laguna, 4031, Philippines
		  	<br/>
	  	</p><br/>
	  	<h3>Proposal Comparison</h3>

	  </article>

	  <article class='text-center'  style='float:left;text-align:center;width:100%;'>
		<div style='float:left;text-align:center;width:100%;'>

			<div style='margin-left:80%;text-align:center;'>
				<div style='height:2px;'><p><b>{$date}</b></p></div>
				<div style='height:2px;border-bottom:1px solid #ccc;'>Date</div>
			</div>
		</div>

		 <p class='breaker'>
		 	&nbsp;&nbsp;&nbsp;
		 </p>
	</article>

	  	<p style='margin-left:25px;'>
	  		<b>{$details[0]->name}</b> <br/>
	  		Estimated budget: {$details[0]->budget_currency} ${budget_amount} <i color='#ccc'>({$details[0]->quantity} {$details[0]->unit})</i>

	  	</p>
	  	{$table}


  </main>

</body>
</html>";

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

//page number
$dompdf->getCanvas()->page_text(280, 800, "Page {PAGE_NUM} of {PAGE_COUNT}", '', 10, array(0,0,0));

// Output the generated PDF to Browser
$dompdf->stream("gasoline.pdf", array("Attachment" => false));

exit(0);

?>