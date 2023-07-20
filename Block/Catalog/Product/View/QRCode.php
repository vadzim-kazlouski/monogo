<?php
namespace Vadimk\Monogo\Block\Catalog\Product\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Vadimk\Monogo\Model\QRGenerator;

/**
 * Class QRCode
 * @package Vadimk\Monogo\Block\Catalog\Product\View
 */
class QRCode extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * QRCode constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return false|string
     */
    public function getQRCode()
    {
        $product = $this->registry->registry('product');
        $monogoAttr = $product->getMonogo();
        if ($monogoAttr) {
            $filename = md5($monogoAttr) . QRGenerator::QR_CODE_FOMAT;
            $filePath = QRGenerator::QR_CODE_FOLDER . '/'. $filename[0]. '/'. $filename[1] .'/' . $filename;
            $mediaURL = $this->_urlBuilder->getBaseUrl(array('_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA));

            return $mediaURL . $filePath;
        }

        return false;
    }
}
