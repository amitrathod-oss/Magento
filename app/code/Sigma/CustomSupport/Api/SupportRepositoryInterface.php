<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Api;

use Sigma\CustomSupport\Api\Data\SupportInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface SupportRepositoryInterface
{
    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $id): SupportInterface;

    /**
     * @throws CouldNotSaveException
     */
    public function save(SupportInterface $support): SupportInterface;

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(SupportInterface $support): bool;

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id): bool;
}
