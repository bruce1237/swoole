<?php

use Swoole\Table;


// create table
$table = new Table(64);

// define column
$table->column("ID", Table::TYPE_INT);
$table->column("NAME", Table::TYPE_STRING, 4);
$table->column("SCORE", Table::TYPE_FLOAT);

// create table
$table->create();

$key = "KEEY";

$table->set(
    $key,
    [
        "ID" => 1,
        "NAME" => "A",
        "SCORE" => 37.6
    ]
);

// set name over size 
$table->set(
    $key,
    [
        "ID" => 1,
        "NAME" => "A B C D E F G",
        "SCORE" => 37.6
    ]
);

$data = $table->get($key);

var_dump($data);
