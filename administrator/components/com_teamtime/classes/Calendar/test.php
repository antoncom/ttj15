<?php

require_once(dirname(__FILE__) . "/Event.php");

/*
  $it = new Calendar_EventIterator("2010-11-05 05:10", "2010-12-05", "weekly");
  print "\nUsual iterator\n";
  foreach ($it as $key => $value) {
  $it->log("$key, $value");
  }

  $it = new Calendar_EventIterator("2010-11-05 05:10", "2010-10-05", "weekly");
  print "\nUsual iterator\n";
  foreach ($it as $key => $value) {
  $it->log("$key, $value");
  }

  $days = array("mon"=>1, "wed"=>1, "sun"=>1);
  $it = new Calendar_EventWeekIterator("2010-12-05 05:10", "2010-12-25", "weekly", 1, $days);
  print "\nWeekdays iterator\n";
  foreach ($it as $key => $value) {
  $it->log("$key, $value");
  }

  $it = new Calendar_EventWeekIterator("2010-12-05 05:10", "2010-11-25", "weekly", 1, $days);
  print "\nWeekdays iterator\n";
  foreach ($it as $key => $value) {
  $it->log("$key, $value");
  }
 */

print "<pre>";

$params = new stdClass();

//$params->created = "2010-12-06 05:10";
$params->created = "2011-01-25 01:41:04";

$params->start_date = "2010-12-23 08:00:00";
$params->end_date = "2011-01-15 01:41:04";

$params->repeat_mode = "weekly";
$params->repeat_interval = 1;

//$params->repeat_mon = 1;
//$params->repeat_tue = 1;
//$params->repeat_sun = 1;

$repeated_event = new Calendar_Event($params);

var_dump($params->created);
var_dump($params->start_date . " - " . $params->end_date);
var_dump($repeated_event->generate_dates());
