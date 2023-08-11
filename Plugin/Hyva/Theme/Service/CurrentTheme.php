<?php

declare(strict_types=1);

namespace BurgessCommerce\HyvaCompatFallbackThemeFallback\Plugin\Hyva\Theme\Service;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use BurgessCommerce\HyvaCompatFallbackThemeFallback\Helper\Logger;
use Magento\Framework\Module\Manager as ModuleManager;

class CurrentTheme
{
    const EXPECTED_MODULE = 'Hyva_DividebuyPayment';
    const EXPECTED_ORIGIN_CLASS = 'Hyva\CompatModuleFallback\Plugin\ViewFileOverride';
    const BACKTRACE_DEPTH = 8;

    /**
     * @var Http|RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $_urlBuilder;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param ModuleManager $moduleManager
     * @param Logger $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        ModuleManager $moduleManager,
        Logger $logger
    ) {
        $this->request = $request;
        $this->_urlBuilder = $urlBuilder;
        $this->moduleManager = $moduleManager;
        $this->logger = $logger;
    }

    /**
     * because dividebuy urls like /checkoutconfig/index/userloginmodal/ is not listed in `Apply fallback to requests containing` in Hyva Fallback Config
     * (it should not be listed there because it is also being used in other pages like PDP or Cart which is Hyva)
     * we need to tell CompatModuleFallback current request is not Hyva if its referer url is checkout page (request was fired from checkout page)
     */
    public function afterIsHyva(
        \Hyva\Theme\Service\CurrentTheme $subject,
        $result
    )
    {
        if ($result == true
            && $this->isHyvaDividebuyPaymentEnabled()
            && $this->isfromHyvaCompatModuleFallback()) {
            //$this->logger->logMixed([$this->request->getHeader('referer'), $this->getUrl('checkout')]);
            return $this->request->getHeader('referer') !== $this->getUrl('checkout');
        }

        return $result;
    }

    /**
     * verify if isHyva is called from Hyva_CompatModuleFallback
     *
     * @return boolean
     */
    function isfromHyvaCompatModuleFallback()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, self::BACKTRACE_DEPTH);
        foreach ($trace as $step){
            if ($step['class'] == self::EXPECTED_ORIGIN_CLASS) {
                //$this->logger->logMixed($step);
                return true;
            }
        }

        return false;
    }

    /**
     * check if Hyva_DividebuyPayment is enabled
     *
     * @return boolean
     */
    function isHyvaDividebuyPaymentEnabled()
    {
        return $this->moduleManager->isEnabled(self::EXPECTED_MODULE);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }
}
