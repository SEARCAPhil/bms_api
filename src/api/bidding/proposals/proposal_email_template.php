<?php 
/*@$submitted_proposal[0]->company_name = 'Accent Micro Technology Inc.';
@$submitted_proposal[0]->username = 'admin@amti.com';
@$submitted_proposal[0]->name = 'HP EliteOne 800 G4';
@$submitted_proposal[0]->quantity = '25';
@$submitted_proposal[0]->unit = 'Unit';

@$unit_price = '25000';
@$submitted_proposal[0]->amount = '350000';
@$submitted_proposal[0]->currency = 'PHP';
@$submitted_proposal[0]->remarks = '';
$specs = [];}
*/
error_reporting(false);
$specs_tr = '';
foreach ($submitted_proposal[0]->specs as $key => $value) {
  $specs_tr.="<tr>
                <td>{$value->name}</td>
                <td>{$value->value}</td>
              </tr>";
}


return <<<EOD

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Bidding Proposal</title>
      <style type="text/css">
      body {
       padding-top: 0 !important;
       padding-bottom: 0 !important;
       padding-top: 0 !important;
       padding-bottom: 0 !important;
       margin:0 !important;
       width: 100% !important;
       -webkit-text-size-adjust: 100% !important;
       -ms-text-size-adjust: 100% !important;
       -webkit-font-smoothing: antialiased !important;
     }
     .tableContent img {
       border: 0 !important;
       display: block !important;
       outline: none !important;
     }
     a{
      color:#382F2E;
    }

    p, h1{
      color:#382F2E;
      margin:0;
    }
 p{
      text-align:left;
      color:#999999;
      font-size:14px;
      font-weight:normal;
      line-height:19px;
    }

    a.link1{
      color:#382F2E;
    }
    a.link2{
      font-size:16px;
      text-decoration:none;
      color:#ffffff;
    }

    h2{
      text-align:left;
       color:#222222; 
       font-size:19px;
      font-weight:normal;
    }
    div,p,ul,h1{
      margin:0;
    }

    .bgBody{
      background: #ffffff;
    }
    .bgItem{
      background: #ffffff;
    }
	
@media only screen and (max-width:480px)
		
{
		
table[class="MainContainer"], td[class="cell"] 
	{
		width: 100% !important;
		height:auto !important; 
	}
td[class="specbundle"] 
	{
		width:100% !important;
		float:left !important;
		font-size:13px !important;
		line-height:17px !important;
		display:block !important;
		padding-bottom:15px !important;
	}
		
td[class="spechide"] 
	{
		display:none !important;
	}
	    img[class="banner"] 
	{
	          width: 100% !important;
	          height: auto !important;
	}
		td[class="left_pad"] 
	{
			padding-left:15px !important;
			padding-right:15px !important;
	}
		 
}
	
@media only screen and (max-width:540px) 

{
		
table[class="MainContainer"], td[class="cell"] 
	{
		width: 100% !important;
		height:auto !important; 
	}
td[class="specbundle"] 
	{
		width:100% !important;
		float:left !important;
		font-size:13px !important;
		line-height:17px !important;
		display:block !important;
		padding-bottom:15px !important;
	}
		
td[class="spechide"] 
	{
		display:none !important;
	}
	    img[class="banner"] 
	{
	          width: 100% !important;
	          height: auto !important;
	}
	.font {
		font-size:18px !important;
		line-height:22px !important;
		
		}
		.font1 {
		font-size:18px !important;
		line-height:22px !important;
		
		}
}

.specs-table{
  width:100%;
}

.specs-table tr:nth-child(even) {
  background:rgba(230,230,230,0.3);
}

.specs-table tr td {
  border-bottom:1px solid rgba(230,230,230,0.8);
  padding:5px;
}

    </style>
<script type="colorScheme" class="swatch active">
{
    "name":"Default",
    "bgBody":"ffffff",
    "link":"382F2E",
    "color":"999999",
    "bgItem":"ffffff",
    "title":"222222"
}
</script>
  </head>
  <body paddingwidth="0" paddingheight="0"   style="padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;" offset="0" toppadding="0" leftpadding="0">
    <table bgcolor="#ffffff" width="100%" border="0" cellspacing="0" cellpadding="0" class="tableContent" align="center"  style='font-family:Helvetica, Arial,serif;'>
  <tbody>
    <tr>
      <td><table width="600" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" class="MainContainer">
  <tbody>
    <tr>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td valign="top" width="40">&nbsp;</td>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
  <!-- =============================== Header ====================================== -->   
    <tr>
    	<td height='75' class="spechide"></td>
        
        <!-- =============================== Body ====================================== -->
    </tr>
    <tr>
      <td class='movableContentContainer ' valign='top'>
      	<div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td height="35"></td>
    </tr>
    <tr>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td valign="top" align="center" class="specbundle">
                                
            <p style='margin:0;font-family:Georgia,Time,sans-serif;font-size:26px;color:#222222;'><span class="specbundle2"><span class="font1">{$submitted_proposal[0]->company_name}</span></span></p>

            <p style='margin:0;font-family:Georgia,Time,sans-serif;font-size:12px;color:#222222;'><span class="specbundle2"><span class="font1">{$submitted_proposal[0]->username}</span></span></p>
                                
      </td>
    </tr>
  </tbody>
