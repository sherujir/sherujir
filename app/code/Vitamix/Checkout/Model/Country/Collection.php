<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vitamix\Checkout\Model\Country;

use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;

/**
 * Country Resource Collection
 *
 * @api
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Collection extends \Magento\Directory\Model\ResourceModel\Country\Collection
{

    /**
     * Convert collection items to select options array
     *
     * @param string|boolean $emptyLabel
     * @return array
     */
    public function toOptionArray($emptyLabel = ' ')
    {
        $options = $this->_toOptionArray('country_id', 'name', ['title' => 'iso2_code']);
        $sort = $this->getSort($options);

        $this->_arrayUtils->ksortMultibyte($sort, $this->_localeResolver->getLocale());
        foreach (array_reverse($this->_foregroundCountries) as $foregroundCountry) {
            $name = array_search($foregroundCountry, $sort);
            if ($name) {
                unset($sort[$name]);
                $sort = [$name => $foregroundCountry] + $sort;
            }
        }
        $isRegionVisible = (bool)$this->helperData->isShowNonRequiredState();

        $options = [];
        foreach ($sort as $label => $value) {
            $option = ['value' => $value, 'label' => $label];
            if ($this->helperData->isRegionRequired($value)) {
                $option['is_region_required'] = true;
            } else {
                $option['is_region_visible'] = $isRegionVisible;
            }
            if ($this->helperData->isZipCodeOptional($value)) {
                $option['is_zipcode_optional'] = true;
            }
            $options[] = $option;
        }
        if ($emptyLabel !== false && count($options) > 1) {
            array_unshift($options, ['value' => '', 'label' => $emptyLabel]);
        }

        $this->addDefaultCountryToOptions($options);

        return $options;
    }

    /**
     * Adds default country to options
     *
     * @param array $options
     * @return void
     */
    private function addDefaultCountryToOptions(array &$options)
    {
		//Get Object Manager Instance
       	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
     	//Load product by product id
       	$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $defaultCountry = [];
        foreach ($storeManager->getWebsites() as $website) {
            $defaultCountryConfig = $this->_scopeConfig->getValue(
                \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_COUNTRY,
                ScopeInterface::SCOPE_WEBSITES,
                $website
            );
            $defaultCountry[$defaultCountryConfig][] = $website->getId();
        }

        foreach ($options as $key => $option) {
            if (isset($defaultCountry[$option['value']])) {
                $options[$key]['is_default'] = !empty($defaultCountry[$option['value']]);
            }
        }
    }

    /**
     * Get sort
     *
     * @param array $options
     * @return array
     */
    private function getSort(array $options): array
    {
     	$writer = new \Zend\Log\Writer\Stream(BP.'/var/log/country.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('overrrided');
        //Get Object Manager Instance
       	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
     	//Load product by product id
       	$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
       	$websiteCode = strtoupper($storeManager->getWebsite()->getCode());
        $sort = [];
        //foreach ($options as $data) {
            $name = (string)$this->_localeLists->getCountryTranslation($websiteCode);
            if (!empty($name)) {
                $sort[$name] = $websiteCode;
            }
        //}
        $logger->info('Website Code :'.$websiteCode);
        return $sort;
    }
}
