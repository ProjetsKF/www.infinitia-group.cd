<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'formations.php';

function infinitia_start_session()
{
    if (session_id() === '') {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        if ($secure) {
            ini_set('session.cookie_secure', '1');
        }
        session_start();
    }
}

function infinitia_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function infinitia_csrf_token()
{
    infinitia_start_session();
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || strlen($_SESSION['csrf_token']) < 32) {
        if (function_exists('random_bytes')) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $_SESSION['csrf_token'] = hash('sha256', uniqid(mt_rand(), true));
        }
    }
    return $_SESSION['csrf_token'];
}

function infinitia_csrf_is_valid($token)
{
    infinitia_start_session();
    return is_string($token) && isset($_SESSION['csrf_token']) && is_string($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function infinitia_post_value($key)
{
    return isset($_POST[$key]) && !is_array($_POST[$key]) ? trim($_POST[$key]) : '';
}

function infinitia_request_method()
{
    return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
}

function infinitia_strlen($value)
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function infinitia_valid_id($value)
{
    return ctype_digit((string) $value) && (int) $value > 0;
}

function infinitia_database_ready($conn)
{
    return $conn instanceof mysqli;
}

function infinitia_table_exists($conn, $table)
{
    if (!infinitia_database_ready($conn)) {
        return false;
    }
    $safe_table = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '" . $safe_table . "'");
    return $result && mysqli_num_rows($result) > 0;
}

function infinitia_get_training_courses($conn, $active_only)
{
    $courses = array();
    if (infinitia_table_exists($conn, 'training_courses')) {
        $sql = 'SELECT id, code, name, category, short_description, detailed_content, duration, price, price_unit, is_active, display_order, created_at, updated_at FROM training_courses';
        if ($active_only) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY display_order ASC, id ASC';
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['is_active'] = (int) $row['is_active'];
                $row['display_order'] = (int) $row['display_order'];
                $courses[] = $row;
            }
        }
        return $courses;
    }

    foreach (infinitia_training_course_fallbacks() as $course) {
        if (!$active_only || (int) $course['is_active'] === 1) {
            $courses[] = $course;
        }
    }
    return $courses;
}

function infinitia_initialize_training_catalog($conn)
{
    $GLOBALS['infinitia_training_courses_by_code'] = array();
    foreach (infinitia_get_training_courses($conn, false) as $course) {
        $GLOBALS['infinitia_training_courses_by_code'][$course['code']] = $course;
    }
}

function infinitia_training_course($code)
{
    return isset($GLOBALS['infinitia_training_courses_by_code'][$code]) ? $GLOBALS['infinitia_training_courses_by_code'][$code] : null;
}

function infinitia_training_is_active($code)
{
    $course = infinitia_training_course($code);
    return $course !== null && (int) $course['is_active'] === 1;
}

function infinitia_format_training_price($price, $price_unit)
{
    if ($price === null || $price === '') {
        return 'Sur demande';
    }
    $number = (float) $price;
    $formatted = floor($number) == $number ? number_format($number, 0, ',', ' ') : number_format($number, 2, ',', ' ');
    return $formatted . ' $' . ($price_unit !== null && trim($price_unit) !== '' ? ' ' . trim($price_unit) : '');
}

function infinitia_training_category_icon($category)
{
    $icons = array(
        'Informatique & Bureautique' => 'bi-pc-display-horizontal',
        'Maintenance informatique' => 'bi-tools',
        'Réseaux' => 'bi-diagram-3',
        'Cybersécurité' => 'bi-shield-lock',
        'Bases de données' => 'bi-database',
        'Administration systèmes' => 'bi-server',
        'Vidéosurveillance' => 'bi-camera-video'
    );
    return isset($icons[$category]) ? $icons[$category] : 'bi-mortarboard';
}

function infinitia_session_is_available($status, $capacity, $accepted_count, $start_date, $end_date)
{
    if ($status !== 'open') {
        return false;
    }
    $today = date('Y-m-d');
    if ($start_date === null || $start_date === '') {
        return false;
    }
    if ($end_date !== null && $end_date !== '' && $end_date < $today) {
        return false;
    }
    if (($end_date === null || $end_date === '') && $start_date < $today) {
        return false;
    }
    $remaining_places = infinitia_remaining_places($capacity, $accepted_count);
    return $remaining_places === null || $remaining_places > 0;
}

