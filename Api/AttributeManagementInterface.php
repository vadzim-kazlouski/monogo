<?php
namespace Vadimk\Monogo\Api;
/**
 * Interface AttributeManagementInterface
 * @package Vadimk\Monogo\Api
 */
interface AttributeManagementInterface
{
    /**
     * @param int $productCount
     * @return mixed
     */
    public function createQueue($productCount);
}
