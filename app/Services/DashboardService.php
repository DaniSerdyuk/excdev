<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DashboardService
{
    /**
     * @param User $user
     *
     * @return User
     */
    public function getUserWithBalance(User $user): User
    {
        return $user->load([
            'balance:id,amount,user_id',
            'transactions' => fn($q) => $q->latest('created_at')->limit(5),
        ]);
    }

    /**
     * @param int         $id
     * @param int         $perPage
     * @param int         $page
     * @param string      $sort
     * @param string|null $search
     *
     * @return LengthAwarePaginator
     */
    public function getTransactions(int $id, int $perPage, int $page, string $sort = 'asc', ?string $search = null): LengthAwarePaginator
    {
        return Transaction::query()
            ->select(['id', 'amount', 'type', 'description', 'created_at'])
            ->where('user_id', $id)
            ->when($search, fn($q) => $q->where('description', 'like', sprintf('%%%s%%', $search)))
            ->orderBy('created_at', $sort)
            ->paginate(perPage: $perPage, page: $page);
    }
}
