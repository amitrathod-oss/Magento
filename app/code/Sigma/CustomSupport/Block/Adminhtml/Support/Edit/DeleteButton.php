<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Block\Adminhtml\Support\Edit;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    public function getButtonData(): array
    {
        $id = (int) $this->request->getParam('id');
        if (!$id) {
            return [];
        }

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => sprintf(
                "deleteConfirm('%s', '%s', {data: {}})",
                __('Are you sure you want to delete this record?'),
                $this->urlBuilder->getUrl('*/*/delete', ['id' => $id])
            ),
            'sort_order' => 20,
        ];
    }
}
