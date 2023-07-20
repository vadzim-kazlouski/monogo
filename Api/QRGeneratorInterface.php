<?php
namespace Vadimk\Monogo\Api;
/**
 * Interface QRGeneratorInterface
 * @package Vadimk\Monogo\Api
 */
interface QRGeneratorInterface
{
    /**
     * @param string $text
     * @return mixed
     */
    public function getQRCode($text);

    /**
     * @param string $content
     * @param string $filename
     * @return string
     */
    public function saveQRcode($content, $filename);
}
