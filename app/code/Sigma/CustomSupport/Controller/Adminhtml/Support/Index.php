<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Controller\Adminhtml\Support;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Index extends Action
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
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Sigma_CustomSupport::support_manage');
        $page->getConfig()->getTitle()->prepend(__('Support Requests'));
        return $page;
    }
}
