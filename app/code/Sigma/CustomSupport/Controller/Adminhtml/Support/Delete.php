<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Controller\Adminhtml\Support;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Sigma\CustomSupport\Api\SupportRepositoryInterface;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Sigma_CustomSupport::support_manage';

    public function __construct(
        Context $context,
        private readonly SupportRepositoryInterface $supportRepository
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('Invalid support request ID.'));
            return $redirect->setPath('*/*/');
        }

        try {
            $this->supportRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('Support request has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $redirect->setPath('*/*/');
    }
}
