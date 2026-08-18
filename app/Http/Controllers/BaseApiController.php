<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller;

abstract class BaseApiController extends Controller
{
    use ApiResponseTrait;
}
