<?php
namespace Vendor\GauravPageBuilderWidget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Cart;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\CartRepositoryInterface;

class HeaderIcons extends Template
{
    protected $cart;
    protected $wishlistFactory;
    protected $customerSession;
    protected $quoteRepository;

    public function __construct(
        Template\Context $context,
        Cart $cart,
        WishlistFactory $wishlistFactory,
        CustomerSession $customerSession,
        CartRepositoryInterface $quoteRepository,
        array $data = []
    ) {
        $this->cart = $cart;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;

        parent::__construct($context, $data);
    }

    /**
     * Check if customer is logged in
     */
    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get Cart Item Count
     */
    public function getCartCount()
    {
        try {
            $quote = $this->cart->getQuote();

            if ($quote && $quote->getId()) {
                return (int) $quote->getItemsQty();
            }
        } catch (\Exception $e) {
            // Fallback return 0
        }

        return 0;
    }

    /**
     * Get Wishlist Item Count
     */
    public function getWishlistCount()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return 0;
        }

        try {
            $customerId = $this->customerSession->getCustomerId();
            $wishlist   = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);

            if ($wishlist && $wishlist->getId()) {
                return (int) $wishlist->getItemCollection()->getSize();
            }
        } catch (\Exception $e) {
            // Fallback return 0
        }

        return 0;
    }
}
