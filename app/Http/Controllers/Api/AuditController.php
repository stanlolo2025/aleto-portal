<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct(
        private AuditLoggerInterface $auditLogger,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['event_type', 'user_id', 'villager_id', 'date_from', 'date_to']);
        $page = $request->get('page', 1);

        $logs = $this->auditLogger->query($filters, $page);

        return response()->json($logs);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['event_type', 'user_id', 'villager_id', 'date_from', 'date_to']);

        return $this->auditLogger->export($filters);
    }

    public function report(Request $request): JsonResponse
    {
        $filters = $request->only(['date_from', 'date_to']);

        $report = $this->auditLogger->generateReport($filters);

        return response()->json($report);
    }
}
