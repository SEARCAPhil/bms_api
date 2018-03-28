<?php
header('Access-Control-Allow-Origin: *');

include_once(dirname(__FILE__).'/../../../../vendor/dompdf/lib/html5lib/Parser.php');
include_once(dirname(__FILE__).'/../../../../vendor/dompdf/src/Autoloader.php');
Dompdf\Autoloader::register();

use Dompdf\Dompdf;


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
			<th width='130px;'>FORD HONDA</th>
			<th width='130px;'>FORD HONDA</th>
			<th width='130px;'>FORD HONDA</th>
			<th width='130px;'>FORD HONDA</th>
			<th width='130px;'>FORD HONDA</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td  style='text-align:left;'>
				Amount: PHP 2,500,00 <br/>	
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td  style='text-align:left;'>
				DISCOUNT : PHP 0.00<br/>	
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
<tr>
			<td  style='text-align:left;'>
				RAM - 16 GB <br/>	
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td  style='text-align:left;'>
				RAM - 16 GB <br/>	
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
<tr>
			<td  style='text-align:left;'>
				RAM - 16 GB <br/>	
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
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
	  	<h3>Bid Comparison</h3>

	  </article>

	  <article class='text-center'  style='float:left;text-align:center;width:100%;'>
		<div style='float:left;text-align:center;width:100%;'>

			<div style='margin-left:80%;text-align:center;'>
				<div style='height:2px;'><p>dsadhjhgjhgjhgjhg</p></div>
				<div style='height:2px;border-bottom:1px solid #ccc;'>Date</div>
			</div>
		</div>

		 <p class='breaker'>
		 	&nbsp;&nbsp;&nbsp;
		 </p>
	</article>

	  	<p style='margin-left:25px;'>
	  		Purchase of 1 unit 2016 Model Ford New Explorer 2.4L Ecobost, White <br/>
	  		Estimated budget: PHP 2,500,00

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