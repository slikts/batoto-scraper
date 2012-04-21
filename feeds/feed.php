<?php
$feed_title = htmlspecialchars("Batoto scraper profile <{$profile_id}>");
$feed_link = 'http://' . $_SERVER['SERVER_NAME'] . $base_path . 'feed/' . $profile_id;
$items = $data->get_items($profile_id, $item_limit);
?>
<?php echo '<?xml version="1.0" encoding="utf-8" ?>' . "\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><![CDATA[ <?php echo $feed_title; ?> ]]>
        </title>
        <link><?php echo $feed_link; ?></link>
        <atom:link href="<?php echo $feed_link; ?>" rel="self" type="application/rss+xml" />
        <description><![CDATA[Latest updates from Batoto]]></description>
        <?php /*
          <image>
          <title><![CDATA[]]>
          </title>
          <link></link>
          <url></url>
          </image>
         */ ?>

        <?php foreach ($items as $item) : ?>
                
        <item>
            <title><![CDATA[<?php echo htmlspecialchars($item['title']); ?>]]>
            </title>
            <link><?php echo $item['_id']; ?></link>
            <guid><?php echo $item['_id']; ?></guid>
            <pubDate><?php echo date(DateTime::RFC822, $item['date']->sec); ?></pubDate>
            <description><![CDATA[<?php echo htmlspecialchars($item['title']); ?>]]>
            </description>
        </item>
        <?php endforeach; ?>
    </channel>
</rss>
