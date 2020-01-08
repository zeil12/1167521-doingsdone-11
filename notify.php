<?php
require_once ('vendor/autoload.php');
require_once ('helpers.php');
require_once ('functions.php');
require ('init.php');

$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");
$mailer = new Swift_Mailer($transport);

$sql = "SELECT * FROM user";
$result = mysqli_query($connect, $sql);

if ($result && mysqli_num_rows($result))
{
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($users as $user)
    {
        $sql = "SELECT * FROM task WHERE DATE(deadline) = DATE(NOW()) AND status = 0 AND user_id = " . $user['id'];
        $result = mysqli_query($connect, $sql);
        if ($result && mysqli_num_rows($result))
        {
            $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

        }

        $message = new Swift_Message();
        $message->setSubject("Уведомление от сервиса «Дела в порядке»");
        $message->setFrom(['keks@phpdemo.ru' => 'Дела в порядке']);
        $message->setBcc($user['email']);

        $msg_content = include_template('_notify.php', ['user' => $user, 'tasks' => $tasks,

        ]);
        $message->setBody($msg_content, 'text/plain');
        $result = $mailer->send($message);

        if ($result)
        {
            print ("Уведомления были отправлены");
        }
        else
        {
            print ("Не удалось отправить уведомления");
        }

    }
}
?>
