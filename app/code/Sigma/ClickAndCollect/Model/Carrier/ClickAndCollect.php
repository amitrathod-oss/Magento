<?php
/**
 * Click and Collect Shipping Carrier Model
 *
 * @category  Sigma
 * @package   Sigma_ClickAndCollect
 */

declare(strict_types=1);

namespace Sigma\ClickAndCollect\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Click and Collect shipping carrier implementation
 */
class ClickAndCollect extends AbstractCarrier implements CarrierInterface
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'clickandcollect';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    private ResultFactory $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private MethodFactory $rateMethodFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // Check if shipping to allowed countries
        $destCountry = $request->getDestCountryId();
        if ($this->getConfigData('sallowspecific') == 1) {
            $specificCountries = $this->getConfigData('specificcountry');
            if ($specificCountries) {
                $allowedCountries = explode(',', $specificCountries);
                if (!in_array($destCountry, $allowedCountries)) {
                    return false;
                }
            }
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        // Set carrier information
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        // Set method information
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        // Calculate shipping price
        $shippingPrice = (float) $this->getConfigData('price');
        $handlingFee = (float) $this->getConfigData('handling_fee');
        $totalPrice = $shippingPrice + $handlingFee;

        $method->setPrice($totalPrice);
        $method->setCost($totalPrice);

        $result->append($method);

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [
            $this->_code => $this->getConfigData('name')
        ];
    }

    /**
     * Check if carrier has tracking
     *
     * @return bool
     */
    public function isTrackingAvailable(): bool
    {
        return false;
    }

    /**
     * Get store address
     *
     * @return string|null
     */
    public function getStoreAddress(): ?string
    {
        return $this->getConfigData('store_address');
    }

    /**
     * Get store hours
     *
     * @return string|null
     */
    public function getStoreHours(): ?string
    {
        return $this->getConfigData('store_hours');
    }

    /**
     * Get pickup instructions
     *
     * @return string|null
     */
    public function getPickupInstructions(): ?string
    {
        return $this->getConfigData('pickup_instructions');
    }
}
