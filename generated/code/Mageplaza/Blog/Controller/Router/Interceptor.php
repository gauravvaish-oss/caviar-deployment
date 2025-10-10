<?php
namespace Mageplaza\Blog\Controller\Router;

/**
 * Interceptor class for @see \Mageplaza\Blog\Controller\Router
 */
class Interceptor extends \Mageplaza\Blog\Controller\Router implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ActionFactory $actionFactory, \Mageplaza\Blog\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($actionFactory, $helper);
    }

    /**
     * {@inheritdoc}
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'match');
        return $pluginInfo ? $this->___callPlugins('match', func_get_args(), $pluginInfo) : parent::match($request);
    }

    /**
     * {@inheritdoc}
     */
    public function switchController($controller, $request, $action)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'switchController');
        return $pluginInfo ? $this->___callPlugins('switchController', func_get_args(), $pluginInfo) : parent::switchController($controller, $request, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function isRss($identifier)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isRss');
        return $pluginInfo ? $this->___callPlugins('isRss', func_get_args(), $pluginInfo) : parent::isRss($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function checkRssIdentifier($identifier)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkRssIdentifier');
        return $pluginInfo ? $this->___callPlugins('checkRssIdentifier', func_get_args(), $pluginInfo) : parent::checkRssIdentifier($identifier);
    }
}
