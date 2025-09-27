<?php
if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $days = $diff->d;
        $weeks = floor($days / 7);
        $days = $days - ($weeks * 7);

        $string = [
            'y' => 'año',
            'm' => 'mes',
            'w' => 'semana',
            'd' => 'día',
            'h' => 'hora',
            'i' => 'minuto',
            's' => 'segundo',
        ];
        $result = [];
        foreach ($string as $k => $v) {
            if ($k == 'w' && $weeks) {
                $result[] = $weeks . ' ' . $v . ($weeks > 1 ? 's' : '');
            } elseif ($k == 'd' && $days) {
                $result[] = $days . ' ' . $v . ($days > 1 ? 's' : '');
            } elseif ($k != 'w' && $k != 'd' && $diff->$k) {
                $result[] = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            }
        }

        if (!$full) $result = array_slice($result, 0, 1);
        return $result ? 'hace ' . implode(', ', $result) : 'justo ahora';
    }
}
