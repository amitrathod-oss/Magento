<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Controller\Adminhtml\Support;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Sigma_CustomSupport::support_manage';

    public function __construct(
        Context $context,
        private readonly PageFactory $pageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $id = $this->getRequest()->getParam('id');
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Sigma_CustomSupport::support_manage');
        $page->getConfig()->getTitle()->prepend(
            $id ? __('Edit Support Request #%1', $id) : __('New Support Request')
        );
        return $page;
    }
}