function infinitia_remaining_places($capacity, $accepted_count)
{
    if ($capacity === null || (int) $capacity <= 0) {
        return null;
    }
    return max(0, (int) $capacity - max(0, (int) $accepted_count));
}

function infinitia_remaining_places_label($remaining_places)
{
    if ($remaining_places === null) {
        return '';
    }
    return $remaining_places === 1 ? '1 place restante' : $remaining_places . ' places restantes';
}

function infinitia_format_date_range($start_date, $end_date)
{
    if ($start_date === null || $start_date === '') {
        return 'Date à préciser';
    }
    $start = date('d/m/Y', strtotime($start_date));
    if ($end_date === null || $end_date === '' || $end_date === $start_date) {
        return $start;
    }
    return $start . ' – ' . date('d/m/Y', strtotime($end_date));
}

function infinitia_get_open_training_sessions($conn)
{
    $sessions = array();
    if (!infinitia_table_exists($conn, 'training_sessions') || !infinitia_table_exists($conn, 'training_registrations')) {
        return $sessions;
    }

    $sql = "SELECT s.id, s.formation_code, s.start_date, s.end_date, s.schedule, s.location, s.capacity, s.status,
                   SUM(CASE WHEN r.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_count
            FROM training_sessions s
            LEFT JOIN training_registrations r ON r.session_id = s.id
            WHERE s.status = 'open'
              AND ((s.end_date IS NOT NULL AND s.end_date >= CURDATE()) OR (s.end_date IS NULL AND s.start_date >= CURDATE()))
            GROUP BY s.id, s.formation_code, s.start_date, s.end_date, s.schedule, s.location, s.capacity, s.status
            ORDER BY s.start_date ASC, s.id ASC";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return $sessions;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $row['accepted_count'] = (int) $row['accepted_count'];
        $row['available'] = infinitia_session_is_available($row['status'], $row['capacity'], $row['accepted_count'], $row['start_date'], $row['end_date']);
        if (infinitia_training_name($row['formation_code']) !== '' && infinitia_training_is_active($row['formation_code'])) {
            $sessions[] = $row;
        }
    }
    return $sessions;
}

function infinitia_group_sessions_by_training($sessions)
{
    $grouped = array();
    foreach ($sessions as $session) {
        $code = $session['formation_code'];
        if (!isset($grouped[$code])) {
            $grouped[$code] = array();
        }
        $grouped[$code][] = $session;
    }
    return $grouped;
}

function infinitia_filter_available_sessions($sessions)
{
    $available_sessions = array();
    foreach ($sessions as $session) {
        if (!empty($session['available'])) {
            $available_sessions[] = $session;
        }
    }
    return $available_sessions;
}

function infinitia_training_action_html($formation_code, $sessions_by_training)
{
    if (isset($sessions_by_training[$formation_code]) && count($sessions_by_training[$formation_code]) > 0) {
        $selected_session = null;
        foreach ($sessions_by_training[$formation_code] as $session) {
            if (!empty($session['available'])) {
                $selected_session = $session;
                break;
            }
        }

        if ($selected_session !== null) {
            $remaining_places = infinitia_remaining_places($selected_session['capacity'], $selected_session['accepted_count']);
            $html = '';
            if ($remaining_places !== null) {
                $html .= '<div class="training-remaining"><i class="bi bi-people"></i> ' . infinitia_h(infinitia_remaining_places_label($remaining_places)) . '</div>';
            }
            $html .= '<a class="training-register-link" href="inscription-formation.php?session=' . (int) $selected_session['id'] . '">S’inscrire <i class="bi bi-arrow-right"></i></a>';
            return $html;
        }

        return '<div class="training-session-full"><i class="bi bi-person-x"></i> Session complète</div>';
    }
    return '<div class="training-unavailable"><p>Aucune session n’est actuellement ouverte pour cette formation.</p><a href="contact.php">Être informé de la prochaine session</a></div>';
}
