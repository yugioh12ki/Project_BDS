<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Transaction;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('check:db-structure', function () {
    $this->comment('=== Cấu trúc bảng appointments ===');
    
    try {
        // Kiểm tra cấu trúc bảng appointments
        $columns = DB::select("DESCRIBE appointments");
        foreach ($columns as $column) {
            $this->line("Field: {$column->Field} | Type: {$column->Type} | Null: {$column->Null} | Key: {$column->Key}");
        }
        
        $this->comment("\n=== Dữ liệu mẫu appointments ===");
        $appointments = Appointment::with(['property', 'user_owner', 'user_agent'])->take(3)->get();
        foreach ($appointments as $appointment) {
            $this->line("ID: {$appointment->AppointmentID}");
            $this->line("Title: {$appointment->TitleAppoint}");
            $this->line("Status: {$appointment->Status}");
            $this->line("PropertyID: {$appointment->PropertyID}");
            $this->line("OwnerID: {$appointment->OwnerID}");
            $this->line("AgentID: {$appointment->AgentID}");
            $this->line("CusID: {$appointment->CusID}");
            $this->line("Start: {$appointment->AppointmentDateStart}");
            $this->line("End: {$appointment->AppointmentDateEnd}");
            $this->line("---");
        }
        
        $this->comment("\n=== Cấu trúc bảng transactions ===");
        $columns = DB::select("DESCRIBE transactions");
        foreach ($columns as $column) {
            $this->line("Field: {$column->Field} | Type: {$column->Type} | Null: {$column->Null} | Key: {$column->Key}");
        }
        
        $this->comment("\n=== Dữ liệu mẫu transactions ===");
        $transactions = Transaction::with(['property', 'owner', 'agent'])->take(3)->get();
        foreach ($transactions as $transaction) {
            $this->line("ID: {$transaction->TransactionID}");
            $this->line("PropertyID: {$transaction->PropertyID}");
            $this->line("OwnerID: {$transaction->OwnerID}");
            $this->line("AgentID: {$transaction->AgentID}");
            $this->line("Status: {$transaction->StatusTransaction}");
            $this->line("Date: {$transaction->TransactionDate}");
            $this->line("---");
        }
        
    } catch (Exception $e) {
        $this->error("Lỗi: " . $e->getMessage());
    }
})->describe('Kiểm tra cấu trúc database');
