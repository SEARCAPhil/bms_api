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
		font-size:14px;
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
	<tbody>
		<tr>
			<td  style='height:300px;'></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan='3'  style='height:50px;'></td>
			<td><b>PHP</b></td>
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
	 	<u><b>ADORACION T. ROBLES</b></u><br/>
	 	Unit Head, Management Services and <br/> Executive Coordinator, OD
	 </div>

 	<div style='float:left;text-align:center; height:40px;width:300px;margin-left:100px;'>
	 	<u><b>ADORACION T. ROBLES</b></u><br/>
	 	Unit Head, Management Services and <br/> Executive Coordinator, OD
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
	 	<u> <b>ADORACION T. ROBLES</b></u><br/>
	 	Vice Chair, CBA
	 </div>

 	<div style='float:left;text-align:center; height:100px;width:300px;margin-left:100px;'>
	 	<u><b>GIL C. SAGUIGUIT, JR.</b></u><br/>
	 	Director
	</div>



	 


</div>
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
  	<article>
  		<section style='width:100%;height:20px;'>
  			<div style='float:left;width:150px;margin-left:440px;'>
  				CBA Control No.
  			</div>
  			<div style='float:left;width:150px;border-bottom:1px solid #ccc;'>&nbsp;&nbsp;</div>
  			  		</section>
  	</article>

  	<article class='text-center'>
  	  	<p>SOUTHEAST ASIAN REGIONAL CENTER FOR GRADUATE STUDY 
		  	<br/>AND RESEARCH IN AGRICULTURE
		  	<br/>College, Laguna, 4031, Philippines
		  	<br/>
	  	</p><br/>
	  	<h3>BIDDING REQUEST</h3>

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

	  	<p style='float:left;margin-left:27px;text-align:left;'>
	  		The Chairman <br/>
	  		SEARA Committee on BIDS and Awards <br/>
	  		College, Laguna

	  	</p>
	  	<br/>
	  	{$table}


  </main>

    <main class='page'>

  		<center style='padding-top:30px;'>
  			<b><br/>
  				IT EQUIPMENT FY 2017/2018 <br/>
  				STANDARD MINIMUM SPECIFICATIONS
  			</b>
  		</center>

  		

  		<section style='text-align:left;margin-left:30px;'>
  			<br/><br/><br/><br/>
  			<p>
	  			<b>A. STANDARD DESKTOP SPECIFICATIONS (21 UNITS)</b>
	  		</p> 
	  		<div style='text-align:left;margin-left:25px;'>
		  		<p>
		  			Processor - dual core 2.4 GHz+ (i5 or i7 series Intel processor or equivalent AMD)
		  		</p>
		  		<p>
					RAM - 16 GB <br/
					Hard Drive - 256 GB or larger solid state hard drive <br/
					Graphics Card - any with DisplayPort/HDMI or DVI support - desktop only <br/
					Wireless (for laptops) - 802.11ac (WPA2 support required) <br/
					Monitor - 23 widescreen LCD with DisplayPort HDMI or DVI support - desktop only <br/
					Operating System - Windows 10 Home or Professional editions, or Apple OS X 10.12.3 <br/
					Warranty - 3 year warranty - desktop only <br/
					Warranty - 4 year warranty with accidental damage protection - laptop only <br/
					Backup Device - External hard drive and/or USB Flash Drive <br/
		  		</p>
		  	</div>
	  	</section>
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