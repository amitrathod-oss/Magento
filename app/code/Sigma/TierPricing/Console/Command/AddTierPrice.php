<?php
/**
 * CLI Command to Add Tier Price
 *
 * @category  Sigma
 * @package   Sigma_TierPricing
 */

declare(strict_types=1);

namespace Sigma\TierPricing\Console\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory;
use Magento\Catalog\Api\ScopedProductTierPriceManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to add tier price to a product
 */
class AddTierPrice extends Command
{
    private const ARG_SKU = 'sku';
    private const ARG_QTY = 'qty';
    private const ARG_PRICE = 'price';
    private const OPT_GROUP = 'group';
    private const OPT_WEBSITE = 'website';

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var ProductTierPriceInterfaceFactory
     */
    private ProductTierPriceInterfaceFactory $tierPriceFactory;

    /**
     * @var ScopedProductTierPriceManagementInterface
     */
    private ScopedProductTierPriceManagementInterface $tierPriceManagement;

    /**
     * @var GroupRepositoryInterface
     */
    private GroupRepositoryInterface $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductTierPriceInterfaceFactory $tierPriceFactory
     * @param ScopedProductTierPriceManagementInterface $tierPriceManagement
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductTierPriceInterfaceFactory $tierPriceFactory,
        ScopedProductTierPriceManagementInterface $tierPriceManagement,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->tierPriceFactory = $tierPriceFactory;
        $this->tierPriceManagement = $tierPriceManagement;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this->setName('sigma:tierprice:add')
            ->setDescription('Add tier price to a product for a specific customer group')
            ->addArgument(
                self::ARG_SKU,
                InputArgument::REQUIRED,
                'Product SKU'
            )
            ->addArgument(
                self::ARG_QTY,
                InputArgument::REQUIRED,
                'Minimum quantity for tier price'
            )
            ->addArgument(
                self::ARG_PRICE,
                InputArgument::REQUIRED,
                'Tier price value'
            )
            ->addOption(
                self::OPT_GROUP,
                'g',
                InputOption::VALUE_OPTIONAL,
                'Customer group ID (0=NOT LOGGED IN, 1=General, 32000=All Groups)',
                '1'
            )
            ->addOption(
                self::OPT_WEBSITE,
                'w',
                InputOption::VALUE_OPTIONAL,
                'Website ID (0 for all websites)',
                '0'
            );
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sku = $input->getArgument(self::ARG_SKU);
        $qty = (float) $input->getArgument(self::ARG_QTY);
        $price = (float) $input->getArgument(self::ARG_PRICE);
        $groupId = $input->getOption(self::OPT_GROUP);
        $websiteId = (int) $input->getOption(self::OPT_WEBSITE);

        try {
            // Verify product exists
            $product = $this->productRepository->get($sku);
            
            // Get existing tier prices
            $existingTierPrices = $product->getTierPrices() ?? [];
            
            // Create new tier price
            $tierPrice = $this->tierPriceFactory->create();
            $tierPrice->setCustomerGroupId($groupId);
            $tierPrice->setQty($qty);
            $tierPrice->setValue($price);
            
            // Add to existing tier prices
            $existingTierPrices[] = $tierPrice;
            $product->setTierPrices($existingTierPrices);
            
            // Save product
            $this->productRepository->save($product);
            
            $groupName = $this->getGroupName($groupId);
            
            $output->writeln(sprintf(
                '<info>Tier price added successfully!</info>'
            ));
            $output->writeln(sprintf(
                '  SKU: %s',
                $sku
            ));
            $output->writeln(sprintf(
                '  Customer Group: %s (ID: %s)',
                $groupName,
                $groupId
            ));
            $output->writeln(sprintf(
                '  Quantity: %s+',
                $qty
            ));
            $output->writeln(sprintf(
                '  Price: $%s',
                number_format($price, 2)
            ));
            
            return Command::SUCCESS;
        } catch (LocalizedException $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        }
    }

    /**
     * Get customer group name by ID
     *
     * @param string $groupId
     * @return string
     */
    private function getGroupName(string $groupId): string
    {
        if ($groupId === '32000') {
            return 'ALL GROUPS';
        }
        
        try {
            $group = $this->groupRepository->getById((int) $groupId);
            return $group->getCode();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
