<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vitamix\Checkout\Plugin\Checkout\Address;

/**
 * Customers collection
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Collection extends \Magento\Customer\Model\ResourceModel\Address\Collection
{
    /**
     * Set customer filter
     *
     * @param \Magento\Customer\Model\Customer|array $customer
     * @return $this
     */
    public function setCustomerFilter($customer)
    {
    	//Get Object Manager Instance
       	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       	$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
       	$websiteCode = strtoupper($storeManager->getWebsite()->getCode());

        if (is_array($customer)) {
            $this->addAttributeToFilter('parent_id', ['in' => $customer]);
            $this->addAttributeToFilter('country_id',$websiteCode);
        } elseif ($customer->getId()) {
            $this->addAttributeToFilter('parent_id', $customer->getId());
            $this->addAttributeToFilter('country_id', $websiteCode);
        } else {
            $this->addAttributeToFilter('parent_id', '-1');
        }
        return $this;
    }
}
