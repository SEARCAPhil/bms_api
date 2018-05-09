<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../bidding/Index/Report.php');
require_once('../../../helpers/CleanStr/CleanStr.php');
require_once('../../../config/database/connections.php');
require_once('../../../auth/Session.php');

use Bidding\Report as Report;
use Helpers\CleanStr as CleanStr;
use Auth\Session as Session;

$Rep = new Report($DB);

# initial count
$total = $failed = $in_progress = $closed = $in_review = 0;
$normal = $exempted = 0;
# param
$from = isset($_GET['from']) ? strip_tags($_GET['from']) : date('Y-m').'-01';
$to = isset($_GET['to']) ? strip_tags($_GET['to']) : date('Y-m-d');
# actual data
# this might take long
$data = ($Rep->lists_all_between_dates($from, $to));

# count data
foreach ($data as $key => $value) {
    # TOTAL
    $total ++;
    # in progress
    if ($value->status == 3) $in_progress ++;
    # in review
    if ($value->status == 1) $in_review ++;
    # closed
    if ($value->status == 5) $closed ++;
    # failed
    if ($value->status == 6) $failed ++;

    # exemption details
    if ($value->excemption) {
        $exempted ++;
    } else {
        $normal ++; 
    }
}

# analysis
# All is in active state
$analysis = [];
# too many pending , not all item is pending
if ($in_progress > $closed && ($in_progress!=$total)) {
    $message = array('message' => 'Too many pending request', 'severity' => 'warning');
    array_push($analysis, $message);
}

if ($in_progress==$total) {
    $message = array('message' => 'You haven\'t closed any bidding request during this period', 'severity' => 'warning');
    array_push($analysis, $message);
}


# result
$data = json_encode(array('total' => $total, 
'failed' => $failed, 
'in_progress' => $in_progress, 
'closed' => $closed, 
'in_review' => $in_review, 
'breakdown' => array('normal' => $normal, 'exempted' => $exempted),
'analysis' => $analysis,
'from' => $from,
'to' => $to));

echo $data;
?>