<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
</head>

<body>
    <?php
    $date = date('Y-m-d H:i:s');

    $ip = $_SERVER['REMOTE_ADDR'];
    file_put_contents('log.txt', $ip.''.$date.PHP_EOL, FILE_APPEND);
    echo $date;

    $color = ['yellow', 13, true];
    echo $color[0];
    echo $color[1];

    var_dump($color);
    var_dump($date);

    print_r($color);
    ?>

</body>

</html>