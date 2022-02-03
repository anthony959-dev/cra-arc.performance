
<?php include "./includes/upd_header.php"; ?>
<?php include "./includes/upd_sidebar.php"; ?>
<?php include "./includes/date-ranges.php"; ?>
<?php include "./includes/functions.php"; ?>
<?php ini_set('display_errors', 1);
?>

<!--Translation Code start-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="./assets/i18n/js/CLDRPluralRuleParser.js"></script>
<script src="./assets/i18n/js/jquery.i18n.js"></script>
<script src="./assets/i18n/js/jquery.i18n.messagestore.js"></script>
<script src="./assets/i18n/js/jquery.i18n.fallbacks.js"></script>
<script src="./assets/i18n/js/jquery.i18n.language.js"></script>
<script src="./assets/i18n/js/jquery.i18n.parser.js"></script>
<script src="./assets/i18n/js/jquery.i18n.emitter.js"></script>
<script src="./assets/i18n/js/jquery.i18n.emitter.bidi.js"></script>
<script src="./assets/i18n/js/global.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.13.0/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/2.25.6/d3-legend.min.js"></script>

<!--Main content start-->

<?php

//-----------------------------
// FUNCTIONS
// we need to add these in functions.php and remove them from every other page
//-----------------------------

function differ($old, $new)
{
    return (($new - $old) / $old);
}

function numDiffer($old, $new)
{
    return ($new - $old);
}

function posOrNeg($num)
{
    if ($num > 0) return 'text-success:arrow_upward';
    else if ($num == 0) return 'text-warning:';
    else return 'text-danger:arrow_downward';
}

function posOrNeg2($num)
{
    if ($num > 0) return 'text-success:+';
    else if ($num == 0) return 'text-warning:';
    else return 'text-danger:-';
}

function percent($num)
{
    return round($num * 100, 0) . '%';
}

function metKPI($num, $old)
{
    if (($num > 0.8) || (abs($old-$num)>0.2))  return 'text-success:check_circle:Met';
    else return 'text-danger:warning:Did not meet';
}

?>




<?php
require 'vendor/autoload.php';
require_once ('./php/get_aa_data.php');
use TANIOS\Airtable\Airtable;

include_once "php/lib/sqlite/DataInterface.php";
include_once 'php/Utils/Date.php';

use Utils\DateUtils;

//$startTime = microtime(true);

$taskId = $_GET['taskId'] ?? "recXkhR8zOWnR83TU";

$dr = $_GET['dr'] ?? "week";

$lang = $_GET['lang'] ?? "en";

$db = new DataInterface();
$taskData = $db->getTaskById($taskId)[0];

$taskPages = $db->getPagesByTaskId($taskId, ['Url']);
$taskPages = array_column($taskPages, 'Url');

$taskProjects = $db->getProjectsByTaskId($taskId, ['title']);
$relatedProjects = array_column($taskProjects, 'title');


$uxTestSelectedFields = [
      '"Test title"',
      '"Success Rate"',
      '"Scenario/Questions"',
      'Date',
      '"# of Users"'
];

$taskTests = $db->getUxTestsByTaskId($taskId, $uxTestSelectedFields);
//$taskTests = $db->getUxTestsByTaskId($taskId, $uxTestSelectedFields);
$relatedUxTests = array_column($taskTests, "Success Rate");


//$relatedUxTests = array_column($taskTests, 'title');


// echo "<h4>UX Test correct</h4><pre>";
// print_r($taskTests);
// echo "</pre>";

// echo "-----------------<br/>";
//
//
// $uxTestAll=[];
// foreach ($taskProjects as $project_id) {
//   //echo ($project_id['id']);
//   $uxTest = $db->getUxTestsByProjectId($project_id['id'], $uxTestSelectedFields);
//   $uxTestAll[] = $uxTest;
//   //$relatedUxTests = array_column($uxTests, 'Test title');
// }
//
// echo "<h4>UX Test</h4><pre>";
// print_r($uxTestAll);
// echo "</pre>";


//$taskUxTests = $db->getProjectsByTaskId($taskId, $uxTestSelectedFields);
//$relatedProjects = array_column($taskUxTests, "Test title");




$dateUtils = new DateUtils();

$weeklyDatesHeader = $dateUtils->getWeeklyDates('header');
?>

<h1 class="visually-hidden">Usability Performance Dashboard</h1>
<div class="back_link"><span class="material-icons align-top">west</span> <a href="./tasks_home.php" alt="Back to Tasks home page">Tasks</a></div>

