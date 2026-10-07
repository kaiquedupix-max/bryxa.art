<?php
const BRYXA_API=true;$action='qa';$method='GET';$db=new PDO('sqlite::memory:');$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$db->exec('CREATE TABLE orders(id TEXT PRIMARY KEY,data TEXT,created TEXT);CREATE TABLE admins(id INTEGER PRIMARY KEY,email TEXT,password TEXT);');
$docs=['settings'=>['domain'=>'bryxa.art'],'products'=>[['id'=>'p1','stock'=>['G'=>9]]],'integrations'=>[]];
function loadDoc($n){global $docs;return $docs[$n]??[];}function saveDoc($n,$v){global $docs;$docs[$n]=$v;}function fail($message,$status=400){throw new RuntimeException($message,$status);}function textValue($v,$max=200){return $v;}
require __DIR__.'/../public/api/notifications.php';require __DIR__.'/../public/api/payments.php';
$o=['id'=>'QA-PAYMENT','total'=>100,'sandbox'=>true,'payment_status'=>'not_created','status'=>'Aguardando pagamento','customer'=>['name'=>'QA','email'=>'qa@example.com'],'items'=>[['id'=>'p1','size'=>'G','quantity'=>1]],'reserved_stock'=>true,'timeline'=>[],'access_link'=>'https://bryxa.art/acompanhar/#qa'];$db->prepare('INSERT INTO orders VALUES(?,?,?)')->execute([$o['id'],json_encode($o),date('c')]);$payment=['id'=>'123','external_reference'=>'QA-PAYMENT','transaction_amount'=>100,'currency_id'=>'BRL','live_mode'=>false,'status'=>'approved'];
function check($v){if(!$v)throw new RuntimeException('QA failed');}
$bad=$payment;$bad['transaction_amount']=1;try{paymentUpdate($bad);throw new RuntimeException('Tampered amount accepted');}catch(RuntimeException $e){check($e->getCode()===409);}
$bad=$payment;$bad['live_mode']=true;try{paymentUpdate($bad);throw new RuntimeException('Wrong environment accepted');}catch(RuntimeException $e){check($e->getCode()===409);}
$approved=paymentUpdate($payment);check($approved['payment_status']==='approved'&&count($approved['timeline'])===1);paymentUpdate($payment);check((int)$db->query('SELECT COUNT(*) FROM email_outbox')->fetchColumn()===1);$bad=$payment;$bad['id']='other';try{paymentUpdate($bad);throw new RuntimeException('Wrong payment accepted');}catch(RuntimeException $e){check($e->getCode()===409);}
echo 'PASS: confirmed payment reconciliation, amount/environment/id checks, idempotent webhook updates and notification deduplication.';

