<?php
namespace Vitamix\Countryrestrsion\Plugin;
use Vitamix\Countryrestrsion\Model;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use function PHPUnit\Framework\assertIsCallable;

class LayoutProcessor
{

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$countryCollectionFactory = $objectManager->get('Magento\Directory\Model\ResourceModel\Country\CollectionFactory');
// Get country collection
$countryCollection = $countryCollectionFactory->create()->loadByStore();

        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'drop-down',
            ],
            'dataScope' => 'shippingAddress.country_id',
            'label' => __('Country1'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [],
            'id' => 'drop-down',
            'options' =>  $this->getCountryByWebsite(),

        ];

        //For Billing Form
        foreach ($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                 ['payment']['children']['payments-list']['children'] as $key => $payment) {
            if (isset($payment['children']['form-fields']['children']['city'])) {
                $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                ['country_id'] = [
                    'component' => 'Magento_Ui/js/form/element/select',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/select',
                        'id' => 'drop-down',
                    ],
                    'label' => __('Country1'),
                    'provider' => 'checkoutProvider',
                    'visible' => true,
                    'validation' => [],
                    'sortOrder' => 70,
                    'id' => 'drop-down',
                     'options' =>  $this->getCountryByWebsite(),
                ];
            }
        }

        return $result;

    }

   /* public function getCitiesDropdown()
    {
        return [['value' => "test1", "label" => "Test city 1", "is_default" => true], ['value' => "test2", "label" => "Test city 2", "is_default" => true]];
    }*/

}