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

$key = "K";

for ($i = 1; $i<4; $i++) {
    $table->set(
        $key.$i,
        [
            "ID" => $i,
            "NAME" => "AA",
            "SCORE" => $i * 11.1
        ]
    );
}


// set name over size 
$table->set(
    $key . "OVERSIZE",
    [
        "ID" => 2,
        "NAME" => "A B C D E F G",
        "SCORE" => 22.2
    ]
);



    // remove key
    $rs = $table->del($key."2");
    var_dump($rs);

    // try to get key2, response false
    $data = $table->get($key . "2");
    var_dump($data);

    

for ($i = 1; $i < 4; $i++) {
    echo $i . "\n";
    // INCR
    $table->incr($key.$i, "SCORE", 3);

    // DECR
    $table->decr($key.$i,"ID", 1);
    // get key
    $data = $table->get($key . $i);
    var_dump($data);
}


