<?php
namespace Goomento\PageBuilder\EntryPoint;

/**
 * Interceptor class for @see \Goomento\PageBuilder\EntryPoint
 */
class Interceptor extends \Goomento\PageBuilder\EntryPoint implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function init(array $buildSubject = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'init');
        return $pluginInfo ? $this->___callPlugins('init', func_get_args(), $pluginInfo) : parent::init($buildSubject);
    }

    /**
     * {@inheritdoc}
     */
    public function registerScripts()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'registerScripts');
        return $pluginInfo ? $this->___callPlugins('registerScripts', func_get_args(), $pluginInfo) : parent::registerScripts();
    }

    /**
     * {@inheritdoc}
     */
    public function registerStyles()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'registerStyles');
        return $pluginInfo ? $this->___callPlugins('registerStyles', func_get_args(), $pluginInfo) : parent::registerStyles();
    }

    /**
     * {@inheritdoc}
     */
    public function registerWidgets(\Goomento\PageBuilder\Builder\Managers\Widgets $widgetsManager)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'registerWidgets');
        return $pluginInfo ? $this->___callPlugins('registerWidgets', func_get_args(), $pluginInfo) : parent::registerWidgets($widgetsManager);
    }

    /**
     * {@inheritdoc}
     */
    public function registerWidgetCategories(\Goomento\PageBuilder\Builder\Managers\Elements $elements)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'registerWidgetCategories');
        return $pluginInfo ? $this->___callPlugins('registerWidgetCategories', func_get_args(), $pluginInfo) : parent::registerWidgetCategories($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function enqueueScripts()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'enqueueScripts');
        return $pluginInfo ? $this->___callPlugins('enqueueScripts', func_get_args(), $pluginInfo) : parent::enqueueScripts();
    }
}
