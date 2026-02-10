<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Support extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('sigma_custom_support', 'id');
    }
}
