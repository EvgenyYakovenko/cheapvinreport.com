<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vin;

    public $reportType;

    public $userName;

    public $reportUrl;

    public $reportKey;

    private $pdfData = null;

    private $pdfChecked = false;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->vin = $order->vin;
        $this->reportType = $order->report_type ?? '';
        $this->userName = $order->user->name ?? $order->email ?? null;
        $this->reportKey = $order->report_key;
        $this->reportUrl = route('view-report', ['report_key' => $order->report_key]);
    }

    public function getAttachment($report_key)
    {
        try {
            $apiKey = config('services.monolith.key');
            $apiMonolithUrl = rtrim((string) config('services.monolith.url'), '/');

            // Получаем base64 HTML отчета
            $url = $apiMonolithUrl.'/api/v4/view_report/?report_key='.$report_key;
            $response = Http::withHeaders([
                'API-KEY' => $apiKey,
            ])->withoutVerifying()->get($url);

            if (! $response->successful()) {
                \Illuminate\Support\Facades\Log::error('ReportMail: Failed to get report HTML', [
                    'report_key' => $report_key,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $base64 = $response->json()['base64'] ?? null;
            if (! $base64) {
                \Illuminate\Support\Facades\Log::error('ReportMail: No base64 content in response', ['report_key' => $report_key]);

                return null;
            }

            // Получаем PDF из base64
            $url = $apiMonolithUrl.'/api/v4/get_pdf';
            $response = Http::withHeaders([
                'API-KEY' => $apiKey,
            ])->withoutVerifying()->post($url, [
                'base64_content' => $base64,
                'vin' => $this->vin,
            ]);

            if (! $response->successful()) {
                \Illuminate\Support\Facades\Log::error('ReportMail: Failed to get PDF', [
                    'report_key' => $report_key,
                    'status' => $response->status(),
                ]);

                return null;
            }

            // Получаем бинарные данные PDF
            $pdf = $response->body();

            return $pdf;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ReportMail: Exception getting attachment', [
                'report_key' => $report_key,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.report.subject', ['vin' => $this->vin]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.Report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Временно отключена привязка PDF
        //return [];

        if (! $this->reportKey) {
            return [];
        }

        // Проверяем PDF заранее, чтобы не создавать Attachment если PDF недоступен
        if (! $this->pdfChecked) {
            $this->pdfData = $this->getAttachment($this->reportKey);
            $this->pdfChecked = true;
        }

        if ($this->pdfData === null) {
            \Illuminate\Support\Facades\Log::warning('ReportMail: PDF not available, sending email without attachment', [
                'report_key' => $this->reportKey,
            ]);

            return [];
        }

        // Сохраняем данные в локальную переменную для замыкания
        $pdfData = $this->pdfData;

        return [
            Attachment::fromData(
                fn () => $pdfData,
                'vin-report-'.$this->vin.'.pdf'
            )
                ->withMime('application/pdf'),
        ];
    }
}
