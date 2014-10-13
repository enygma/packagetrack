<?xml version="1.0"?>
<rss version="2.0">
	<channel>
		<title>Package Scan: PHP Package Tracking</title>
		<link>http://packagescan.io</link>
		<description>Composer.lock feed: <?php echo $hash; ?></description>
		<language>en-us</language>
		<pubDate><?php echo date('r'); ?></pubDate>
		<ttl>30</ttl>
		<?php foreach ($items as $item): ?>
		<item>
			<title><![CDATA[<?php echo $item['name']; ?> (<?php echo $item['version']; ?>)]]></title>
			<guid><?php echo $item['source']; ?></guid>
			<link><?php echo $item['source']; ?></link>
			<description><![CDATA[<?php echo $item['description']; ?>]]></description>
			<pubDate><?php echo $item['date_posted']; ?> UTC</pubDate>
		</item>
		<?php endforeach; ?>
	</channel>
</rss>