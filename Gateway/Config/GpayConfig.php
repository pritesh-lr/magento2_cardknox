<?php
/**
 * Copyright © 2018 Cardknox Development Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace CardknoxDevelopment\Cardknox\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class GpayConfig
{
    public const DEFAULT_PATH_PATTERN = 'payment/%s/%s';
    public const KEY_ACTIVE = 'payment/cardknox_google_pay/active';
    public const MERCHANT_ID = 'payment/cardknox_google_pay/merchant_id';
    public const MERCHANT_NAME = 'payment/cardknox_google_pay/merchant_name';
    public const ENVIRONMENT = 'payment/cardknox_google_pay/environment';
    public const BUTTON_STYLE = 'payment/cardknox_google_pay/button_style';
    public const SPECIFICCOUNTRY = 'payment/cardknox_google_pay/specificcountry';
    public const KEY_CC_TYPES = ['VI', 'MC', 'AE', 'DI', 'JCB', 'MI', 'DN', 'CUP'];
    public const METHOD_CODE = 'cardknox_google_pay';
    public const CARDKNOX_TOKEN_KEY = 'cardknox_token_key';
    public const KEY_CC_TYPES_CARDKNOX_MAPPER = 'cctypes_cardknox_mapper';
    public const GPAY_PAYMENT_ACTION = 'payment/cardknox_google_pay/payment_action';
    public const GPAY_SPLIT_CAPTURE_ENABLED = 'payment/cardknox_google_pay/split_capture_enabled';
    public const GPAY_BUTTON_TYPE = 'payment/cardknox_google_pay/gp_button_type';
    public const GOOGLEPAY_ENABLE_ON_CARTPAGE = 'payment/cardknox_google_pay/cardknox_googlepay_enable_cart';
    public const ENABLE_GOOGLEPAY_SHOW_SUMMARY = 'payment/cardknox_google_pay/gp_enable_show_summary';
    public const BUTTON_SIZE_MODE = 'payment/cardknox_google_pay/button_size_mode';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var mixed
     */
    public $pathPattern;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->pathPattern = $pathPattern;
    }

    /**
     * IsActive function
     *
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }

    /**
     * GetMerchantName function
     *
     * @return string
     */
    public function getMerchantName()
    {
        return $this->getValue(self::MERCHANT_NAME);
    }

    /**
     * GetEnvironment function
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->getValue(self::ENVIRONMENT);
    }

    /**
     * GetButtonStyle function
     *
     * @return string
     */
    public function getButtonStyle()
    {
        return $this->getValue(self::BUTTON_STYLE);
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|null $storeId
     *
     * @return mixed
     */
    public function getValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get GooglePay payment action function
     *
     * @return string
     */
    public function getGPayPaymentAction()
    {
        return $this->getValue(self::GPAY_PAYMENT_ACTION);
    }

    /**
     * Enable split capture for autorize payment action for GooglePay
     *
     * @return boolean
     */
    public function isGPaySplitCaptureEnabled()
    {
        return (bool) $this->getValue(self::GPAY_SPLIT_CAPTURE_ENABLED);
    }

    /**
     * Get GooglePay Button Type function
     *
     * @return string
     */
    public function getGooglePayButtonType()
    {
        return $this->getValue(self::GPAY_BUTTON_TYPE);
    }

    /**
     * Check GooglePay is enable for cart page function
     *
     * @return boolean
     */
    public function isEnabledGooglePayOnCartPage()
    {
        return (bool) $this->getValue(self::GOOGLEPAY_ENABLE_ON_CARTPAGE);
    }

    /**
     * Check show summary is enable for GooglePaypopup on cart page function
     *
     * @return boolean
     */
    public function isEnabledGooglePayShowSummary()
    {
        return (bool) $this->getValue(self::ENABLE_GOOGLEPAY_SHOW_SUMMARY);
    }

    /**
     * GetButtonSizeMode function
     *
     * @return string
     */
    public function getGPButtonSizeMode()
    {
        return $this->getValue(self::BUTTON_SIZE_MODE);
    }
}
