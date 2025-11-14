<?php
namespace Vendor\ReviewDashboard\Controller\Review;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\RatingFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;

class Save extends Action
{
    protected $reviewFactory;
    protected $ratingFactory;
    protected $storeManager;
    protected $jsonFactory;
    protected $session;

    public function __construct(
        Context $context,
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory,
        StoreManagerInterface $storeManager,
        Session $session,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->storeManager = $storeManager;
        $this->jsonFactory = $jsonFactory;
        $this->session = $session;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();

        try {
            if (!$this->session->isLoggedIn()) {
                return $result->setData([
                    'success' => false,
                    'message' => 'Login required'
                ]);
            }

            $data = $this->getRequest()->getParams();
            $storeId = $this->storeManager->getStore()->getId();

            /** STEP 1: Create review */
            $review = $this->reviewFactory->create()->setData([
                'nickname'        => $data['nickname'],
                'title'           => $data['title'],
                'detail'          => $data['detail'],
                'status_id'       => \Magento\Review\Model\Review::STATUS_PENDING,
                'entity_id'       => 1, // PRODUCT ENTITY
                'entity_pk_value' => $data['product_id'],
                'customer_id'     => $this->session->getCustomerId(),
                'store_id'        => $storeId
            ]);

            $review->save();

            /** IMPORTANT: Assign store relation */
            $review->setStoreId($storeId)
                   ->setStores([$storeId])
                   ->save();

            /** STEP 2: Save Rating properly */
            if (!empty($data['rating'])) {

                // fetch default rating ID (usually 1 = "Rating")
                $ratingId = 1;

                $rating = $this->ratingFactory->create()
                    ->setRatingId($ratingId)
                    ->setReviewId($review->getId())
                    ->setCustomerId($this->session->getCustomerId());

                // Attach rating to product
                $rating->addOptionVote($data['rating'], $data['product_id']);
            }

            /** STEP 3: Aggregate review after rating */
            $review->aggregate();

            return $result->setData([
                'success' => true,
                'message' => 'Review submitted and pending approval.'
            ]);

        } catch (\Exception $e) {

            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
