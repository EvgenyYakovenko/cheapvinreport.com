<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService
{
    public static function topupBalance($user_id, $type, $amount)
    {
        $user = User::where('id', $user_id)->first();
        if (! $user) {
            Log::error('UserService: User not found.', ['user_id' => $user_id]);

            return;
        }
        if ($type == 'topup_report_balance') {
            $amount = (int) $amount;
            if ($amount <= 0 || ! is_numeric($amount)) {
                Log::error('UserService: Invalid amount for topup_report_balance.', ['user_id' => $user_id, 'amount' => $amount]);

                return;
            }
            $user->increment('report_balance', $amount);
//            Log::info('UserService: User ID '.$user_id.' report balance updated to '.$user->report_balance.' for type '.$type.' and amount '.$amount.'.');
        } elseif ($type == 'topup_balance') {
            $amount = (int) $amount;
            if ($amount <= 0 || ! is_numeric($amount)) {
                Log::error('UserService: Invalid amount for topup_balance.', ['user_id' => $user_id, 'amount' => $amount]);

                return;
            }
            $user->increment('balance', $amount);
//            Log::info('UserService: User ID '.$user_id.' balance updated to '.$user->balance.' for type '.$type.' and amount '.$amount.'.');
        } else {
            Log::error('UserService: Invalid type for topup balance.', ['user_id' => $user_id, 'type' => $type]);

            return;
        }
    }

    public static function useBalance($user_id, $amount)
    {
        $user = User::where('id', $user_id)->first();
        if (! $user) {
            Log::error('UserService: User not found.', ['user_id' => $user_id]);

            return false;
        }
        if ($user->balance <= 0) {
            Log::error('UserService: User ID '.$user_id.' balance is 0.', ['user_id' => $user_id, 'amount' => $amount, 'balance' => $user->balance]);

            return false;
        }
        if ($user->balance < $amount) {
            Log::error('UserService: User ID '.$user_id.' balance is less than the amount to use.', ['user_id' => $user_id, 'amount' => $amount, 'balance' => $user->balance]);

            return false;
        }
        $user->balance -= $amount;
        $user->save();
//        Log::info('UserService: User ID '.$user_id.' balance updated to '.$user->balance.' for amount '.$amount.'.');

        return true;
    }

    public static function useReportBalance($user_id, $amount)
    {
        $user = User::where('id', $user_id)->first();
        if (! $user) {
            Log::error('UserService: User not found.', ['user_id' => $user_id]);

            return false;
        }
        if ($user->report_balance <= 0) {
            Log::error('UserService: User ID '.$user_id.' report balance is 0.', ['user_id' => $user_id, 'amount' => $amount, 'report_balance' => $user->report_balance]);

            return false;
        }
        if ($user->report_balance < $amount) {
            Log::error('UserService: User ID '.$user_id.' report balance is less than the amount to use.', ['user_id' => $user_id, 'amount' => $amount, 'report_balance' => $user->report_balance]);

            return false;
        }
        $user->report_balance -= $amount;
        $user->save();
//        Log::info('UserService: User ID '.$user_id.' report balance updated to '.$user->report_balance.' for amount '.$amount.'.');

        return true;
    }
}
