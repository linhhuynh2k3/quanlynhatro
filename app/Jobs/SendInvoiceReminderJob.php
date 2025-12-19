<?php

namespace App\Jobs;

use App\Mail\InvoiceReminderMail;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Lấy các hóa đơn sắp đến hạn (3 ngày trước hạn thanh toán) hoặc đã quá hạn
        $invoices = Invoice::where('status', 'pending')
            ->where(function($query) {
                $query->where('due_date', '<=', now()->addDays(3))
                      ->orWhere('due_date', '<', now());
            })
            ->whereDoesntHave('tenant', function($q) {
                // Có thể thêm điều kiện lọc nếu cần
            })
            ->with(['tenant', 'landlord', 'contract.listing'])
            ->get();

        foreach ($invoices as $invoice) {
            try {
                Mail::to($invoice->tenant->email)
                    ->send(new InvoiceReminderMail($invoice));
            } catch (\Exception $e) {
                // Log error but continue with other invoices
                \Log::error('Failed to send invoice reminder: ' . $e->getMessage());
            }
        }
    }
}
