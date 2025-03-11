<?php
// time_helpers.php
function timeAgo($timestamp) {
    // Create timezone object for Laos
    $laosTimezone = new DateTimeZone('Asia/Vientiane');

    // Create DateTime objects with Laos timezone
    $now = new DateTime('now', $laosTimezone);
    $past = new DateTime($timestamp, $laosTimezone);
    
    $diff = $now->diff($past);

    if ($diff->y > 0) return $diff->y . 'y';
    if ($diff->m > 0) return $diff->m . 'm';
    if ($diff->d > 0) return $diff->d . 'd';
    if ($diff->h > 0) return $diff->h . 'h';
    if ($diff->i > 0) return $diff->i . 'm';
    return $diff->s . 's';
}