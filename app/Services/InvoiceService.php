<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class InvoiceService extends BaseService
{
    public function __construct(InvoiceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): EloquentModel
    {
        if (empty($data['invoice_no'])) {
            $data['invoice_no'] = 'INV-' . strtoupper(uniqid());
        }

        $amount = (float)($data['amount'] ?? 0);
        $gst    = (float)($data['gst_amount'] ?? 0);
        $data['total'] = $amount + $gst;

        return parent::create($data);
    }
}
