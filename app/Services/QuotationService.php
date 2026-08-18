<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\QuotationRepositoryInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class QuotationService extends BaseService
{
    public function __construct(QuotationRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): EloquentModel
    {
        if (empty($data['quotation_no'])) {
            $data['quotation_no'] = 'QT-' . strtoupper(uniqid());
        }

        if (auth()->check()) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
        }

        return parent::create($data);
    }
}
