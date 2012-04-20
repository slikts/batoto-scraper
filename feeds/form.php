<?php
$subscriptions = $data->get_subscriptions($profile_id);
if ($profile_id) {
    $feed_link = $base_path . 'feed/' . $profile_id;
}
?>
<!doctype HTML>
<body>
<head>
    <title>Batoto scraper</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <form action="<?php echo $base_path; ?>" method="post">
        <?php if ($profile_id) : ?>
            <p>Subscription profile &lt;<a href="<?php echo $feed_link; ?>"><?php echo $profile_id; ?></a>&gt;</p>
        <?php endif; ?>
        <p>
            <label for="subscriptions"><?php echo count($series); ?> series</label>
            <select id="subscriptions" name="subscriptions[]" multiple="multiple" size="20">
                <?php
                foreach ($series as $title) :
                    $selected = in_array($title, $subscriptions) ? ' selected="selected"' : '';
                    ?>
                    <option value="<?php echo htmlspecialchars($title); ?>"<?php echo $selected; ?>>
                        <?php echo htmlspecialchars($title); ?>
                    </option>
                <? endforeach; ?>
            </select>
            <?php if ($profile_id) : ?>
                <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
            <?php endif; ?>
        </p>
        <p>
            <input id="save" type="submit" value="Subscribe">
        </p>
    </form>
</body>
</body>
