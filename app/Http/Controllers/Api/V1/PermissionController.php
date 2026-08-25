<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $permissions = Permission::all()->groupBy('module');

        $formatted = [];
        foreach ($permissions as $module => $items) {
            $formatted[] = [
                'module'      => $module,
                'permissions' => $items->map(fn($p) => [
                    'id'          => $p->id,
                    'action'      => $p->action,
                    'description' => $p->description,
                ])->values(),
            ];
        }

        return $this->successResponse($formatted, 'Permissions retrieved successfully');
    }
}
