<?php
namespace Goomento\PageBuilder\Controller\Content\View;

/**
 * Interceptor class for @see \Goomento\PageBuilder\Controller\Content\View
 */
class Interceptor extends \Goomento\PageBuilder\Controller\Content\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Goomento\PageBuilder\Logger\Logger $logger, \Goomento\Core\Model\Registry $registry, \Goomento\PageBuilder\Helper\Data $dataHelper)
    {
        $this->___init();
        parent::__construct($context, $logger, $registry, $dataHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        return $pluginInfo ? $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo) : parent::getActionFlag();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        return $pluginInfo ? $this->___callPlugins('getRequest', func_get_args(), $pluginInfo) : parent::getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        return $pluginInfo ? $this->___callPlugins('getResponse', func_get_args(), $pluginInfo) : parent::getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function sendResponse404()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'sendResponse404');
        return $pluginInfo ? $this->___callPlugins('sendResponse404', func_get_args(), $pluginInfo) : parent::sendResponse404();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlBuilder()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrlBuilder');
        return $pluginInfo ? $this->___callPlugins('getUrlBuilder', func_get_args(), $pluginInfo) : parent::getUrlBuilder();
    }
}
