<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuditService;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $auditService;
    public function __construct(AuditService $auditService) {
        $this->auditService = $auditService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);

        $routeName = $request->route() ? $request->route()->getName() : 'unknown';
        $method = $request->method();

        $this->auditService->log(
            'api_access',
            "API Access: {$method} {$routeName}",
            null,
            [
                'enpoint' => $routeName,
                'method' => $method,
                'params' => $request->all(),
            ]
        );

        return $response;

    }
}
