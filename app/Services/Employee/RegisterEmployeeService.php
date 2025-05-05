<?php

declare(strict_types=1);

namespace App\Services\Employee;

use App\Jobs\SendRegisterNotification;
use App\Mail\RegisterNotification;
use App\Models\User;
use App\Services\User\RegisterUserService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterEmployeeService
{
    public function __construct(
        private readonly RegisterUserService $registerUserService,
    ) {}

    public function execute(array $data): void
    {
        DB::transaction(function () use ($data): void {
            $user = $this->registerUserService->execute($data['user']['email'], $data['user']['password']);

            $user->employee()->create([
                'name' => $data['name'],
                'company_name' => $data['company_name'],
                'normal_hourly_rate' => $data['normal_hourly_rate'],
                'overtime_hourly_rate' => $data['overtime_hourly_rate'],
                'night_hourly_rate' => $data['night_hourly_rate'],
                'holiday_hourly_rate' => $data['holiday_hourly_rate'],
                'irpf' => $data['irpf'],
            ]);

            DB::afterCommit(function () use ($user) {
                $user->assignRole('employee');
                $this->sendRegisterNotification($user);
            });
        });
    }

    private function sendRegisterNotification(User $user): void
    {
        try {
           SendRegisterNotification::dispatch($user);
            Log::info('Notificación de registro enviada', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } catch (Exception $e) {
            Log::error('Error al enviar notificación de registro', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
        }
    }
}
