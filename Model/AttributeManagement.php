<?php
namespace Vadimk\Monogo\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\MessageQueue\BulkPublisherInterface;
use Vadimk\Monogo\Api\AttributeManagementInterface;
use Magento\Eav\Model\Entity\TypeFactory;

/**
 * Class AttributeManagement
 * @package Vadimk\Monogo\Model
 */
class AttributeManagement implements AttributeManagementInterface
{
    const TOPIC_NAME = 'monogo.attribute.sync';
    const BASE_ATTR = 'name';

    /**
     * @var BulkPublisherInterface
     */
    protected $publisher;

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
     * AttributeManagement constructor.
     * @param BulkPublisherInterface $publisher
     * @param ResourceConnection $resourceConnection
     * @param ScopeConfigInterface $scopeConfig
     * @param TypeFactory $typeFactory
     */
    public function __construct(
        BulkPublisherInterface $publisher,
        ResourceConnection $resourceConnection,
        ScopeConfigInterface $scopeConfig,
        TypeFactory $typeFactory
    ) {
        $this->publisher = $publisher;
        $this->resourceConnection = $resourceConnection;
        $this->scopeConfig = $scopeConfig;
        $this->typeFactory = $typeFactory;
    }

    /**
     * @param int $productCount
     * @return mixed|void
     */
    public function createQueue($productCount)
    {
        $resultData = $this->getAttrubuteValueIds($productCount);
        if (!empty($resultData)) {
            $this->publisher->publish(self::TOPIC_NAME, $resultData);
        }
    }

    /**
     * @param $count
     * @return array
     */
    protected function getAttrubuteValueIds($count)
    {
        $resultData = [];
        $connection = $this->resourceConnection->getConnection();
        $productEntityType = $this->typeFactory->create()->loadByCode('catalog_product');
        $entityTypeId = $productEntityType->getId();
        $select = $connection->select()
            ->from(
                ['a' => $this->resourceConnection->getTableName('eav_attribute')],
                ['attribute_id', 'backend_type']
            )
            ->where('a.attribute_code = ?', self::BASE_ATTR)
            ->where('a.entity_type_id = ?', $entityTypeId);

        $attributeData = $connection->fetchRow($select);
        if ($attributeData) {
            $table = 'catalog_product_entity_' . $attributeData['backend_type'];
            $select = $connection->select()
                ->from(
                    ['attr' => $this->resourceConnection->getTableName($table)],
                    ['value_id']
                )
                ->where('attribute_id = ?', $attributeData['attribute_id']);
            if ($productCount = (int)$count) {
                $select->limit($productCount);
            }
            $resultData = $connection->fetchCol($select);
        }

        return $resultData;
    }
}
