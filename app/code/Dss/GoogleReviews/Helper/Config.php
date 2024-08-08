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
namespace Dss\GoogleReviews\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Dss\GoogleReviews\Helper\CustomDeliveryTime;

class Config extends AbstractHelper
{
    private const MODULE_ENABLED_PATH = 'dss_google_reviews/general/enabled';
    private const MERCHANT_ID_PATH = 'dss_google_reviews/general/merchant_id';
    private const DELIVERY_OFFSET_PATH = 'dss_google_reviews/general/delivery_time';

    private const SURVEY_ALL_CUSTOMERS_PATH = 'dss_google_reviews/survey/all_customers';
    private const SURVEY_CUSTOMER_GROUPS_PATH = 'dss_google_reviews/survey/customer_groups';
    private const SURVEY_LANGUAGE_PATH = 'dss_google_reviews/survey/language';
    private const SURVEY_STYLE_PATH = 'dss_google_reviews/survey/style';

    private const BADGE_ENABLED_PATH = 'dss_google_reviews/badge/enabled';
    private const BADGE_POSITION_PATH = 'dss_google_reviews/badge/position';
    private const BADGE_LANGUAGE_PATH = 'dss_google_reviews/badge/language';

    /**
     * @param Context $context
     * @param CustomDeliveryTime $customTimeHelper
     */
    public function __construct(
        Context $context,
        private CustomDeliveryTime $customTimeHelper
    ) {
        parent::__construct($context);
    }

    /**
     * Check if the module is enabled.
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return (bool) $this->scopeConfig
            ->getValue(self::MODULE_ENABLED_PATH, ScopeInterface::SCOPE_WEBSITES);
    }

    /**
     * Get the merchant ID.
     *
     * @return int
     */
    public function getMerchantId(): int
    {
        return (int) $this->scopeConfig
            ->getValue(self::MERCHANT_ID_PATH, ScopeInterface::SCOPE_WEBSITES);
    }

    /**
     * Get the delivery offset.
     *
     * @return int
     */
    public function getDeliveryOffset(): int
    {
        return (int) max(0, $this->scopeConfig
            ->getValue(self::DELIVERY_OFFSET_PATH, ScopeInterface::SCOPE_WEBSITES));
    }

    /**
     * Check if the survey is offered to all customers.
     *
     * @return bool
     */
    public function isOfferSurveyToAllCustomers(): bool
    {
        return (bool) $this->scopeConfig
            ->getValue(self::SURVEY_ALL_CUSTOMERS_PATH, ScopeInterface::SCOPE_WEBSITES);
    }

    /**
     * Get the customer groups to offer the survey to.
     *
     * @return array
     */
    public function getCustomerGroupsToOffer(): array
    {
        return explode(',', $this->scopeConfig
            ->getValue(self::SURVEY_CUSTOMER_GROUPS_PATH, ScopeInterface::SCOPE_WEBSITES));
    }

    /**
     * Get the survey style.
     *
     * @return string
     */
    public function getSurveyStyle(): string
    {
        return $this->scopeConfig->getValue(self::SURVEY_STYLE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the survey language.
     *
     * @return string
     */
    public function getSurveyLanguage(): string
    {
        return $this->scopeConfig->getValue(self::SURVEY_LANGUAGE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if the badge is enabled.
     *
     * @return bool
     */
    public function isBadgeEnabled(): bool
    {
        return $this->scopeConfig->getValue(self::BADGE_ENABLED_PATH, ScopeInterface::SCOPE_WEBSITES)
            && $this->isModuleEnabled();
    }

    /**
     * Get the badge position.
     *
     * @return string
     */
    public function getBadgePosition(): string
    {
        return $this->scopeConfig->getValue(self::BADGE_POSITION_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the badge language.
     *
     * @return string
     */
    public function getBadgeLanguage(): string
    {
        return $this->scopeConfig->getValue(self::BADGE_LANGUAGE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get custom delivery time rules based on shipping method and country.
     *
     * @param string $shippingMethod
     * @param string $country
     * @return int
     */
    public function getCustomDeliveryTimeRules(string $shippingMethod, string $country): int
    {
        $customValue = $this->customTimeHelper->getConfigValue($shippingMethod, $country);
        return $customValue ?: $this->scopeConfig
            ->getValue(self::DELIVERY_OFFSET_PATH, ScopeInterface::SCOPE_WEBSITES);
    }
}
