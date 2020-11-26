<?php

namespace Rubic\CleanCheckoutSocial\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Rubic\CleanCheckoutSocial\Service\SocialLoginService;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SocialLogin extends Template
{

    /**
     * SocialLogin constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context);
        $this->setTemplate('Rubic_CleanCheckoutSocial::social-login.phtml');

    }

    /**
     * @param $providerKey
     * @return bool
     */
    public function isEnabled() {
        return (bool) $this->_scopeConfig->getValue(
            SocialLoginService::CONFIG_PATH_SOCIAL_LOGIN_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $providerKey
     * @return bool
     */
    public function isProviderEnabled($providerKey) {
        return (bool) $this->_scopeConfig->getValue(
            sprintf(
                SocialLoginService::CONFIG_PATH_SOCIAL_LOGIN_PROVIDER_ENABLED, $providerKey
            ),
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getProviderLoginUrl($providerKey) {
        return $this->getUrl(
        'clean_checkout/social/authenticate',
            ['_query' => [
                'provider' => $providerKey,
                'referer' => $this->_request->getParam('referer')
            ]]
        );
    }

    public function getProviders() {
        return (array) $this->getData('providers');
    }

    public function toHtml() {
        if ($this->isEnabled()) {
            return parent::toHtml();
        }
    }

}
