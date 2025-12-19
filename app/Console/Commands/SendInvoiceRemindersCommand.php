<?php

namespace App\Console\Commands;

use App\Jobs\SendInvoiceReminderJob;
use Illuminate\Console\Command;

class SendInvoiceRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email thông báo hóa đơn đến hạn cho người thuê';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang gửi thông báo hóa đơn đến hạn...');
        
        SendInvoiceReminderJob::dispatch();
        
        $this->info('Đã gửi thông báo thành công!');
        
        return Command::SUCCESS;
    }
}
