<?php
include "Ws.php";

$ws = new Ws();
$ws->connect();
$ws->send("BBBC");
$ws->close();
$ws->start();