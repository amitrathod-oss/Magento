<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Sigma\CustomSupport\Api\Data\SupportInterface;
use Sigma\CustomSupport\Api\SupportRepositoryInterface;
use Sigma\CustomSupport\Model\ResourceModel\Support as SupportResource;

class SupportRepository implements SupportRepositoryInterface
{
    public function __construct(
        private readonly SupportResource $resource,
        private readonly SupportFactory $supportFactory
    ) {
    }

    public function getById(int $id): SupportInterface
    {
        $support = $this->supportFactory->create();
        $this->resource->load($support, $id);
        if (!$support->getId()) {
            throw new NoSuchEntityException(__('Support request with ID "%1" does not exist.', $id));
        }
        return $support;
    }

    public function save(SupportInterface $support): SupportInterface
    {
        try {
            $this->resource->save($support);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save support request: %1', $e->getMessage()));
        }
        return $support;
    }

    public function delete(SupportInterface $support): bool
    {
        try {
            $this->resource->delete($support);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete support request: %1', $e->getMessage()));
        }
        return true;
    }

    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }
}
