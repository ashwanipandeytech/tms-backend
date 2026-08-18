<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\LeadCreatedEvent;
use App\Models\Model;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class LeadService extends BaseService
{
    public function __construct(LeadRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): EloquentModel
    {
        if (auth()->check()) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
        }

        $lead = parent::create($data);

        event(new LeadCreatedEvent($lead));

        return $lead;
    }
}
