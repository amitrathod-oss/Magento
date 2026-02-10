<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Model\ResourceModel\Support;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sigma\CustomSupport\Model\Support;
use Sigma\CustomSupport\Model\ResourceModel\Support as SupportResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct(): void
    {
        $this->_init(Support::class, SupportResource::class);
    }
}
