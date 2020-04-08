<?php
require_once('./config.php');
require_once('./HabboServers.class.php');

$force = new HabboServers();
if ($force->hasVoted()) {
    echo 'Você já votou!';
} else {
    $force->goVote();
}
?>

<head>
    <title>
        Teste API
    </title>
</head>

<body>
    <h4>Hello User</h4>
</body>