<h2 class="h3 pt-2 pb-2" data-i18n=""><?=$taskData['Task']?></h2>

<div class="page_header back_link">
        <span id="page_project">
              <?php
              if (count($taskProjects) > 0) {
                  echo '<span class="material-icons align-top">folder</span>';
              }

              echo implode(", ", array_map(function($project) {
                  return '<a href="./projects_summary.php?projectId='.$project['id'].'" alt="Project: '.$project['title'].'">' . $project['title'] . '</a>';
                  //SWITCH TO THIS line after the summary page is done
                  //return '<a href="./projects_summary.php?prj='.$project.'" alt="Project: '.$project.'">' . $project . '</a>';
              }, $taskProjects));
              ?>
         </span>
</div>

<div class="tabs sticky">
    <ul>
        <li <?php if ($tab=="summary") {echo "class='is-active'";} ?>><a href="./tasks_summary.php?taskId=<?=$taskId?>" data-i18n="tab-summary">Summary</a></li>
        <li <?php if ($tab=="webtraffic") {echo "class='is-active'";} ?>><a href="./tasks_webtraffic.php?taskId=<?=$taskId?>" data-i18n="tab-webtraffic">Web traffic</a></li>
        <li <?php if ($tab=="searchanalytics") {echo "class='is-active'";} ?>><a href="./tasks_searchanalytics.php?taskId=<?=$taskId?>" data-i18n="tab-searchanalytics">Search analytics</a></li>
        <li <?php if ($tab=="pagefeedback") {echo "class='is-active'";} ?>><a href="./tasks_pagefeedback.php?taskId=<?=$taskId?>" data-i18n="tab-pagefeedback">Page feedback</a></li>
        <li <?php if ($tab=="calldrivers") {echo "class='is-active'";} ?>><a href="./tasks_calldrivers.php?taskId=<?=$taskId?>" data-i18n="tab-calldrivers">Call drivers</a></li>
        <li <?php if ($tab=="uxtests") {echo "class='is-active'";} ?>><a href="#" data-i18n="tab-uxtests">UX tests</a></li>
    </ul>
</div>

<?php

// // Adobe Analytics
//
// if (!isset($_SESSION['CREATED']))
// {
//     $_SESSION['CREATED'] = time();
//     require_once ('./php/getToken.php');
// }
// else if (time() - $_SESSION['CREATED'] > 86400)
// {
//     session_regenerate_id(true);
//     $_SESSION['CREATED'] = time();
//     require_once ('./php/getToken.php');
// }
// if (isset($_SESSION["token"]))
// {
//     require_once ('./php/api_post.php');
//     $config = include ('./php/config-aa.php');
//     $data = include ('./php/data-aa.php');
// }

?>

<div class="row mb-4 mt-1">
    <div class="dropdown">
        <button type="button" class="btn bg-white border border-1 dropdown-toggle" id="range-button" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-icons align-top">calendar_today</span> <span data-i18n="dr-lastweek">Last week</span></button>
        <span class="text-secondary ps-3 text-nowrap dates-header-week"><strong><?=$weeklyDatesHeader['current']['start']?> - <?=$weeklyDatesHeader['current']['end']?></strong></span>
        <span class="text-secondary ps-1 text-nowrap dates-header-week" data-i18n="compared_to">compared to</span>
        <span class="text-secondary ps-1 text-nowrap dates-header-week"><strong><?=$weeklyDatesHeader['previous']['start']?> - <?=$weeklyDatesHeader['previous']['end']?></strong></span>

        <ul class="dropdown-menu" aria-labelledby="range-button" style="">
            <li><a class="dropdown-item active" href="#" aria-current="true" data-i18n="dr-lastweek">Last week</a></li>
            <li><a class="dropdown-item" href="#" data-i18n="dr-lastmonth">Last month</a></li>
        </ul>

    </div>
</div>

<?php

// echo "<pre>";
// print_r($taskData);
// echo "</pre>";
//
// echo "<pre>";
// print_r($taskProjects);
// echo "</pre>";


//


//sort the array by Date
usort($taskTests, function($b, $a) {
   return new DateTime($a['Date']) <=> new DateTime($b['Date']);
 });

//$prjByGroupType = group_by('Test Type', $prjData);

