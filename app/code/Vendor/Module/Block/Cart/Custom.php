<?php
namespace Vendor\Module\Block\Cart;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Custom extends Template
{
    protected $cart;
    protected $checkoutSession;
    protected $priceHelper;

    public function __construct(
        Template\Context $context,
        Cart $cart,
        CheckoutSession $checkoutSession,
        PriceHelper $priceHelper,
        array $data = []
    ) {
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
        parent::__construct($context, $data);
    }

    public function getItems()
    {
        return $this->cart->getQuote()->getAllVisibleItems();
    }

    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    public function getSubtotal()
    {
        return $this->getFormattedPrice($this->cart->getQuote()->getSubtotal());
    }

    public function getGrandTotal()
    {
        return $this->getFormattedPrice($this->cart->getQuote()->getGrandTotal());
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('checkout/cart/updatePost');
    }

    public function getRemoveUrl($item)
    {
        return $this->getUrl('checkout/cart/delete', ['id' => $item->getItemId()]);
    }

    public function getProductUrl($item)
    {
        return $item->getProduct()->getProductUrl();
    }

    public function getProductImage($item)
    {
        return $item->getProduct()->getMediaGalleryImages()->getFirstItem()->getUrl() ?? $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
    }

    /**
 * Get current quote
 *
 * @return \Magento\Quote\Model\Quote
 */
public function getQuote()
{
    return $this->checkoutSession->getQuote();
}

public function getRemovePostData($item)
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $formKey = $objectManager->get(\Magento\Framework\Data\Form\FormKey::class)->getFormKey();
    $urlBuilder = $objectManager->get(\Magento\Framework\UrlInterface::class);

    $uenc = $urlBuilder->getUrl('*/*/*', ['_current' => true]); // current page encoded

    return json_encode([
        'action' => $this->getUrl('checkout/cart/delete'), // Magento delete action
        'data' => [
            'id' => $item->getItemId(),
            'uenc' => base64_encode($uenc),
            'form_key' => $formKey
        ]
    ]);
}

}
