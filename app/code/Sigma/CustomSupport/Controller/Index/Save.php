<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Sigma\CustomSupport\Model\SupportFactory;
use Sigma\CustomSupport\Api\SupportRepositoryInterface;

class Save implements HttpPostActionInterface
{
    private const RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly ManagerInterface $messageManager,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly SupportFactory $supportFactory,
        private readonly SupportRepositoryInterface $supportRepository,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Curl $curl,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): Redirect
    {
        $redirect = $this->redirectFactory->create();
        $redirect->setPath('customersupport');

        // Validate form key
        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('Invalid form submission.'));
            return $redirect;
        }

        // Get form data
        $name = trim((string) $this->request->getParam('name'));
        $email = trim((string) $this->request->getParam('email'));
        $contactNumber = trim((string) $this->request->getParam('contact_number'));
        $message = trim((string) $this->request->getParam('message'));

        // Server-side validation
        $errors = $this->validateFormData($name, $email, $contactNumber, $message);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->messageManager->addErrorMessage($error);
            }
            return $redirect;
        }

        // reCAPTCHA validation
        if (!$this->validateRecaptcha()) {
            $this->messageManager->addErrorMessage(__('reCAPTCHA verification failed. Please try again.'));
            return $redirect;
        }

        // Save data
        try {
            $support = $this->supportFactory->create();
            $support->setName($name);
            $support->setEmail($email);
            $support->setContactNumber($contactNumber);
            $support->setMessage($message);
            $this->supportRepository->save($support);

            $this->messageManager->addSuccessMessage(
                __('Your support request has been submitted successfully. We will get back to you soon.')
            );
        } catch (\Exception $e) {
            $this->logger->error('CustomSupport save error: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Something went wrong. Please try again later.'));
        }

        return $redirect;
    }

    private function validateFormData(string $name, string $email, string $contactNumber, string $message): array
    {
        $errors = [];

        if (empty($name) || strlen($name) < 2 || strlen($name) > 255) {
            $errors[] = __('Name is required (2-255 characters).');
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = __('Please enter a valid email address.');
        }

        if (empty($contactNumber) || !preg_match('/^[0-9+\-\s()]{7,20}$/', $contactNumber)) {
            $errors[] = __('Please enter a valid contact number (7-20 digits).');
        }

        if (empty($message) || strlen($message) < 10) {
            $errors[] = __('Message is required (minimum 10 characters).');
        }

        return $errors;
    }

    private function validateRecaptcha(): bool
    {
        $enabled = $this->scopeConfig->isSetFlag(
            'sigma_customsupport/recaptcha/enabled',
            ScopeInterface::SCOPE_STORE
        );

        if (!$enabled) {
            return true;
        }

        $secretKey = $this->scopeConfig->getValue(
            'sigma_customsupport/recaptcha/secret_key',
            ScopeInterface::SCOPE_STORE
        );

        $recaptchaResponse = $this->request->getParam('g-recaptcha-response');

        if (empty($recaptchaResponse) || empty($secretKey)) {
            return false;
        }

        try {
            $this->curl->post(self::RECAPTCHA_VERIFY_URL, [
                'secret' => $secretKey,
                'response' => $recaptchaResponse
            ]);

            $result = json_decode($this->curl->getBody(), true);
            return isset($result['success']) && $result['success'] === true;
        } catch (\Exception $e) {
            $this->logger->error('reCAPTCHA verification error: ' . $e->getMessage());
            return false;
        }
    }
}
