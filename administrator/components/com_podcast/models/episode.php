<?php
/**
 * @author      Joseph LeBlanc - Cory Webb Media
 * @link        www.corywebbmedia.com
 * @copyright   Copyright 2012 Cory Webb Media. All Rights Reserved.
 * @category    cwm_podcast
 * @package
 */
defined( '_JEXEC' ) or die;

jimport('joomla.application.component.modeladmin');

class PodcastModelEpisode extends JModelAdmin
{
	public function getTable($type = 'Episode', $prefix = 'PodcastTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_podcast.episode', 'episode', array('control' => 'jform', 'load_data' => $loadData));

		return $form;
	}

    public function getAssets()
    {
		$item = $this->getItem();

		if ($item) {
			$db = JFactory::getDBO();
	        $query = $db->getQuery(true);
	        $query->select('tbl.*, m.*')
	                ->from('#__podcast_assets AS tbl')
	                ->join('LEFT', '#__podcast_assets_map AS m ON tbl.podcast_asset_id = m.podcast_asset_id');
	        $query->where('m.episode_id = '.$item->episode_id);
	        $db->setQuery($query);

	        return $db->loadObjectList();
		}

		return array();
    }

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_podcast.edit.episode.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function validate($form, $data, $group = null)
	{
		$data = parent::validate($form, $data, $group);

		$asset_ids = JRequest::getVar('asset_ids', '');

		if (!$asset_ids)
		{
			$data['published'] = '0';
			JFactory::getApplication()->enqueueMessage(JText::_('COM_PODCAST_EPISODE_CANNOT_PUBLISH_NO_MEDIA'));
		}

		return $data;
	}
}