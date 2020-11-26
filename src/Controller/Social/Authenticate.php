<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\CleanCheckoutSocial\Controller\Social;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Rubic\CleanCheckoutSocial\Service\SocialLoginService;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Encryption\UrlCoder;
use Magento\Framework\Controller\ResultFactory;

class Authenticate extends Action
{
    /**
     * @var SocialLoginService
     */
    private $socialLoginService;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var UrlCoder
     */
    private $urlCoder;

    /**
     * @param Context $context
     * @param SocialLoginService $socialLoginService
     * @param SessionManagerInterface $session
     * @param UrlCoder $urlCoder
     */
    public function __construct(
        Context $context,
        SocialLoginService $socialLoginService,
        SessionManagerInterface $session,
        UrlCoder $urlCoder
    ) {
        parent::__construct($context);
        $this->socialLoginService = $socialLoginService;
        $this->session = $session->start();
        $this->urlCoder = $urlCoder;
    }

    /**
     * Authenticates the user using social media, then returns to the checkout.
     *
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $provider = $this->_request->getParam('provider');
        $refererParam = $this->_request->getParam('referer');
        $refererSession = $this->session->getLoginReferer();

        if ($refererParam) {
            $this->session->setLoginReferer($refererParam);
        }

        $this->socialLoginService->login($provider);

        $referer = $refererParam ?: $refererSession;
        if ($referer) {
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl($this->urlCoder->decode($referer));
            return $redirect;
        }

        return $this->_redirect('checkout/index/index');
    }
}
