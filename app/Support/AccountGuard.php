<?php

namespace App\Support;

use App\Models\Account;
use RuntimeException;

class AccountGuard
{
    public static function cashOrBank(int $accountId): Account
    {
        $account = Account::query()->findOrFail($accountId);

        if (!$account->is_active || $account->type !== 'asset' || !in_array($account->subtype, ['cash', 'bank'], true)) {
            throw new RuntimeException('The selected payment account must be an active Cash or Bank account.');
        }

        return $account;
    }

    public static function expense(int $accountId): Account
    {
        $account = Account::query()->findOrFail($accountId);

        if (!$account->is_active || $account->type !== 'expense') {
            throw new RuntimeException('The selected expense account must be an active Expense account.');
        }

        return $account;
    }
}
