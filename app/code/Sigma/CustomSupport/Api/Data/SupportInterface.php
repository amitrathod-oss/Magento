<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Api\Data;

interface SupportInterface
{
    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const CONTACT_NUMBER = 'contact_number';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getId();
    public function setId($id);
    public function getName(): ?string;
    public function setName(string $name): self;
    public function getEmail(): ?string;
    public function setEmail(string $email): self;
    public function getContactNumber(): ?string;
    public function setContactNumber(string $contactNumber): self;
    public function getMessage(): ?string;
    public function setMessage(string $message): self;
    public function getCreatedAt(): ?string;
    public function getUpdatedAt(): ?string;
}
