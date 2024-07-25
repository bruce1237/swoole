<?php


$taskListA = [
    "taskA",
    "taskB",
    "taskC",
    "taskD",
];

$taskListB = [
    "taskW",
    "taskX",
    "taskY",
    "taskZ",
];

$taskListC = [
    "taskListA",
    "taskListB",
];

foreach($taskListC as $taskListName){
    var_dump($$taskListName);
}