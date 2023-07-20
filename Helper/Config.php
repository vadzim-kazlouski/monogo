<?php
namespace Vadimk\Monogo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Config
 * @package Vadimk\Monogo\Helper
 */
class Config extends AbstractHelper
{
    const DEFAULT_PRODUCT_COUNT = 'monogo/synchronization/default_product_count';
    const QR_SERVICE_URL = 'monogo/qr_code/url';
    const QR_SERVICE_USERNAME = 'monogo/qr_code/username';
    const QR_SERVICE_PASSWORD = 'monogo/qr_code/password';

    /**
     * @return mixed
     */
    public function getDefaultProductCount()
    {
        return $this->scopeConfig->getValue(self::DEFAULT_PRODUCT_COUNT);
    }

    /**
     * @return mixed
     */
    public function getServiceURL()
    {
        return $this->scopeConfig->getValue(self::QR_SERVICE_URL);
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue(self::QR_SERVICE_USERNAME);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->scopeConfig->getValue(self::QR_SERVICE_PASSWORD);
    }
}
