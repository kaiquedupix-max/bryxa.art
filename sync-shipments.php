<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$_GET['action']='internal-shipment-sync';$_SERVER['REQUEST_METHOD']='GET';
require __DIR__.'/public/api/index.php';
