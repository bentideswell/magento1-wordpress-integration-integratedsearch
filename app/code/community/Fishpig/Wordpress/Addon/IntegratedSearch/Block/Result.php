<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_IntegratedSearch_Block_Result extends Fishpig_Wordpress_Block_Search_Result
{
	/**
	 *
	**/
	protected function _prepareLayout()
	{
		/*
		if ($this->isGroupPostTypes()) {
			$postTypes = Mage::helper('wordpress/app')->getPostTypes();

			foreach($postTypes as $type => $postType) {
				if ((int)$postType->getExcludeFromSearch() === 1) {
					unset($postTypes[$type]);
				}
				else if ((int)$postType->getPublic() !== 1) {
					unset($postTypes[$type]);
				}
			}
			
			if (count($postTypes) > 0) {
				Mage::app()->getRequest()->setParam('post_type', implode(',', array_keys($postTypes)));
			}
		}*/
		
		return parent::_prepareLayout();
	}

	/**
	 * Retrieve the tab data
	 *
	 * @return array
	 */
	public function getTabData()
	{
		if (!$this->hasTabData()) {
			$tabs = array(
					'product' => array(
					'alias' => 'product',
					'html' => trim($this->getChildHtml('search_result_list')),
					'title' => $this->__('Products'),
				),
				'blog' => array(
					'alias' => 'blog',
					'html' => trim($this->getPostListHtml()),
					'title' => $this->__('Posts'),
				),
			);
			
			$transportObject = new Varien_Object(array('tabs' => $tabs));
			
			Mage::dispatchEvent('wordpress_addon_integratedsearch_get_tabs', array('transport' => $transportObject, 'parsed_search_term' => $this->_getParsedSearchString(), 'block' => $this));

			$tabs = $transportObject->getTabs();

			$isFirst = false;
			
			foreach($tabs as $key => $tab) {
				$html = trim(preg_replace('/<p class="note-msg">.*<\/p>/Ui', '', $tab['html']));
				
				if ($html === '') {
					unset($tabs[$key]);
					continue;
				}
				
				$tabs[$key] = new Varien_Object($tab);
			}
			
			if (count($tabs) > 0) {
				$tabs = array_values($tabs);
				
				// Set first tab
				$tabs[0]->setIsFirst(true);
				
				$this->setTabData($tabs);
			}
			else {
				$this->setTabData(false);
			}
		}
		
		return $this->_getData('tab_data');
	}
	
	public function isGroupPostTypes()
	{
		return Mage::getStoreConfigFlag('wordpress/integratedsearch/group_post_types');
	}
}
