<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\LookupService;

/**
 * Class LookupController
 *
 * @package App\Http\Controllers
 */
class LookupController extends Controller
{
    public function lookup(LookupService $lookupService)
    {
        return $lookupService->getLookup();
    }
}
