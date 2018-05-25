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

$data_per_department = [];

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

    # aggregate data by department
    # create empty stack
    if (!isset($data_per_department[$value->department])) {
         $data_per_department[$value->department] = [];
        $data_per_department[$value->department] = array(
            'name' => $value->department,
            'alias' => $value->department_alias,
            'exempted' =>0,
            'normal' => 0,
            'total' => 0,
            'in_progress' => 0,
            'closed' => 0,
            'failed' => 0,
            'in_review' => 0,
            'engagement' => '0%'
        );
    }
    # count total
    $data_per_department[$value->department]['total']++;
    # count excemption
    if ($value->excemption) {
            $data_per_department[$value->department]['exempted']++;
    } else {
            $data_per_department[$value->department]['normal']++;
    }
    # count per dept status
    #in review
    if ($value->status == 1 ) {
        $data_per_department[$value->department]['in_review']++;
   }
    # in progress
    if ($value->status == 3 ) {
        $data_per_department[$value->department]['in_progress']++;
    }

    # closed
    if ($value->status == 5 ) {
        $data_per_department[$value->department]['closed']++;
    }

    # failed
    if ($value->status == 6 ) {
        $data_per_department[$value->department]['failed']++;
    }
}

# analysis
# All is in active state
$analysis = [];
# too many pending , not all item is pending
if ($in_progress > $closed && ($in_progress!=$total)) {
    $message = array('message' => 'Too many requests in progress', 'severity' => 'warning');
    array_push($analysis, $message);
}

if ($in_progress==$total) {
    $message = array('message' => 'You haven\'t closed any bidding request during this period', 'severity' => 'warning');
    array_push($analysis, $message);
}


# engagement per department
# measure in percentage
# number of bidding request made by department / total number of bidding
foreach ($data_per_department as $key => $value) {
    $perc = 0;

    # compute
    $perc = ($value['total'] / $total) * 100;
    # change percentage value
    $data_per_department[$value['name']]['engagement'] = "{$perc}%";

}


# result
$data = json_encode(array('total' => $total, 
'failed' => $failed, 
'in_progress' => $in_progress, 
'closed' => $closed, 
'in_review' => $in_review, 
'breakdown' => array('normal' => $normal, 'exempted' => $exempted),
'analysis' => $analysis,
'department' => $data_per_department,
'from' => $from,
'to' => $to));

echo $data;
?>