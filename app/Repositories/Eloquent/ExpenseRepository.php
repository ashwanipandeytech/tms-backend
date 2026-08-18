<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{
    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }
}
