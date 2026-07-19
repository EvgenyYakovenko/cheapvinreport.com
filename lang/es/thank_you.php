<?php

return [
    'hero' => [
        'title' => '¡Gracias por su pedido!',
        'subtitle' => 'Hemos recibido su pedido y lo estamos procesando. Verificando el estado del pago...',
    ],

    'status' => [
        'checking' => 'Verificando el estado del pedido...',
        'waiting_payment' => 'Esperando confirmación del pago...',
        'processing_data' => 'Procesando datos...',
        'checking_payment' => 'Verificando el sistema de pago...',
        'paid' => 'Pagado',
        'error' => 'Error',
        'under_review' => 'En Revisión',
        'processing' => 'Procesando',
    ],

    'order' => [
        'order_number' => 'Número de Pedido',
        'status' => 'Estado',
    ],

    'messages' => [
        'payment_success_title' => '¡Pago Exitoso!',
        'payment_success' => 'Su pedido ha sido pagado exitosamente y está siendo procesado. El informe se enviará al correo electrónico especificado.',
        'order_completed_title' => '¡Pedido Completado!',
        'order_completed' => 'Su pedido ha sido procesado exitosamente. El informe ha sido enviado al correo electrónico especificado.',
        'order_cancelled_title' => 'Pedido Cancelado',
        'order_cancelled' => 'El pedido fue cancelado. Si se realizó el pago, los fondos serán reembolsados. Por favor, contacte con soporte.',
        'order_review_title' => 'Pedido en Revisión',
        'order_review' => 'Su pedido está bajo revisión adicional. Esto puede tomar algún tiempo. Nos pondremos en contacto con usted por correo electrónico.',
        'payment_failed_title' => 'Pago Fallido',
        'payment_failed' => 'Desafortunadamente, el pago no se completó. Por favor, intente realizar su pedido nuevamente.',
        'order_expired_title' => 'Pedido Expirado',
        'order_expired' => 'El tiempo de pago del pedido ha expirado. Por favor, realice un nuevo pedido.',
        'order_not_completed_title' => 'Pedido No Completado',
        'order_not_completed' => 'El pedido no fue completado. Si se realizó el pago, los fondos serán reembolsados. Por favor, contacte con soporte.',
    ],

    'errors' => [
        'no_order' => 'Error',
        'no_order_message' => 'Número de pedido no encontrado. Por favor, contacte con soporte.',
        'timeout_title' => 'Tiempo de Espera Excedido',
        'timeout_message' => 'No se pudo verificar el estado del pedido. Por favor, verifique el estado en su cuenta o contacte con soporte.',
        'check_error_title' => 'Error de Verificación',
        'check_error_message' => 'Error al verificar el estado del pedido. Por favor, contacte con soporte.',
        'connection_error_title' => 'Error de Conexión',
        'connection_error_message' => 'No se pudo conectar al servidor. Por favor, verifique su conexión a internet y actualice la página.',
    ],

    'actions' => [
        'home' => 'Inicio',
        'my_orders' => 'Mis Pedidos',
        'login' => 'Iniciar Sesión',
        'retry_payment' => 'Reintentar Pago',
        'view_report' => 'Ver Informe',
    ],
];
