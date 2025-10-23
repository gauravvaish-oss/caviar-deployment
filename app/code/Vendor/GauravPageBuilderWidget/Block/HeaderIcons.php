<?php
namespace Vendor\GauravPageBuilderWidget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Cart;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Asset\Repository;

class HeaderIcons extends Template
{
    protected $cart;
    protected $wishlistFactory;
    protected $customerSession;
    protected $assetRepo;

    public function __construct(
        Template\Context $context,
        Cart $cart,
        WishlistFactory $wishlistFactory,
        CustomerSession $customerSession,
        Repository $assetRepo,
        array $data = []
    ) {
        $this->cart = $cart;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        $this->assetRepo = $assetRepo;
        parent::__construct($context, $data);
    }

    /**
     * Get cart item count (guest or logged-in)
     */
   public function getCartCount(): int
{
    // Reload the current quote to get latest items
    $quote = $this->cart->getQuote();
    // dd($quote);die;

    $quote->collectTotals()->save(); // ensures totals and item count are up-to-date
    return (int) $quote->getItemsQty();
}


    /**
     * Get wishlist count (logged-in only)
     */
    public function getWishlistCount(): int
    {
        if ($this->customerSession->isLoggedIn()) {
            $wishlist = $this->wishlistFactory
                ->create()
                ->loadByCustomerId($this->customerSession->getCustomerId(), true);
            return (int) $wishlist->getItemCollection()->getSize();
        }
        return 0;
    }

    /**
     * Get icon URL using asset repository
     */
    public function getIconUrl(string $iconFile): string
    {
        return $this->assetRepo->getUrl("Vendor_GauravPageBuilderWidget::images/{$iconFile}");
    }
}
