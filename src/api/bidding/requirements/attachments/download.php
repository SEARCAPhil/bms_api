<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../../config/server.php');
require_once('../../../../bidding/Attachments.php');
require_once('../../../../helpers/CleanStr/CleanStr.php');
require_once('../../../../config/database/connections.php');
require_once('../../../../suppliers/Logs/Logs.php');

use Bidding\Attachments as Attachments;
use Suppliers\Logs as Logs;
use Helpers\CleanStr as CleanStr;

$LIMIT=20;
$status='all'; 
$page=1;

$clean_str=new CleanStr();
$logs = new Logs($DB);
$att = new Attachments($DB);


$method=($_SERVER['REQUEST_METHOD']);

function download($db,$id){
	# Upload directory
	$dir = UPLOAD_DIR;
	try{
		$db->beginTransaction();
		$id=htmlentities(htmlspecialchars($id));
		$file_exists=0;
		$returnFile='File not found!This will automatically close after 5 seconds.<script>setTimeout(function(){window.close();},5000);</script>';
			$attach_sql="SELECT bidding_requirements_attachments.* from bidding_requirements_attachments left join bidding_requirements on bidding_requirements_attachments.bidding_requirements_id=bidding_requirements.id where bidding_requirements_attachments.id=:id";
			$attach_statement=$db->prepare($attach_sql);
			$attach_statement->bindParam(':id',$id);
			$attach_statement->execute();

			if($row=$attach_statement->fetch(PDO::FETCH_OBJ)){

					$bid = $row->bidding_requirements_id;

					if(!is_null($row->original_copy_id)){
						//original copy information
						$attach_sql2="SELECT bidding_requirements_attachments.* from bidding_requirements_attachments left join bidding_requirements on bidding_requirements_attachments.bidding_requirements_id=bidding_requirements.id where bidding_requirements_attachments.id=:id";
						$attach_statement2=$db->prepare($attach_sql2);
						$attach_statement2->bindParam(':id',$row->original_copy_id);
						$attach_statement2->execute();

						if($row2=$attach_statement2->fetch(PDO::FETCH_OBJ)){
							//original location where file is located on the server
							$bid = $row->bidding_requirements_id;
						}
					}

					// Get document root
					$doc_root = $_SERVER['DOCUMENT_ROOT'];  

					// Account for possible trailing slash
					if( substr( $doc_root, strlen( $doc_root )-1, 1 ) == '/' ){
						$doc_root = substr( $doc_root, 0, strlen( $doc_root - 1 ) );
					}

					$absolute_dir = $doc_root.'/'.parse_url($dir)['host'].parse_url($dir)['path'].$bid.'/'.$row->filename;
					// read files
					if(file_exists($absolute_dir)){
						$file_exists=1;
						#headers to force download
						$returnFile = header("Content-Description: File Transfer"); 
						$returnFile.= header("Content-Type: application/octet-stream"); 
						$returnFile.= header("Content-Disposition: attachment; filename=\"$row->filename\"");
						$returnFile.= ob_clean();
						$returnFile.= flush();
						$returnFile.= readfile ($absolute_dir);	
					}
			}

		
			$db->commit();

			if($file_exists){
				return $returnFile;
			}else{
				echo $returnFile;
			}
			

	}catch(PDOException $e){$db->rollback(); echo $e->getMessage();}
}

if($method=="GET") {
	$id = (int) @strip_tags(htmlentities(htmlspecialchars($_GET['id'])));

	if(!empty($id)) {
		download($DB,$id);
	}else{
		echo 'File not found!This will automatically close after 5 seconds.<script>setTimeout(function(){window.close();},5000);</script>';
	}
}

?>