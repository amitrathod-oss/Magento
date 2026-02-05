<?php
/**
 * CLI Command to List Tier Prices
 *
 * @category  Sigma
 * @package   Sigma_TierPricing
 */

declare(strict_types=1);

namespace Sigma\TierPricing\Console\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Command to list tier prices for a product
 */
class ListTierPrices extends Command
{
    private const ARG_SKU = 'sku';

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var GroupRepositoryInterface
     */
    private GroupRepositoryInterface $groupRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        GroupRepositoryInterface $groupRepository
    ) {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this->setName('sigma:tierprice:list')
            ->setDescription('List all tier prices for a product')
            ->addArgument(
                self::ARG_SKU,
                InputArgument::REQUIRED,
                'Product SKU'
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

        try {
            $product = $this->productRepository->get($sku);
            $tierPrices = $product->getTierPrices();

            $output->writeln(sprintf('<info>Tier Prices for Product: %s</info>', $sku));
            $output->writeln(sprintf('Product Name: %s', $product->getName()));
            $output->writeln(sprintf('Regular Price: $%s', number_format((float) $product->getPrice(), 2)));
            $output->writeln('');

            if (empty($tierPrices)) {
                $output->writeln('<comment>No tier prices configured for this product.</comment>');
                return Command::SUCCESS;
            }

            $table = new Table($output);
            $table->setHeaders(['Customer Group', 'Group ID', 'Min Qty', 'Tier Price', 'Discount']);

            foreach ($tierPrices as $tierPrice) {
                $groupId = $tierPrice->getCustomerGroupId();
                $groupName = $this->getGroupName($groupId);
                $qty = $tierPrice->getQty();
                $price = $tierPrice->getValue();
                $regularPrice = (float) $product->getPrice();
                $discount = $regularPrice > 0 
                    ? round((($regularPrice - $price) / $regularPrice) * 100, 2) 
                    : 0;

                $table->addRow([
                    $groupName,
                    $groupId,
                    $qty . '+',
                    '$' . number_format($price, 2),
                    $discount . '%'
                ]);
            }

            $table->render();
            
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
     * @param int|string $groupId
     * @return string
     */
    private function getGroupName($groupId): string
    {
        if ($groupId == 32000) {
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
