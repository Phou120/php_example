<?php

    echo 'fn.php<br>';

    function add($a, $b) {
        return $a + $b;
    }

    echo add(1, 4);
    echo add(3, 3) + add(4, 4);
    echo '<br>';
    echo '<hr>';
    // Set timezone to Asia/Vientiane (for Laos) or change to your preference
    date_default_timezone_set('Asia/Vientiane');
    echo date('Y-m-d H:i:s');

    echo '<hr>';

    // Lao month names
    $lao_months = [
        1 => 'ມັງກອນ',   // January
        2 => 'ກຸມພາ',     // February
        3 => 'ມີນາ',     // March
        4 => 'ເມສາ',     // April
        5 => 'ພຶດສະພາ', // May
        6 => 'ມິຖຸນາ',   // June
        7 => 'ກໍລະກົດ', // July
        8 => 'ສິງຫາ',   // August
        9 => 'ກັນຍາ',   // September
        10 => 'ຕຸລາ',    // October
        11 => 'ພະຈິກ',  // November
        12 => 'ທັນວາ'   // December
    ];

    // Get current day, month, and year
    $day = date('d');
    $month = (int)date('m'); // Convert to integer for array indexing
    $year = date('Y');

    // Display formatted date in Lao
    echo "ວັນທີ: {$day} {$lao_months[$month]} {$year}";


    echo '<hr>';

    function sayHi($message) {
        echo "Hi $message";
    }

    sayHi('World');
?>