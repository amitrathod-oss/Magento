<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Model;

use Magento\Framework\Model\AbstractModel;
use Sigma\CustomSupport\Api\Data\SupportInterface;
use Sigma\CustomSupport\Model\ResourceModel\Support as SupportResource;

class Support extends AbstractModel implements SupportInterface
{
    protected function _construct(): void
    {
        $this->_init(SupportResource::class);
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): SupportInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getEmail(): ?string
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail(string $email): SupportInterface
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function getContactNumber(): ?string
    {
        return $this->getData(self::CONTACT_NUMBER);
    }

    public function setContactNumber(string $contactNumber): SupportInterface
    {
        return $this->setData(self::CONTACT_NUMBER, $contactNumber);
    }

    public function getMessage(): ?string
    {
        return $this->getData(self::MESSAGE);
    }

    public function setMessage(string $message): SupportInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }
}