// echo "<pre>";
// print_r($taskTests);
// echo "</pre>";
//
// echo "<pre>";
// print_r($taskTests[0]['Success Rate']);
// echo "</pre>";

$taskParticipants = array_sum(array_column_recursive($taskTests,"# of Users"));

//     ?>

<div class="row mb-2 gx-2">
   <div class="col-lg-6 col-md-6 col-sm-12">
     <div class="card">
       <div class="card-body card-pad pt-2">
         <h3 class="card-title"><span class="h6" data-i18n="">Latest success rate</span></h3>
           <div class="row">
             <div class="col-lg-8 col-md-8 col-sm-8">
               <?php
                if (count($taskTests)>0) {
                  ?>
                  <span class="h3 text-nowrap"><?=percent($taskTests[0]['Success Rate']);?></span>
                  <?php
                }
                else { ?>
                  <span class='small'>No UX test associated with this task</span>
                <?php
                }
               ?>
              </div>
             <div class="col-lg-4 col-md-4 col-sm-4 text-end"><span class="h3 text-nowrap"><span class="material-icons"></span> </span></div>
         </div>
       </div>
     </div>
   </div>

   <div class="col-lg-6 col-md-6 col-sm-12">
     <div class="card">
       <div class="card-body card-pad pt-2">
         <h3 class="card-title"><span class="h6" data-i18n="">Total participants from all tests</span></h3>
           <div class="row">
             <div class="col-sm-8">
               <?php
                if (count($taskTests)>0) {
                  ?>
                  <span class="h3 text-nowrap"><?=number_format($taskParticipants);?></span>
                  <?php
                }
                else { ?>
                  <span class='small'>No UX test associated with this task</span>
                <?php
                }
               ?>
              </div>
             <div class="col-lg-4 col-md-4 col-sm-4 text-end"><span class="h3 text-nowrap"><span class="material-icons"></span></span></div>
         </div>
       </div>
     </div>
   </div>
 </div>


 <!-- <div class="row mb-4">
   <div class="col-lg-12 col-md-12">
     <div class="card">
       <div class="card-body pt-2">
         <h3 class="card-title"><span class="card-tooltip h6" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="right" data-bs-content="" data-bs-original-title="" title="" data-i18n="">Members</span></h3>

         <div class="table-responsive">
           <table class="table table-striped dataTable no-footer">
             <thead>
               <tr>
                 <th data-i18n="">Project</th>
                 <th data-i18n="">Scenario</th>
                 <th data-i18n="">Result</th>
                 <th data-i18n="">Date</th>
               </tr>
             </thead>
             <tbody>

               <tr>
                 <td>Project Lead</td>
                 <td><?=$prjLeads[0];?></td>
                 <td></td>
               </tr>
             </tbody>
           </table>
         </div>

         </div></div>

         <div class="row"><div class="col-sm-12 col-md-5"></div><div class="col-sm-12 col-md-7"></div></div>
       </div>
     </div> -->

     <div class="row mb-4">
       <div class="col-lg-12 col-md-12">
         <div class="card">
           <div class="card-body pt-2">
             <h3 class="card-title"><span class="card-tooltip h6" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="right" data-bs-content="Success rate and scenarios" data-bs-original-title="" title="" data-i18n="">Success rate and scenarios</span></h3>
             <div id="toptask_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer"><div class="row"><div class="col-sm-12 col-md-6"></div><div class="col-sm-12 col-md-6"></div></div><div class="row"><div class="col-sm-12">

               <?php
                   // uasort($prevPages, function($b, $a) {
                   //    if ($a["data"][3] == $b["data"][3]) {
                   //        return 0;
                   //    }
                   //    return ($a["data"][3] < $b["data"][3]) ? -1 : 1;
                   //  });
                   //
                    // $top15prevPages = array_slice($prevPages, 0, 15);
                    // //$top5Decrease = array_reverse(array_slice($fieldsByGroup, -5));
                    $qry = $taskTests;
                    // echo "---<pre>";
                    // print_r($taskTests);
                    // echo "</pre>";

                    if (count($qry) > 0) { ?>
                      <div class="table-responsive">
                        <table class="table table-striped dataTable no-footer" role="grid" id="toptask">
                          <caption>Success rate and scenarios</caption>
                          <thead>
                            <tr>
                              <th class="sorting" aria-controls="toptask" aria-label="Project" data-i18n="" scope="col">Project</th>
                              <th class="sorting" aria-controls="toptask" aria-label="Scenario" data-i18n="" scope="col">Scenario</th>
                              <th class="sorting" aria-controls="toptask" aria-label="Result" data-i18n="" scope="col">Result</th>
                              <th class="sorting" aria-controls="toptask" aria-label="Date" data-i18n="" scope="col">Date</th>
                            </tr>
                          </thead>
                          <tbody>
                        <?php foreach ($qry as $row) {
                          // echo "---<pre>";
                          // print_r($row);
                          // echo "</pre>";
                          // '"Test title"',
                          // '"Success Rate"',
                          // '"Scenario/Questions"',
                          // 'Date',
                          // '"# of Users"'

                          ?>
                            <tr>
                              <td><?=$row['Test title']?></td>
                              <td><?=$row['Scenario/Questions']?></td>
                              <td><?=percent($row['Success Rate'])?></td>
                              <td><?=date("Y-m-d", strtotime($row['Date']))?></td>
                            </tr>
                        <?php } ?>
                          </tbody>
                        </table>
                      </div>
                  <?php } ?>



             </div></div><div class="row"><div class="col-sm-12 col-md-5"></div><div class="col-sm-12 col-md-7"></div></div></div>
           </div>
         </div>
       </div>
     </div>

     <div class="row mb-4">
       <div class="col-lg-12 col-md-12">
         <div class="card">
           <div class="card-body pt-2">
             <h3 class="card-title"><span class="card-tooltip h6" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="right" data-bs-content="" data-bs-original-title="" title="" data-i18n="">Documents</span></h3>
                 <div>
                     <!-- <p>Start Date: <?=date("M d, Y", strtotime($prjStartDate[0]))?></p>
                     <p>Launch Date: <?=date("M d, Y", strtotime($prjLaunchDate[0]))?></p>
                     <p>Completed:</p>
                     <p>Year review:</p> -->
                 </div>
             </div></div><div class="row"><div class="col-sm-12 col-md-5"></div><div class="col-sm-12 col-md-7"></div></div>
           </div>
         </div>










     <?php
