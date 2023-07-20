<?php

namespace Vadimk\Monogo\Model;

use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Vadimk\Monogo\Api\QRGeneratorInterface;
use Magento\Framework\Filesystem;
use Vadimk\Monogo\Helper\Config;
use GuzzleHttp\ClientFactory;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class QRGenerator
 * @package Vadimk\Monogo\Model
 */
class QRGenerator implements QRGeneratorInterface
{
    const CONTENT_TYPE = 'application/json';
    const QR_CODE_KEY = 'base64QRCode';
    const QR_CODE_FOLDER = 'qr_codes';
    const QR_CODE_FOMAT = '.png';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * QRGenerator constructor.
     * @param LoggerInterface $logger
     * @param Json $serializer
     * @param Filesystem $filesystem
     * @param Config $config
     * @param ClientFactory $clientFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Json $serializer,
        Filesystem $filesystem,
        Config $config,
        ClientFactory $clientFactory
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param string $text
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getQRCode($text)
    {
        $username = $this->config->getUsername();
        $password = $this->config->getPassword();
        $url = $this->config->getServiceURL();
        if (!$username || !$password || !$url) {
            throw new \Exception(__('Please configure QR code service'));
        }
        $data = [
            'plainText' => $text
        ];
        $jsonData = $this->serializer->serialize($data);
        $client = $this->clientFactory->create(
            ['config' =>
                ['base_uri' => $url]
            ]
        );
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => self::CONTENT_TYPE,
            'Accept' => self::CONTENT_TYPE
        ];
        try {
            $response = $client->request(
                Request::HTTP_METHOD_POST,
                $url,
                [
                    'headers' => $headers,
                    'body' => $jsonData,
                ]
            );
            if ($response->getStatusCode() == 200) {
                $responseBody = $this->serializer->unserialize( $response->getBody()->getContents());
                if (isset($responseBody[self::QR_CODE_KEY])) {
                    $filename = md5($text) . self::QR_CODE_FOMAT;
                    return $this->saveQRcode($responseBody[self::QR_CODE_KEY], $filename);
                }
            }
            throw new \Exception( $response->getBody()->getContents());
        } catch (\Exception $e) {
            $this->logger->critical('QR generation error: '. $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $content
     * @param string $filename
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function saveQRcode($content, $filename)
    {
        $decodedContent = base64_decode($content);
        $mediaDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $filePath = self::QR_CODE_FOLDER . '/'. $filename[0]. '/'. $filename[1] .'/' . $filename;
        $mediaDirectory->writeFile($filePath, $decodedContent);

        return $mediaDirectory->getAbsolutePath($filePath);
    }
}
