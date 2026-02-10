<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Controller\Adminhtml\Support;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Sigma\CustomSupport\Api\SupportRepositoryInterface;
use Sigma\CustomSupport\Model\SupportFactory;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Sigma_CustomSupport::support_manage';

    public function __construct(
        Context $context,
        private readonly SupportRepositoryInterface $supportRepository,
        private readonly SupportFactory $supportFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $redirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $redirect->setPath('*/*/');
        }

        try {
            $id = isset($data['id']) ? (int) $data['id'] : null;
            $support = $id ? $this->supportRepository->getById($id) : $this->supportFactory->create();

            $support->setName((string) ($data['name'] ?? ''));
            $support->setEmail((string) ($data['email'] ?? ''));
            $support->setContactNumber((string) ($data['contact_number'] ?? ''));
            $support->setMessage((string) ($data['message'] ?? ''));

            $this->supportRepository->save($support);
            $this->messageManager->addSuccessMessage(__('Support request has been saved.'));

            if ($this->getRequest()->getParam('back') === 'edit') {
                return $redirect->setPath('*/*/edit', ['id' => $support->getId()]);
            }
            return $redirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $redirect->setPath('*/*/edit', ['id' => $data['id'] ?? null]);
        }
    }
}
