<?php
defined( '_JEXEC' ) or die;

class PodcastTableFeed extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__podcast_feed', 'feed_id', $db);
	}
}