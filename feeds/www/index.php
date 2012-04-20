<?php

$root = __DIR__ . '/../';

require $root . 'data.php';

// "http://www.batoto.net/recent_rss" encoded as base16
$db_name = '687474703A2F2F6C6F63616C686F73742F726563656E745F727373';
$base_path = '/';

// Config overrides
@include $root . 'config.php';

$mongo = new Mongo();
$db = $mongo->selectDB($db_name);

$data = new Data($db);

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

$series = $data->get_series();

if ($request_path_gibs[0] == 'feed') {
    require $root . 'feed.php';
    return;
}
if (!empty($_POST)) {
    $update_profile_id = !empty($_POST['profile_id']) ? $_POST['profile_id'] : NULL;
    $update_profile_id = $data->update_profile($_POST['subscriptions'], $update_profile_id);
    header('Location: ' . $base_path . $update_profile_id);
    return;
}

require $root . 'form.php';
