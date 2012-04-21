<?php
$subscriptions = $data->get_subscriptions($profile_id);
$subscriptions_series = $subscriptions['series'] ? $subscriptions['series'] : array();
$subscriptions_languages = $subscriptions['languages'] ? $subscriptions['languages'] : array();
$series = $data->get_series();
$languages = $data->get_languages();
if ($profile_id) {
    $subscribe_label = 'Update subscriptions';
} else {
    $subscribe_label = 'Subscribe';
}
$series_count = count($series);
$languages_count = count($languages);

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = array();
}
if ($profile_id) {
    $_SESSION['history'][$profile_id] = $subscriptions['title'];
}
$history = $_SESSION['history'];
?>
<!doctype HTML>
<html>
    <head>
        <title>Batoto scraper</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>
        <div id="wrapper">
            <h1>Batoto scraper</h1>
            <form action="<?php echo $base_path; ?>" method="post" class="scraper-form">
                <?php if ($profile_id) : ?>
                    <p>Subscription profile &lt;<a href="<?php echo $feed_link; ?>" title="RSS feed"><?php echo $profile_id; ?></a>&gt;</p>
                    <p>
                        <label for="feed-link">Unique feed link</label> 
                        <input id="feed-link" type="text" readonly="readonly" value="<?php echo $feed_link ?>" size="<?php echo strlen($feed_link); ?>">
                    </p>
                <?php endif; ?>
                <p class="form-title">
                    <label for="form-title">Profile title</label>
                    <input id="form-title" name="title" type="text" value="<?php echo htmlspecialchars($subscriptions['title']); ?>">
                </p>
                <p class="form-series">
                    <label for="form-series"><?php
                if ($profile_id) {
                    echo count($subscriptions_series) . '/';
                }
                echo $series_count;
                ?> series</label>
                    <select id="form-series" name="series[]" multiple="multiple" size="<?php echo min(20, $series_count); ?>">
                        <?php
                        foreach ($series as $series_title) :
                            $selected = in_array($series_title, $subscriptions_series) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo htmlspecialchars($series_title); ?>"<?php echo $selected; ?>>
                                <?php echo htmlspecialchars($series_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($profile_id) : ?>
                        <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    <?php endif; ?>
                </p>
                <p class="form-languages">
                    <label for="form-languages"><?php
                    if ($profile_id) {
                        echo count($subscriptions_languages) . '/';
                    }
                    echo $languages_count;
                    ?> languages</label>
                    <select id="form-languages" name="languages[]" multiple="multiple" size="<?php echo min(10, $languages_count); ?>">
                        <?php
                        foreach ($languages as $language_title) :
                            $selected = in_array($language_title, $subscriptions_languages) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo htmlspecialchars($language_title); ?>"<?php echo $selected; ?>>
                                <?php echo htmlspecialchars($language_title); ?>
                            </option>
                        <? endforeach; ?>
                    </select>
                    <?php if ($profile_id) : ?>
                        <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                    <?php endif; ?>
                </p>
                <p>
                    <input id="save" type="submit" value="<?php echo $subscribe_label; ?>">
                    <?php if ($profile_id) : ?>
                        <a href="<?php echo $base_path; ?>">New profile</a>
                    <?php endif; ?>
                </p>
                <?php
                if (!empty($_SESSION['messages'])) {
                    foreach ($_SESSION['messages'] as $text) {
                        echo "<p class='message'><strong>$text</strong></p>";
                    }
                    unset($_SESSION['messages']);
                }
                ?>
            </form>
            <h2>Info</h2>
            <div class="tips">
                <p>Tip: Hold the <kbd>Ctrl</kbd>key while clicking to select or unselect multiple items in the lists.</p>
                <p>This is a tool to filter <a href="http://batoto.net/">Batoto</a> RSS feeds by series or languages. It creates unique links for subscription profiles that can be used to follow or update the subscriptions.</a>
            </div>
            <?php if (!empty($history)) : ?>
                <div class="history">
                    <h2>Recently visited profiles</h2>
                    <ul>
                        <?php foreach ($history as $id => $title) : ?>
                            <li><a href="<?php echo $base_path . $id; ?>"><?php
                    if ($title) {
                        echo $title . ' ';
                    }
                    echo "&lt;$id>";
                            ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <form action="<?php echo $base_path; ?>reset_history" method="post">
                        <p>
                            <input type="submit" value="Reset history">
                            <input name="return" type="hidden" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        </p>
                    </form>
                </div>
            <?php endif; ?>
            <h2>Links</h2>
            <div id="footer">
                <ul>
                    <li><a href="https://github.com/slikts/batoto-scraper" title="Source code">github.com/slikts/batoto-scraper</a></li>
                    <li><a href="http://untu.ms/" title="Author home">../untu.ms</a></li>
                </ul>
                <p class="version">v0.0001</p>
            </div>
        </div>
    </body>
</html>