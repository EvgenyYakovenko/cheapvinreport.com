<?php

return [
    'hero' => [
        'title' => 'Dziękujemy za zamówienie!',
        'subtitle' => 'Otrzymaliśmy Twoje zamówienie i przetwarzamy je. Sprawdzamy status płatności...',
    ],

    'status' => [
        'checking' => 'Sprawdzanie statusu zamówienia...',
        'waiting_payment' => 'Oczekiwanie na potwierdzenie płatności...',
        'processing_data' => 'Przetwarzanie danych...',
        'checking_payment' => 'Sprawdzanie systemu płatności...',
        'paid' => 'Opłacone',
        'error' => 'Błąd',
        'under_review' => 'W trakcie weryfikacji',
        'processing' => 'Przetwarzanie',
    ],

    'order' => [
        'order_number' => 'Numer zamówienia',
        'status' => 'Status',
    ],

    'messages' => [
        'payment_success_title' => 'Płatność zakończona sukcesem!',
        'payment_success' => 'Twoje zamówienie zostało pomyślnie opłacone i jest przetwarzane. Raport zostanie wysłany na podany adres e-mail.',
        'order_completed_title' => 'Zamówienie zakończone!',
        'order_completed' => 'Twoje zamówienie zostało pomyślnie przetworzone. Raport został wysłany na podany adres e-mail.',
        'order_cancelled_title' => 'Zamówienie anulowane',
        'order_cancelled' => 'Zamówienie zostało anulowane. Jeśli płatność została dokonana, środki zostaną zwrócone. Proszę skontaktować się z obsługą.',
        'order_review_title' => 'Zamówienie w weryfikacji',
        'order_review' => 'Twoje zamówienie jest w trakcie dodatkowej weryfikacji. Może to zająć trochę czasu. Skontaktujemy się z Tobą e-mailem.',
        'payment_failed_title' => 'Płatność nie powiodła się',
        'payment_failed' => 'Niestety, płatność nie została zakończona. Proszę spróbować ponownie złożyć zamówienie.',
        'order_expired_title' => 'Zamówienie wygasło',
        'order_expired' => 'Czas na płatność zamówienia wygasł. Proszę złożyć nowe zamówienie.',
        'order_not_completed_title' => 'Zamówienie nie zostało zakończone',
        'order_not_completed' => 'Zamówienie nie zostało zakończone. Jeśli płatność została dokonana, środki zostaną zwrócone. Proszę skontaktować się z obsługą.',
    ],

    'errors' => [
        'no_order' => 'Błąd',
        'no_order_message' => 'Nie znaleziono numeru zamówienia. Proszę skontaktować się z obsługą.',
        'timeout_title' => 'Przekroczono czas oczekiwania',
        'timeout_message' => 'Nie udało się sprawdzić statusu zamówienia. Proszę sprawdzić status w panelu użytkownika lub skontaktować się z obsługą.',
        'check_error_title' => 'Błąd sprawdzania',
        'check_error_message' => 'Błąd podczas sprawdzania statusu zamówienia. Proszę skontaktować się z obsługą.',
        'connection_error_title' => 'Błąd połączenia',
        'connection_error_message' => 'Nie udało się połączyć z serwerem. Proszę sprawdzić połączenie internetowe i odświeżyć stronę.',
    ],

    'actions' => [
        'home' => 'Strona główna',
        'my_orders' => 'Moje zamówienia',
        'login' => 'Zaloguj się',
        'retry_payment' => 'Spróbuj ponownie',
        'view_report' => 'Zobacz Raport',
    ],
];
