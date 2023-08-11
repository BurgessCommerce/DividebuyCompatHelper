<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace BurgessCommerce\HyvaCompatFallbackThemeFallback\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;

class Logger extends AbstractHelper
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
    }

    /**
     * custom logger to log exception content to log file
     *
     * @param Exception
     * @return void
     */
    public function logException(\Exception $e)
    {
        $original_message = $e->getMessage();
        $this->logger->error(
            $original_message,
            [ 'detail' => $e->getTraceAsString() ]
        );
    }

    /**
     * custom logger to log exception content to log file
     *
     * @param array
     * @return void
     */
    public function logMixed($mixed_data, $msg = null)
    {
        $msg = $msg ?? __('Info Array');
        $this->logger->info(
            $msg,
            $mixed_data
        );
    }
}

