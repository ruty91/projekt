<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('task_queue', false, true, false, false);

$email= $_POST["e-mail"];
$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = $email;
$msg = new AMQPMessage($data,
                        array('delivery_mode' => 2) # make message persistent
                      );

$channel->basic_publish($msg, '', 'task_queue');

echo " Wyslano e-mail na adres: ", $data, "\n";


$channel->close();
$connection->close();
?>