<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('task_queue', false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
$callback = function($msg){
  echo " [x] Wysylam e-mail na adres: ", $msg->body, "\n";
  sleep(10);
  $to      =  $msg->body;
  $subject = 'the subject';
  $message = 'dziala';
  $headers = 'From: rutyubuntu@gmail.com' . "\r\n" .
     'Reply-To: rutyubuntu@gmail.com' . "\r\n" .
     'X-Mailer: PHP/' . phpversion();
  mail($to, $subject, $message, $headers);

  echo " [x] Wyslano e-mail na adres: ", $msg->body, "\n";
  $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>



