<?php
require __DIR__ . '/../public/api/smtp.php';
function check($value, $message) { if (!$value) throw new RuntimeException($message); }
$db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db->exec('CREATE TABLE email_outbox(id INTEGER PRIMARY KEY,recipient TEXT,subject TEXT,plain TEXT,html TEXT,status TEXT,expires_at INTEGER,attempted_at INTEGER,sent_at TEXT,last_error TEXT)');
$insert = $db->prepare('INSERT INTO email_outbox VALUES(?,?,?,?,?,?,?,?,?,?)');
foreach ([1=>'Pendente',2=>'Pendente',3=>'Aguardando plataforma de e-mail',4=>'Pendente',5=>'Enviando'] as $id=>$status) $insert->execute([$id,'test@example.test','Olá BRYXA','Pedido recebido','<p>Pedido recebido</p>',$status,$id===2?time()-1:time()+60,null,null,'']);
$sent=[];
smtpProcess($db,function($mail)use(&$sent){if($mail['id']===4)throw new RuntimeException('secret must not escape');$sent[]=$mail['id'];});
check($sent===[1],'Only new, valid queued messages must be sent');
check($db->query('SELECT status FROM email_outbox WHERE id=1')->fetchColumn()==='Enviado','Success recorded');
check($db->query('SELECT status FROM email_outbox WHERE id=2')->fetchColumn()==='Expirado','Expired reset not sent');
check($db->query('SELECT status FROM email_outbox WHERE id=4')->fetchColumn()==='Falha no envio','Failure recorded');
check(!str_contains($db->query('SELECT last_error FROM email_outbox WHERE id=4')->fetchColumn(),'secret'),'No credential leak');
check(smtpProcess($db,function(){throw new RuntimeException('Duplicate');})===0,'No duplicate or ambiguous retry');
$mail=['id'=>1,'recipient'=>'test@example.test','subject'=>"Olá\r\nBcc: evil@example.test",'plain'=>'Mensagem','html'=>'<p>Mensagem</p>'];
$s=['from'=>'contato@bryxa.com.br','user'=>'contato@bryxa.com.br','name'=>'BRYXA'];
$message=smtpMessage($mail,$s);
check(str_contains($message,'multipart/alternative'),'Multipart format');
check(!str_contains($message,"\r\nBcc:"),'Subject header injection prevented');
try{$mail['recipient']="test@example.test\r\nBcc: evil@example.test";smtpMessage($mail,$s);throw new LogicException('Address accepted');}catch(RuntimeException $e){}
echo "Email queue, expiry, deduplication, failure isolation and MIME checks passed.\n";
