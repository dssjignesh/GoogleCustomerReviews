<?php

declare(strict_types=1);
/**
 * Digit Software Solutions.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category  Dss
 * @package   Dss_GoogleReviews
 * @author    Extension Team
 * @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
 */
namespace Dss\GoogleReviews\Block;

use Magento\Sales\Model\Order;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Dss\GoogleReviews\Helper\Config as ConfigHelper;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;

class Survey extends Template
{
    /**
     * Number of survey data fields
     */
    private const SURVEY_DATA_FIELDS_COUNT = 6;

    /** @var array */
    private $surveyData = [];

    /**
     * @param Template\Context $context
     * @param ConfigHelper $configHelper
     * @param DateTime $date
     * @param OrderCollectionFactory $salesOrderCollectionFactory
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private ConfigHelper $configHelper,
        private DateTime $date,
        private OrderCollectionFactory $salesOrderCollectionFactory,
        private Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render the HTML for the survey if rendering is allowed and data is valid.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->isRenderAllowed()) {
            $this->prepareSurveyData();
            if ($this->isValid()) {
                return parent::_toHtml();
            }
        }

        return '';
    }

    /**
     * Prepare the survey data.
     *
     * @return void
     */
    private function prepareSurveyData(): void
    {
        $this->surveyData = $this->getOrderData();
        $this->surveyData['merchant_id'] = $this->configHelper->getMerchantId();
        $this->surveyData['opt_in_style'] = $this->configHelper->getSurveyStyle();
    }

    /**
     * Get the order data for the survey.
     *
     * @return array
     */
    private function getOrderData(): array
    {
        $orderData = [];

        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return $orderData;
        }

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->salesOrderCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);

        if (!$collection->getSize()) {
            return $orderData;
        }

        foreach ($collection as $order) {
            /** @var Order $order */
            try {
                if (!$this->validateCustomerGroup($order->getCustomerGroupId())) {
                    throw new LocalizedException(__('Restricted customer group.'));
                }

                $orderData['email'] = $order->getCustomerEmail();
                $orderData['order_id'] = $order->getIncrementId();

                if ($order->getIsVirtual()) {
                    $address = $order->getBillingAddress();
                } else {
                    $address = $order->getShippingAddress();
                }

                $orderData['delivery_country'] = $address->getCountryId();
                $orderData['estimated_delivery_date'] = $this->getDeliveryDate($order, $address->getCountryId());

                foreach ($orderData as $item) {
                    if (empty($item)) {
                        throw new LocalizedException(__('Invalid value.'));
                    }
                }
            } catch (\Exception $e) {
                $orderData = [];
                continue;
            }

            break;
        }

        return $orderData;
    }

    /**
     * Check if rendering the survey is allowed.
     *
     * @return bool
     */
    private function isRenderAllowed(): bool
    {
        return $this->configHelper->isModuleEnabled();
    }

    /**
     * Validate if the customer group is allowed for the survey.
     *
     * @param int $groupId
     * @return bool
     */
    private function validateCustomerGroup($groupId)
    {
        return $this->configHelper->isOfferSurveyToAllCustomers()
            || in_array($groupId, $this->configHelper->getCustomerGroupsToOffer());
    }

    /**
     * Validate the survey data.
     *
     * @return bool
     */
    private function isValid(): bool
    {
        return count($this->surveyData) == self::SURVEY_DATA_FIELDS_COUNT;
    }

    /**
     * Get the estimated delivery date.
     *
     * @param Order $order
     * @param string $countryCode
     * @return string
     */
    private function getDeliveryDate($order, string $countryCode): string
    {
        $createdDate = $this->date->date('Y-m-d', $order->getCreatedAt());
        if ($order->getIsVirtual()) {
            return $createdDate;
        }

        $offset = max(0, $this->configHelper->getCustomDeliveryTimeRules($order->getShippingMethod(), $countryCode));
        return $this->date->date('Y-m-d', $createdDate . ' + ' . $offset . 'days');
    }

    /**
     * Get a specific survey data value.
     *
     * @param string $key
     * @return string
     */
    public function getSurveyData(string $key): string
    {
        return $this->escaper->escapeHtml($this->surveyData[$key] ?? '');
    }

    /**
     * Get the survey language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        $lang = $this->configHelper->getSurveyLanguage();
        return $this->escaper->escapeHtml($lang);
    }
}
