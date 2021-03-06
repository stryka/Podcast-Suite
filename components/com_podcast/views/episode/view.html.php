<?php
/**
 * @author      Joseph LeBlanc - Cory Webb Media
 * @link        www.corywebbmedia.com
 * @copyright   Copyright 2012 Cory Webb Media. All Rights Reserved.
 * @category    cwm_podcast
 * @package
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view');
jimport('podcast.helper');

require JPATH_COMPONENT . '/scripthelper.php';

class PodcastViewEpisode extends JView
{
	protected $item;
	protected $assets;
	protected $asset;
	protected $storage;
	protected $params;

	public function display($tpl = null)
	{
		$this->item = $this->get('Item');
		$this->assets = $this->get('Assets');
		$this->asset = $this->assets[0];
		$this->storage = PodcastHelper::getStorage();
		$this->params = JFactory::getApplication()->getParams();

		if (!count($this->assets)) {
			print 'Error, no assets attached to this episode!';
		}

		if ($this->item->published == 0) {
			throw new Exception(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 404);
		}

		$pathway = JFactory::getApplication()->getPathway();
		$pathway->addItem($this->item->feed_title, JRoute::_('index.php?option=com_podcast&view=feed&feed_id='.$this->item->feed_id));
		$pathway->addItem($this->item->episode_title, JFactory::getUri()->toString());

		parent::display($tpl);
	}
}