</table>
</td>
    </tr>
  </tbody>
</table>
        </div>
        
        <div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                          <tr><td height='55'></td></tr>
                          <tr>
                            <td align='left'>
                              <div class="contentEditableContainer contentTextEditable">
                                <div>
                                  <p>
                                    <h2><b>{$submitted_proposal[0]->name}</b> ({$submitted_proposal[0]->quantity} {$submitted_proposal[0]->unit})</h2><br/>
                                    <span><b>Unit Price :</b> <span style="color:red;"> {$submitted_proposal[0]->currency} {$unit_price}</span><br/>\n
                                      <b>Total : <span style="color:red;">{$submitted_proposal[0]->amount}</span></b>
                                    </span>
                                  </p>
                                </div>
                              </div>
                            </td>
                          </tr>

                          <tr><td height='15'> </td></tr>

                          <tr>
                            <td align='left'>
                              <div class="contentEditableContainer contentTextEditable">
                                <div class="contentEditable" align='center'>
                                  <p >
                                   {$submitted_proposal[0]->remarks}
                                  </p>
                                </div>
                              </div>
                            </td>
                          </tr>



                          <tr>
                            <td align='left'>
                              <hr/>
                              <br/>
                              <h4>Specifications</h4>
                              <!-- specs -->
                              <table class="specs-table" cellspacing="0" cellpadding="0">
                                {$specs_tr}
                              </table>
                            </td>
                          </tr>


                           <tr>
                            <td align='left'>
                              <div class="contentEditableContainer contentTextEditable">
                                <div align='center'>
                                  <p>
                                    <br/>
                                    <br/>
                                    <br/>
                                    <br/>
                                    This is a system generated email. Please Do not reply
                                    <br>
                                    <span style='color:#222222;'>SEARCA Bidding Management System</span>
                                  </p>
                                </div>
                              </div>
                            </td>
                          </tr>

                          <tr><td height='55'></td></tr>

                          <tr>
                            <td align='center'>
                              <table>
                                <tr>
                                  <td align='center' bgcolor='#1A54BA' style='background:#DC2828; padding:15px 18px;-webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px;'>
                                    <div class="contentEditableContainer contentTextEditable">
                                      <div class="contentEditable" align='center'>
                                        <a target='_blank' href='#' class='link2' style='color:#ffffff;'>View details in BMS</a>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr><td height='20'></td></tr>
                        </table>
        </div>
        <div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td height='65'>
    </tr>
    <tr>
      <td  style='border-bottom:1px solid #DDDDDD;'></td>
    </tr>
    <tr><td height='25'></td></tr>
    <tr>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td valign="top" class="specbundle"><div class="contentEditableContainer contentTextEditable">
                                      <div class="contentEditable" align='center'>
                                        <p  style='text-align:left;color:#CCCCCC;font-size:12px;font-weight:normal;line-height:20px;'>
                                          <span style='font-weight:bold;'>Southeast Asian Regional Center for Graduate Study and Research in Agriculture.</span>
                                          <br>
                                          College, Los Baños, Laguna 4031 Philippines
                                          <br>
                                          <a target="_blank" class='link1' class='color:#382F2E;' href="https://www.searca.org">Visit Website</a>
                                          <br>
                                          <a target='_blank' class='link1' class='color:#382F2E;' href="http://www.searca.org/index.php/about-us/who-we-are">Read more about SEARCA</a>
                                        </p>
                                      </div>
                                    </div></td>
      <td valign="top" width="30" class="specbundle">&nbsp;</td>
      <td valign="top" class="specbundle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td valign='top' width='52'>
                                    <div class="contentEditableContainer contentFacebookEditable">
                                      <div class="contentEditable">
                                        <a target='_blank' href="#"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" x="0px" y="0px" width="20" height="20" viewBox="0 0 50 50" style="fill:#2863dc;" class="icon icons8-Facebook-Filled" >    <path d="M40,0H10C4.486,0,0,4.486,0,10v30c0,5.514,4.486,10,10,10h30c5.514,0,10-4.486,10-10V10C50,4.486,45.514,0,40,0z M39,17h-3 c-2.145,0-3,0.504-3,2v3h6l-1,6h-5v20h-7V28h-3v-6h3v-3c0-4.677,1.581-8,7-8c2.902,0,6,1,6,1V17z"></path></svg></a>
                                      </div>
                                    </div>
                                  </td>
      <td valign="top" width="16">&nbsp;Facebook</td>
      <td valign='top' width='52'>
                                    
                                  </td>
    </tr>
  </tbody>
</table>
</td>
    </tr>
  </tbody>
</table>
</td>
    </tr>
    <tr><td height='88'></td></tr>
  </tbody>
</table>

        </div>
        
        <!-- =============================== footer ====================================== -->
      
      </td>
    </tr>
  </tbody>
</table>
</td>
      <td valign="top" width="40">&nbsp;</td>
    </tr>
  </tbody>
</table>
</td>
    </tr>
  </tbody>
</table>
</td>
    </tr>
  </tbody>
</table>

      </body>
      </html>
EOD;

