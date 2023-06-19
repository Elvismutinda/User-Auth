<?php

require_once("../../config.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <form action="../classes/logout.php" method="POST">
        <h1>SUCCESSFUL LOGIN. WELCOME!!!</h1>
        <button type="submit" class="btn btn-primary">LOGOUT</button>
    </form>
</body>
</html>