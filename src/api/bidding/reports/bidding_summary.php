<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Index.php');
require_once('../../../bidding/Particulars/Particulars.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../suppliers/Logs/Logs.php');
require_once('../../../auth/Session.php');
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

if (!isset($_GET['from']) || !isset($_GET['to'])) exit;

$date = date('m-d-Y');
$from = htmlentities(htmlspecialchars($_GET['from']));
$to = htmlentities(htmlspecialchars($_GET['to']));

$data = ($Index->lists_all_between_dates($from, $to));
$table = "";
foreach ($data as $key => $value) {
    # particulars
    $part = '';
    $total_budget = [];
    $req = '';
    foreach ($value->particulars as $key2 => $value2) {
        $part.=$value2->name.'<br/>';
       # requirements
       foreach($value2->requirements as $keyReq => $valReq) {
            $req.="{$valReq->name}<br/>"; 
            # currency
            if (!isset($total_budget[$valReq->budget_currency])) $total_budget[$valReq->budget_currency] = 0;
            $total_budget[$valReq->budget_currency]+= $valReq->budget_amount;   
       }
    }

    $p = '';
    foreach($total_budget as $keyB => $valueB) {
        # formated
        $amount_formated = number_format($valueB, 2, '.', ',');
        $p.="{$keyB} {$amount_formated}";
    }

    $table.="<tr>
    <td class='text-center'>{$value->date_created}</td>
    <td class='text-center'>{$value->id}</td>
    <td><b>{$part}</b><small>{$req}</small></td>
    <td class='text-center'>{$p}</td>
    <td>{$value->profile_name}</td>
    </tr>";
   
}

$html = "<html>
<head>
  <style>
    @page { margin: 0px 25px;}
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

    .table-bordered {
        cborder-spacing:0;
        border-collapse: collapse;
        width:100%;
    }

    .table-bordered tr td {
        border:1px solid #ccc;
        padding: 4px;
        font-size:14px;
    }


    .text-center{
    	text-align:center;
    }
  </style>
</head>
<body>

  <main class='first-page'>
  	<article>
  		<section style='width:100%;height:20px;'>
  			<div style='float:left;width:120px;margin-left:440px;'>
  			
  			</div>
  			
  			  		</section>
  	</article>

  		<article class='text-center'>
	  	  	<p>SOUTHEAST ASIAN REGIONAL CENTER FOR GRADUATE STUDY 
			  	<br/>AND RESEARCH IN AGRICULTURE
			  	<br/>College, Laguna, 4031, Philippines
			  	<br/>
		  	</p>
		 </article>

	 

	  	<div style='float:left;margin-left:27px;text-align:left;width:72%;'>
	  		The Chairman <br/>
	  		SEARA Committee on BIDS and Awards <br/>
	  		<b>From</b> : <u>{$from}</u> &nbsp;&nbsp;&nbsp;<b>To</b> : <u>{$to}</u>

	  	</div>

	  	<div style='float:left;text-align:left;width:20%;'>
	  		<div style='float:left;text-align:center;width:100%;'>
				<div style='text-align:center;'>
					<div style='height:2px;'><p><b>{$date}</b></p></div>
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
          <p class='breaker'>
		 	&nbsp;&nbsp;&nbsp;
         </p> 
         
    <article style='width:95%;margin-left:30px;'>
        <table class='table-bordered'>
            <tr style='background:rgba(240,240,240,0.3);'>
                <td class='text-center'>DATE</td>
                <td class='text-center'>BID NO.</td>
                <td class='text-center'>ITEM / DESCRIPTION</td>
                <td class='text-center'>APPROVED BUDGET</td>
                <td class='text-center'>REMARKS</td>
            </tr>
            {$table}
        </table>
    </article>
	  

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