//     // AIRTABLE
//
//     $iso = 'Y-m-d\TH:i:s.v';
//
//     $previousWeekStart = strtotime("last sunday midnight", strtotime("-2 week +1 day"));
//     $previousWeekEnd = strtotime("next sunday", $previousWeekStart);
//     $previousWeekStart = date($iso, $previousWeekStart);
//     $previousWeekEnd = date($iso, $previousWeekEnd);
//
//     $weekStart = strtotime("last sunday midnight", strtotime("-1 week +1 day"));
//     $weekEnd = strtotime("next sunday", $weekStart);
//     $weekStart = date($iso, $weekStart);
//     $weekEnd = date($iso, $weekEnd);
//
//     $monthStart = (new DateTime("first day of last month midnight"))->format($iso);
//     $monthEnd = (new DateTime("first day of this month midnight"))->format($iso);
//
//     $previousMonthStart = (new DateTime("first day of -2 month midnight"))->format($iso);
//     $previousMonthEnd = $monthStart;
//
//
//     // Get date for GSC
//     $iso = 'Y-m-d';
//
//     $startLastGSC = (new DateTime($previousWeekStart))->format($iso);
//     $endLastGSC = (new DateTime($previousWeekEnd))->modify('-1 days')
//         ->format($iso);
//     $startGSC = (new DateTime($weekStart))->format($iso);
//     $endGSC = (new DateTime($weekEnd))->modify('-1 days')
//         ->format($iso);
//
//     $dates = [[$startLastGSC, $endLastGSC], [$startGSC, $endGSC]];
//
//     // Get date for header
//     $iso = 'M d';
//
//     $startLastHeader = (new DateTime($previousWeekStart))->format($iso);
//     $endLastHeader = (new DateTime($previousWeekEnd))->modify('-1 days')
//         ->format($iso);
//     $startHeader = (new DateTime($weekStart))->format($iso);
//     $endHeader = (new DateTime($weekEnd))->modify('-1 days')
//         ->format($iso);
//
//     // Weekly date ranges for the Header
//     $datesHeader = [[$startLastHeader, $endLastHeader], [$startHeader, $endHeader]];
//
//
//     $monthStartHeader = (new DateTime("first day of last month midnight"))->format($iso);
//     $monthEndHeader = (new DateTime("last day of last month midnight"))->format($iso);
//
//     $previousMonthStartHeader = (new DateTime("first day of -2 month midnight"))->format($iso);
//     $previousMonthEndHeader = (new DateTime("last day of -2 month midnight"))->format($iso);
//
//     // Monthly date ranges for the Header
//     $datesHeaderMonth = [[$previousMonthStartHeader, $previousMonthEndHeader], [$monthStartHeader, $monthEndHeader]];
//
//
//     // AIRTABLE CONNECTION - SETUP REUQEST AND PARSE RESPONSE
//     //--------------------------------------------------------------
//     $s = $startLastGSC;
//     $e = $endLastGSC;
//     $s1 = $startGSC;
//     $e1 = $endGSC;
//
//     $config = include ('./php/config-at.php');
//     $airtable = new Airtable($config['feedback']);
//
//     // -----------------------------------------------------------------------------------------------
//     // GET DATA FROM "Page Feedback" (CRA view) table filtered by date range - last two weekStart
//     // -----------------------------------------------------------------------------------------------
//
//     $listOfPages = array_map(fn($url) => "(URL = 'https://$url')", $taskPages);
//
//     $paramPages = implode(",", $listOfPages);
//
//     //echo $url;
//     $params = array(
//         // for get multiple url's or Projects from Airtable listOfPages
//         "filterByFormula" => "AND(IS_AFTER({Date}, DATEADD('$s',-1,'days')), IS_BEFORE({Date}, DATEADD('$e1',1,'days')), OR($paramPages))",
//         "view" => "CRA"
//     );
//     $table = 'Page feedback';
//
//     $fullArray = [];
//     $request = $airtable->getContent($table, $params);
//     do
//     {
//         $response = $request->getResponse();
//         $fullArray = array_merge($fullArray, ($response->records));
//     }
//     while ($request = $response->next());
//
//     $allData = ( json_decode(json_encode($fullArray), true));
//
//     $all_fields = array();
//
//     // if there's data (record exist)
//     if ( count( $allData ) > 0 ) {
//         $re = $allData;
//
//         //weekly data range
//         $rangeStartW = strtotime($s1);
//         $rangeEndW = strtotime($e1);
//         //previous week range
//         $rangeStartPW = strtotime($s);
//         $rangeEndPW = strtotime($e);
//
//         //filter array by date ranges
//         $WeeklyData = array_filter( $re, function($var) use ($rangeStartW, $rangeEndW) {
//             $utime = strtotime($var['fields']['Date']);
//             return $utime <= $rangeEndW && $utime >= $rangeStartW;
//         });
//
//         $PWeeklyData = array_filter( $re, function($var) use ($rangeStartPW, $rangeEndPW) {
//             $utime = strtotime($var['fields']['Date']);
//             return $utime <= $rangeEndPW && $utime >= $rangeStartPW;
//         });
//
//         if (( count( $WeeklyData ) > 0 ) && ( count( $PWeeklyData ) > 0 )) {
//
//             // Get just the ['fields'] array of each record -  as a separate array - $all_fields
//             $all_fields = array_column_recursive($WeeklyData, 'fields');
//             $all_fieldsPW = array_column_recursive($PWeeklyData, 'fields');
//
//             //we are grouping the pages by URL instead of Page Title, cause some pages might not have titles listes in the table
//             //stil, the main idea is to group the pages by some unique page element
//
//             foreach ( $all_fields as &$item ) {
//                 $item["Tag"] = implode($item['Lookup_tags']);
//             }
//
//             foreach ( $all_fieldsPW as &$item ) {
//                 $item["Tag"] = implode($item['Lookup_tags']);
//             }
//
//             $fieldsByGroupTag = group_by('Tag', $all_fields);
//             $fieldsByGroupTagPW = group_by('Tag', $all_fieldsPW);
//
//             foreach ( $fieldsByGroupTagPW as &$item ) {
//                 $item["Total tag comments"] = count($item);
//             }
//             foreach ( $fieldsByGroupTag as &$item ) {
//                 $item["Total tag comments"] = count($item);
//             }
//
//             $d3TotalFeedbackByPageSuccess = 1;
//
//         }
//     } else {
//         $d3TotalFeedbackByPageSuccess = 0;
//     }

    ?>


<?php //} ?>
<?php
//$endTime = microtime(true);

//$timeElapsed = round($endTime - $startTime, 2);

//echo "Page loaded in: $timeElapsed seconds";
?>
<!--Main content end-->
<?php include "includes/upd_footer.php"; ?>
