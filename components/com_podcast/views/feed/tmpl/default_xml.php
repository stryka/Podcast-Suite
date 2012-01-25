<?php
defined( '_JEXEC' ) or die;

$document = JFactory::getDocument();
$document->setMimeEncoding('application/xml');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
	<channel>
		<title><?php echo $this->escape($this->feed->feed_title) ?></title>
		<link><?php echo $this->escape($this->feed->feed_link) ?></link>
		<language><?php echo $this->escape($this->feed->feed_language) ?></language>
		<copyright><?php echo $this->escape($this->feed->feed_copyright) ?></copyright>
		<itunes:subtitle><?php echo $this->escape($this->feed->feed_subtitle) ?></itunes:subtitle>
		<itunes:author><?php echo $this->escape($this->feed->feed_author) ?></itunes:author>
		<itunes:summary><?php echo $this->escape($this->feed->feed_summary) ?></itunes:summary>
		<description><?php echo $this->escape($this->feed->feed_summary) ?></description>
		<itunes:owner>
			<itunes:name><?php echo $this->escape($this->feed->feed_owner_name) ?></itunes:name>
			<itunes:email><?php echo $this->escape($this->feed->feed_owner_email) ?></itunes:email>
		</itunes:owner>
<?php if ($this->feed->feed_new_feed_url): ?>
		<itunes:new-feed-url><?php echo $this->escape($this->feed->feed_new_feed_url) ?></itunes:new-feed-url>
<?php endif ?>
<?php if ($this->feed->feed_block): ?>
		<itunes:block>yes</itunes:block>
<?php endif ?>
<?php if ($this->feed->feed_complete): ?>
		<itunes:complete>yes</itunes:complete>
<?php endif ?>
		<itunes:image href="<?php echo $this->escape($this->feed->feed_image) ?>"/>
<?php foreach ($this->items as $item): ?>
		<item>
			<title><?php echo $this->escape($item->item_title) ?></title>
			<itunes:author><?php echo $this->escape($item->item_author) ?></itunes:author>
			<itunes:subtitle><?php echo $this->escape($item->item_subtitle) ?></itunes:subtitle>
			<itunes:summary><?php echo $this->escape($item->item_summary) ?></itunes:summary>
			<description><?php echo $this->escape($item->item_summary) ?></description>
			<itunes:image href="<?php echo $this->escape($item->item_summary) ?>"/>
			<enclosure url="<?php echo $this->escape($item->item_enclosure_url) ?>" length="<?php echo $this->escape($item->item_enclosure_length) ?>" type="<?php echo $this->escape($item->item_enclosure_type) ?>"/>
			<guid><?php echo $this->escape($item->item_guid) ?></guid>
			<pubDate><?php echo date('r', strtotime($item->item_pubDate)) ?></pubDate>
			<itunes:duration><?php echo $this->escape($item->item_duration) ?></itunes:duration>
			<itunes:keywords><?php echo $this->escape($item->item_keywords) ?></itunes:keywords>
<?php if ($this->item->item_isClosedCaptioned): ?>
			<itunes:isClosedCaptioned>yes</itunes:isClosedCaptioned>
<?php endif ?>
		</item>
<?php endforeach ?>
	</channel>
</rss>
