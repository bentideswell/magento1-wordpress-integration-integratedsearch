<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_IntegratedSearch_Model_Observer
{
	/**
	 * Setup posts for the integrated search results
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function preparePostsForIntegratedSearchObserver(Varien_Event_Observer $observer)
	{
		$collection = $observer->getEvent()->getCollection();
		$excerptSize = $observer->getEvent()->getWrapperBlock()->getExcerptSize();

		foreach($collection->getItems() as $post) {
			if ($post->getData('post_excerpt')) {
				$excerpt = $post->getData('post_excerpt');
			}
			else if ($post->hasMoreTag()) {
				$excerpt = $post->getPostExcerpt();
			}
			else {
				$excerpt = $post->getPostContent();
			}

			$excerpt = strip_tags($excerpt);
			$words = explode(' ', $excerpt);
			
			if ($excerptSize && (count($words) < $excerptSize)) {
				$post->setPostExcerpt($excerpt);
			}
			else {
				$post->setPostExcerpt('<p>' . implode(' ', array_slice($words, 0, $excerptSize)) . '</p>');
			}
			
			$post->setTrueFeaturedImage($post->getFeaturedImage())
				->setFeaturedImage(false);
		}

		return $this;
	}
	
	/**
	 * Enable the search system integration for the Magento search
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function injectSearchSystemObserver(Varien_Event_Observer $observer)
	{
		$request = Mage::app()->getRequest();

		if ($request->getModuleName() === 'catalogsearch' && $request->getControllerName() === 'result') {
			if (Mage::getStoreConfigFlag('wordpress/integratedsearch/magento')) {
				Mage::getSingleton('core/layout')->getUpdate()->addHandle('catalogsearch_result_index_with_wordpress');
			}
		}
		
		return $this;
	}
}
