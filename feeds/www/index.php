<?php

session_start();

$root = __DIR__ . '/../';

function message($text) {
    global $_SESSION;
    $_SESSION['messages'][] = $text;
}

require $root . 'data.php';

$db_name = 'scraper';
$collection_name = 'http://www.batoto.net/recent_rss';
$base_path = '/';
$item_limit = 50;

// Config overrides
@include $root . 'config.php';

$mongo = new Mongo();
$db = $mongo->selectDB($db_name);
$collection = $db->{$collection_name};

$base_url = 'http://' . $_SERVER['SERVER_NAME'] . $base_path;

$data = new Data($db, $collection_name);

$request_uri = $_SERVER['REQUEST_URI'];
$request_path = $request_uri;
if (strpos($request_uri, $base_path) == 0) {
    $request_path = substr($request_uri, strlen($base_path));
} else {
    $request_path = $request_uri;
}

$request_path_gibs = explode('/', $request_path);

$last_gib = strtolower(trim(end($request_path_gibs)));
if (Data::validate_profile_id($last_gib)) {
    $profile_id = $last_gib;
} else {
    $profile_id = NULL;
}
$feed_link = $base_url . 'feed/' . $profile_id;

$series = $data->get_series();

if ($request_path_gibs[0] == 'reset_history') {
    
    $_SESSION['history'] = array();
    message('History reset');
    $return = parse_url($_POST['return'], PHP_URL_PATH);
    header("Location: $return");
    return;
}
if ($request_path_gibs[0] == 'feed') {
    require $root . 'feed.php';
    return;
}
if ((count($request_path_gibs) > 1 || $request_path_gibs[0]) && !Data::validate_profile_id($request_path_gibs[0])) {
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/plain;charset=utf-8');
    echo 'Not found';
    return;
}
if (!empty($_POST)) {
    $update_profile_id = !empty($_POST['profile_id']) ? $_POST['profile_id'] : NULL;
    $update_profile_id = $data->update_profile($_POST, $update_profile_id);
    header('Location: ' . $base_path . $update_profile_id);
    return;
}

require $root . 'form.php';
