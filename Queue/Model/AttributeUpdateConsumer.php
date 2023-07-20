<?php
namespace Vadimk\Monogo\Queue\Model;

use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Vadimk\Monogo\Model\QRGenerator;

/**
 * Class AttributeUpdateConsumer
 * @package Vadimk\Monogo\Queue\Model
 */
class AttributeUpdateConsumer
{
    const TARGET_ATTR = 'monogo';

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TypeFactory
     */
    protected $typeFactory;

    /**
     * @var array
     */
    protected $targetAttrubite;

    /**
     * @var QRGenerator
     */
    protected $qrGenerator;

    /**
     * AttributeUpdateConsumer constructor.
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param TypeFactory $typeFactory
     * @param QRGenerator $qrGererator
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        TypeFactory $typeFactory,
        QRGenerator $qrGererator
    ) {
        $this->resourceConnection = $resourceConnection->getConnection();
        $this->scopeConfig = $scopeConfig;
        $this->typeFactory = $typeFactory;
        $this->qrGenerator = $qrGererator;
    }

    /**
     * @param int $valueId
     * @return bool
     */
    public function processMessage(int $valueId)
    {
        $select = $this->resourceConnection->select()
            ->from(
                ['attr' => $this->resourceConnection->getTableName('catalog_product_entity_varchar')]
            )
            ->where('value_id = ?', $valueId);
        $attributeData = $this->resourceConnection->fetchRow($select);
        if ($attributeData) {
            $this->qrGenerator->getQRCode($attributeData['value']);
            $attributeData['attribute_id'] = $this->getTargetAttributeId();
            unset($attributeData['value_id']);
            $this->resourceConnection->insertOnDuplicate(
                'catalog_product_entity_varchar',
                $attributeData
            );
        }

        return true;
    }

    /**
     * @return array|string
     */
    protected function getTargetAttributeId()
    {
        if (!isset($this->targetAttrubite)) {
            $productEntityType = $this->typeFactory->create()->loadByCode('catalog_product');
            $entityTypeId = $productEntityType->getId();
            $select = $this->resourceConnection->select()
                ->from(
                    ['a' => $this->resourceConnection->getTableName('eav_attribute')],
                    ['attribute_id']
                )
                ->where('a.attribute_code = ?', self::TARGET_ATTR)
                ->where('a.entity_type_id = ?', $entityTypeId);

            $this->targetAttrubite = $this->resourceConnection->fetchOne($select);
        }

        return $this->targetAttrubite;
    }
}
