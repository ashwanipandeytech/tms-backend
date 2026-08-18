<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Quotation;
use App\Repositories\Contracts\QuotationRepositoryInterface;

class QuotationRepository extends BaseRepository implements QuotationRepositoryInterface
{
    public function __construct(Quotation $model)
    {
        parent::__construct($model);
    }
}
