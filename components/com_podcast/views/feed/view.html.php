<?php
defined( '_JEXEC' ) or die;

jimport('joomla.application.component.view');
jimport('podcast.asset');

class PodcastViewFeed extends JView
{
	protected $items;
	protected $feed;

	public function display($tpl = null)
	{
		$this->feed = $this->get('Feed');

		if ($this->feed->published != 1) {
			throw new Exception(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 404);
		}

		parent::display($tpl);
	}
}