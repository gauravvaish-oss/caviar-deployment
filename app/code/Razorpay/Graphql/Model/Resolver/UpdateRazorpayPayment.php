<?php
namespace Razorpay\Graphql\Model\Resolver;


use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Razorpay\Magento\Model\OrderLinkFactory;
use Razorpay\Magento\Model\ResourceModel\OrderLink\CollectionFactory as OrderLinkCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class UpdateRazorpayPayment implements ResolverInterface
{
protected $orderRepository;
protected $orderLinkFactory;
protected $orderLinkCollectionFactory;
protected $searchCriteriaBuilder;
protected $logger;


public function __construct(
OrderRepositoryInterface $orderRepository,
OrderLinkFactory $orderLinkFactory,
OrderLinkCollectionFactory $orderLinkCollectionFactory,
SearchCriteriaBuilder $searchCriteriaBuilder,
\Psr\Log\LoggerInterface $logger
) {
$this->orderRepository = $orderRepository;
$this->orderLinkFactory = $orderLinkFactory;
$this->orderLinkCollectionFactory = $orderLinkCollectionFactory;
$this->searchCriteriaBuilder = $searchCriteriaBuilder;
$this->logger = $logger;
}


public function resolve(
$field,
$context,
ResolveInfo $info,
array $value = null,
array $args = null
) {
if (!isset($args['input']) || !is_array($args['input'])) {
throw new GraphQlInputException(__('Invalid input.'));
}


$input = $args['input'];


if (empty($input['order_increment_id'])) {
throw new GraphQlInputException(__('order_increment_id is required.'));
}


try {
// Load order by increment id
$order = $this->loadOrderByIncrementId($input['order_increment_id']);


if (!$order || !$order->getEntityId()) {
throw new GraphQlNoSuchEntityException(__('Order not found.'));
}


$payment = $order->getPayment();


// Update sales_order_payment additional information
$map = [
'rzp_order_id' => 'rzp_order_id',
'rzp_payment_id' => 'rzp_payment_id',
'rzp_signature' => 'rzp_signature',
'rzp_status' => 'rzp_status',
'payment_method' => 'rzp_payment_method',
'bank' => 'rzp_bank',
'wallet' => 'rzp_wallet',
'vpa' => 'rzp_vpa',
'invoice_id' => 'rzp_invoice_id',
'refund_id' => 'rzp_refund_id',
'error_code' => 'rzp_error_code',
'error_description' => 'rzp_error_description',
];


foreach ($map as $inputKey => $storedKey) {
if (isset($input[$inputKey]) && $input[$inputKey] !== null) {
$payment->setAdditionalInformation($storedKey, $input[$inputKey]);
}
}

if (isset($input['amount_paid'])) {
$payment->setAdditionalInformation('amount_paid', $input['amount_paid']);
$order->setTotalPaid((float)$input['amount_paid']);
$order->setBaseTotalPaid((float)$input['amount_paid']);
}


if (isset($input['amount_authorized'])) {
$payment->setAdditionalInformation('amount_authorized', $input['amount_authorized']);
}


// Save payment and order
$payment->save();


// Update or create record in razorpay_sales_order
$this->saveOrderLink($order->getEntityId(), $input);


// Optionally change order state/status when payment is captured
if (!empty($input['set_order_status']) && !empty($input['rzp_status']) && strtolower($input['rzp_status']) === 'captured') {
// set to processing if not already
try {
if ($order->getState() !== \Magento\Sales\Model\Order::STATE_PROCESSING) {
$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
$order->setStatus('processing');
}
} catch (\Exception $e) {
$this->logger->error('Error setting order status: ' . $e->getMessage());
}
}


$this->orderRepository->save($order);


return ['success' => true, 'message' => __('Razorpay details updated successfully.')];


} catch (GraphQlNoSuchEntityException $e) {
throw $e;
} catch (GraphQlInputException $e) {
throw $e;
} catch (\Exception $e) {
$this->logger->error('GraphQL updateRazorpayPayment error: ' . $e->getMessage());
throw new GraphQlInputException(__('Unable to update Razorpay details.'));
}
}


protected function loadOrderByIncrementId($incrementId)
{
$searchCriteria = $this->searchCriteriaBuilder
->addFilter('increment_id', $incrementId, 'eq')
->create();


$orderList = $this->orderRepository->getList($searchCriteria);


$items = $orderList->getItems();
if (count($items) > 0) {
return array_shift($items);
}
return null;
}

protected function saveOrderLink($orderId, $input)
{
try {
// Try to load existing link by order_id
$collection = $this->orderLinkCollectionFactory->create();
$collection->addFieldToFilter('order_id', $orderId);
$item = $collection->getFirstItem();


if (!$item || !$item->getId()) {
$item = $this->orderLinkFactory->create();
$item->setOrderId($orderId);
}


if (isset($input['rzp_order_id'])) {
$item->setRzpOrderId($input['rzp_order_id']);
}
if (isset($input['rzp_payment_id'])) {
$item->setRzpPaymentId($input['rzp_payment_id']);
}
if (isset($input['rzp_signature'])) {
$item->setRzpSignature($input['rzp_signature']);
}


$item->save();


} catch (\Exception $e) {
$this->logger->error('Unable to save razorpay_sales_order link: ' . $e->getMessage());
}
}
}