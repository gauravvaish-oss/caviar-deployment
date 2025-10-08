<?php
namespace Magento\Catalog\Model\ResourceModel\Category\Tree;

/**
 * Interceptor class for @see \Magento\Catalog\Model\ResourceModel\Category\Tree
 */
class Interceptor extends \Magento\Catalog\Model\ResourceModel\Category\Tree implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\ResourceModel\Category $catalogCategory, \Magento\Framework\App\CacheInterface $cache, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Catalog\Model\Attribute\Config $attributeConfig, \Magento\Catalog\Model\ResourceModel\Category\Collection\Factory $collectionFactory)
    {
        $this->___init();
        parent::__construct($catalogCategory, $cache, $storeManager, $resource, $eventManager, $attributeConfig, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setStoreId');
        return $pluginInfo ? $this->___callPlugins('setStoreId', func_get_args(), $pluginInfo) : parent::setStoreId($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStoreId');
        return $pluginInfo ? $this->___callPlugins('getStoreId', func_get_args(), $pluginInfo) : parent::getStoreId();
    }

    /**
     * {@inheritdoc}
     */
    public function addCollectionData($collection = null, $sorted = false, $exclude = [], $toLoad = true, $onlyActive = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addCollectionData');
        return $pluginInfo ? $this->___callPlugins('addCollectionData', func_get_args(), $pluginInfo) : parent::addCollectionData($collection, $sorted, $exclude, $toLoad, $onlyActive);
    }

    /**
     * {@inheritdoc}
     */
    public function addInactiveCategoryIds($ids)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addInactiveCategoryIds');
        return $pluginInfo ? $this->___callPlugins('addInactiveCategoryIds', func_get_args(), $pluginInfo) : parent::addInactiveCategoryIds($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function getInactiveCategoryIds()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getInactiveCategoryIds');
        return $pluginInfo ? $this->___callPlugins('getInactiveCategoryIds', func_get_args(), $pluginInfo) : parent::getInactiveCategoryIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($sorted = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCollection');
        return $pluginInfo ? $this->___callPlugins('getCollection', func_get_args(), $pluginInfo) : parent::getCollection($sorted);
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection($collection)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCollection');
        return $pluginInfo ? $this->___callPlugins('setCollection', func_get_args(), $pluginInfo) : parent::setCollection($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function move($category, $newParent, $prevNode = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'move');
        return $pluginInfo ? $this->___callPlugins('move', func_get_args(), $pluginInfo) : parent::move($category, $newParent, $prevNode);
    }

    /**
     * {@inheritdoc}
     */
    public function loadByIds($ids, $addCollectionData = true, $updateAnchorProductCount = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadByIds');
        return $pluginInfo ? $this->___callPlugins('loadByIds', func_get_args(), $pluginInfo) : parent::loadByIds($ids, $addCollectionData, $updateAnchorProductCount);
    }

    /**
     * {@inheritdoc}
     */
    public function loadBreadcrumbsArray($path, $addCollectionData = true, $withRootNode = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadBreadcrumbsArray');
        return $pluginInfo ? $this->___callPlugins('loadBreadcrumbsArray', func_get_args(), $pluginInfo) : parent::loadBreadcrumbsArray($path, $addCollectionData, $withRootNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExistingCategoryIdsBySpecifiedIds($ids)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExistingCategoryIdsBySpecifiedIds');
        return $pluginInfo ? $this->___callPlugins('getExistingCategoryIdsBySpecifiedIds', func_get_args(), $pluginInfo) : parent::getExistingCategoryIdsBySpecifiedIds($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function getDbSelect()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDbSelect');
        return $pluginInfo ? $this->___callPlugins('getDbSelect', func_get_args(), $pluginInfo) : parent::getDbSelect();
    }

    /**
     * {@inheritdoc}
     */
    public function setDbSelect($select)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDbSelect');
        return $pluginInfo ? $this->___callPlugins('setDbSelect', func_get_args(), $pluginInfo) : parent::setDbSelect($select);
    }

    /**
     * {@inheritdoc}
     */
    public function load($parentNode = null, $recursionLevel = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'load');
        return $pluginInfo ? $this->___callPlugins('load', func_get_args(), $pluginInfo) : parent::load($parentNode, $recursionLevel);
    }

    /**
     * {@inheritdoc}
     */
    public function addChildNodes($children, $path, $parentNode, $level = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addChildNodes');
        return $pluginInfo ? $this->___callPlugins('addChildNodes', func_get_args(), $pluginInfo) : parent::addChildNodes($children, $path, $parentNode, $level);
    }

    /**
     * {@inheritdoc}
     */
    public function loadNode($nodeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadNode');
        return $pluginInfo ? $this->___callPlugins('loadNode', func_get_args(), $pluginInfo) : parent::loadNode($nodeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren($node, $recursive = true, $result = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChildren');
        return $pluginInfo ? $this->___callPlugins('getChildren', func_get_args(), $pluginInfo) : parent::getChildren($node, $recursive, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function loadEnsuredNodes($category, $rootNode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadEnsuredNodes');
        return $pluginInfo ? $this->___callPlugins('loadEnsuredNodes', func_get_args(), $pluginInfo) : parent::loadEnsuredNodes($category, $rootNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getTree()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTree');
        return $pluginInfo ? $this->___callPlugins('getTree', func_get_args(), $pluginInfo) : parent::getTree();
    }

    /**
     * {@inheritdoc}
     */
    public function appendChild($data, $parentNode, $prevNode = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'appendChild');
        return $pluginInfo ? $this->___callPlugins('appendChild', func_get_args(), $pluginInfo) : parent::appendChild($data, $parentNode, $prevNode);
    }

    /**
     * {@inheritdoc}
     */
    public function addNode($node, $parent = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addNode');
        return $pluginInfo ? $this->___callPlugins('addNode', func_get_args(), $pluginInfo) : parent::addNode($node, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function moveNodeTo($node, $parentNode, $prevNode = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'moveNodeTo');
        return $pluginInfo ? $this->___callPlugins('moveNodeTo', func_get_args(), $pluginInfo) : parent::moveNodeTo($node, $parentNode, $prevNode);
    }

    /**
     * {@inheritdoc}
     */
    public function copyNodeTo($node, $parentNode, $prevNode = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'copyNodeTo');
        return $pluginInfo ? $this->___callPlugins('copyNodeTo', func_get_args(), $pluginInfo) : parent::copyNodeTo($node, $parentNode, $prevNode);
    }

    /**
     * {@inheritdoc}
     */
    public function removeNode($node)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeNode');
        return $pluginInfo ? $this->___callPlugins('removeNode', func_get_args(), $pluginInfo) : parent::removeNode($node);
    }

    /**
     * {@inheritdoc}
     */
    public function createNode($parentNode, $prevNode = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createNode');
        return $pluginInfo ? $this->___callPlugins('createNode', func_get_args(), $pluginInfo) : parent::createNode($parentNode, $prevNode);
    }

    /**
     * {@inheritdoc}
     */
    public function getChild($node)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChild');
        return $pluginInfo ? $this->___callPlugins('getChild', func_get_args(), $pluginInfo) : parent::getChild($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getNodes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getNodes');
        return $pluginInfo ? $this->___callPlugins('getNodes', func_get_args(), $pluginInfo) : parent::getNodes();
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeById($nodeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getNodeById');
        return $pluginInfo ? $this->___callPlugins('getNodeById', func_get_args(), $pluginInfo) : parent::getNodeById($nodeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($node)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPath');
        return $pluginInfo ? $this->___callPlugins('getPath', func_get_args(), $pluginInfo) : parent::getPath($node);
    }
}
