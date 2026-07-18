<?php

return [
    'hero' => [
        'title' => 'Thank you for your order!',
        'subtitle' => 'We have received your order and are processing it. Checking payment status...',
    ],

    'status' => [
        'checking' => 'Checking order status...',
        'waiting_payment' => 'Waiting for payment confirmation...',
        'processing_data' => 'Processing data...',
        'checking_payment' => 'Checking payment system...',
        'paid' => 'Paid',
        'error' => 'Error',
        'under_review' => 'Under Review',
        'processing' => 'Processing',
    ],

    'order' => [
        'order_number' => 'Order Number',
        'status' => 'Status',
    ],

    'messages' => [
        'payment_success_title' => 'Payment Successful!',
        'payment_success' => 'Your order has been successfully paid and is being processed. The report will be sent to the specified email.',
        'order_completed_title' => 'Order Completed!',
        'order_completed' => 'Your order has been successfully processed. The report has been sent to the specified email.',
        'order_cancelled_title' => 'Order Cancelled',
        'order_cancelled' => 'The order was cancelled. If payment was made, funds will be refunded. Please contact support.',
        'order_review_title' => 'Order Under Review',
        'order_review' => 'Your order is under additional review. This may take some time. We will contact you by email.',
        'payment_failed_title' => 'Payment Failed',
        'payment_failed' => 'Unfortunately, the payment was not completed. Please try placing your order again.',
        'order_expired_title' => 'Order Expired',
        'order_expired' => 'The payment time for the order has expired. Please place a new order.',
        'order_not_completed_title' => 'Order Not Completed',
        'order_not_completed' => 'The order was not completed. If payment was made, funds will be refunded. Please contact support.',
    ],

    'errors' => [
        'no_order' => 'Error',
        'no_order_message' => 'Order number not found. Please contact support.',
        'timeout_title' => 'Timeout Exceeded',
        'timeout_message' => 'Unable to check order status. Please check the status in your account or contact support.',
        'check_error_title' => 'Check Error',
        'check_error_message' => 'Error checking order status. Please contact support.',
        'connection_error_title' => 'Connection Error',
        'connection_error_message' => 'Unable to connect to the server. Please check your internet connection and refresh the page.',
    ],

    'actions' => [
        'home' => 'Home',
        'my_orders' => 'My Orders',
        'login' => 'Login to Account',
        'retry_payment' => 'Retry Payment',
        'view_report' => 'View Report',
    ],
];
