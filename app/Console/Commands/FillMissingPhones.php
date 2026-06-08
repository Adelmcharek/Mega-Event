<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FillMissingPhones extends Command
{
    protected $signature   = 'users:fill-phones';
    protected $description = 'Assign a random Tunisian phone number to users with no phone';

    public function handle(): int
    {
        $prefixes = [
            '20', '22', '25', '26', '27', '28', '29',
            '50', '52', '53', '54', '55', '56', '58',
            '90', '92', '93', '95', '96', '97', '98', '99',
        ];

        $users = User::whereNull('phone')->orWhere('phone', '')->get();

        if ($users->isEmpty()) {
            $this->info('All users already have a phone number.');
            return self::SUCCESS;
        }

        foreach ($users as $user) {
            $prefix = $prefixes[array_rand($prefixes)];
            $number = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->phone = '+216 ' . $prefix . ' ' . substr($number, 0, 3) . ' ' . substr($number, 3);
            $user->save();
            $this->line("  {$user->email} → {$user->phone}");
        }

        $this->info("Done — {$users->count()} user(s) updated.");
        return self::SUCCESS;
    }
}
