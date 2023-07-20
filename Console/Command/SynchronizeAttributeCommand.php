<?php
namespace Vadimk\Monogo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vadimk\Monogo\Api\AttributeManagementInterface;
use Vadimk\Monogo\Helper\Config;

/**
 * Class SynchronizeAttributeCommand
 * @package Vadimk\Monogo\Console\Command
 */
class SynchronizeAttributeCommand extends Command
{
    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var AttributeManagementInterface
     */
    protected $arrributeManagement;

    /**
     * SynchronizeAttributeCommand constructor.
     * @param Config $helper
     * @param AttributeManagementInterface $arrributeManagement
     * @param null $name
     */
    public function __construct(
        Config $helper,
        AttributeManagementInterface $arrributeManagement,
        $name = null
    ) {
        $this->helper = $helper;
        $this->arrributeManagement = $arrributeManagement;
        parent::__construct($name);
    }

    /**
     * @ingeritdoc
     */
    protected function configure()
    {
        $this->setName('monogo:synchronize-attribute:run')
            ->setDescription('Synchronizes product names to the custom attribute')
            ->addArgument('product_count', InputArgument::OPTIONAL, 'Number of products to synchronize');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $productCount = $input->getArgument('product_count') ?: $this->helper->getDefaultProductCount();
        try {
            $this->arrributeManagement->createQueue($productCount);
            $output->writeln(__('Products attribute synchronization successfully scheduled in the message queue'));
        } catch (\Exception $exception) {
            $output->writeln(__($exception->getMessage()));
        }
    }
}
