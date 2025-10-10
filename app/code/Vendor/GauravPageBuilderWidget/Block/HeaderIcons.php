<?php
namespace Vendor\GauravPageBuilderWidget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Cart;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Customer\Model\Session as CustomerSession;

class HeaderIcons extends Template
{
    protected $cart;
    protected $wishlistFactory;
    protected $customerSession;

    public function __construct(
        Template\Context $context,
        Cart $cart,
        WishlistFactory $wishlistFactory,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->cart = $cart;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getCartCount()
    {
        return $this->cart->getQuote()->getItemsQty() ?? 0;
    }

    public function getWishlistCount()
    {
        if ($this->customerSession->isLoggedIn()) {
            $wishlist = $this->wishlistFactory->create()->loadByCustomerId($this->customerSession->getCustomerId(), true);
            return $wishlist->getItemCollection()->getSize();
        }
        return 0;
    }
}
