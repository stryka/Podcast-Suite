<?php
/**
 * @author      Joseph LeBlanc - Cory Webb Media
 * @link        www.corywebbmedia.com
 * @copyright   Copyright 2012 Cory Webb Media. All Rights Reserved.
 * @category    cwm_podcast
 * @package
 */
defined( '_JEXEC' ) or die;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>
<form action="<?php echo JRoute::_('index.php?option=com_podcast&view=feeds'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_PODCAST_SEARCH_FEEDS'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => false, 'trash' => false, 'all' => false)), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
		</div>

	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_PODCAST_FEED_TITLE', 'feed_title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_PODCAST_FEED_SUMMARY', 'feed_summary', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_PODCAST_EPISODE_COUNT', 'item_count', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_PODCAST_FEED_CREATED', 'feed_created', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JText::_('COM_PODCAST_FIELD_SHOW_SUBSCRIBE'); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort', 'JENABLED', 'published', $listDirn, $listOrder); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort', 'COM_PODCAST_FEED_DEFAULT', 'feed_default', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="20">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->feed_id); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_podcast&task=feed.edit&feed_id='. $item->feed_id); ?>"><?php echo $this->escape($item->feed_title) ?></a>
				</td>
				<td>
					<?php echo $this->escape($item->feed_summary) ?>
				</td>
				<td class="center">
					<a href="<?php echo JRoute::_('index.php?option=com_podcast&view=episodes&filter_feed='.$item->feed_id); ?>"><?php echo $this->escape($item->item_count) ?> <?php echo JText::_('COM_PODCAST_FEEDS_VIEW'); ?> &raquo;</a>
				</td>
				<td>
					<?php echo JHTML::_('date', $item->feed_created) ?>
				</td>
				<td>
					<a href="<?php echo str_replace('/administrator', '', JURI::current()); ?>?option=com_podcast&view=feed&format=raw&feed_id=<?php echo $item->feed_id; ?>"><?php echo JText::_('COM_PODCAST_FEEDS_VIEW'); ?> &raquo;</a>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'feeds.'); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.isdefault', $item->feed_default, $i, 'feeds.', !$item->feed_default); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div id="podcast_suite_link">
	<p><?php echo JText::_('COM_PODCAST_COPYRIGHT'); ?></p>
</div>