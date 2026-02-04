<?php
/**
 * Observer to copy GST from quote to order
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

declare(strict_types=1);

namespace Sigma\CheckoutGst\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Copies GST number from quote to order and updates order grid
 */
class CopyGstToOrder implements ObserverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->orderRepository = $orderRepository;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Copy GST from quote to order
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        if (!$quote || !$order || !$order->getId()) {
            return;
        }

        $gstNo = $quote->getData('company_gst_no');
        if (!$gstNo) {
            return;
        }

        // Save to order
        $order->setData('company_gst_no', $gstNo);
        $this->orderRepository->save($order);

        // Update order grid
        $this->updateOrderGrid((int) $order->getId(), $gstNo);
    }

    /**
     * Update order grid with GST number
     *
     * @param int $orderId
     * @param string $gstNo
     * @return void
     */
    private function updateOrderGrid(int $orderId, string $gstNo): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('sales_order_grid');

        $connection->update(
            $tableName,
            ['company_gst_no' => $gstNo],
            ['entity_id = ?' => $orderId]
        );
    }
}
