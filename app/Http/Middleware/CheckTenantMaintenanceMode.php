<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain;

class CheckTenantMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $centralDomains = config('tenancy.central_domains', []);
        $host = $request->getHost();

        // Only enforce on tenant domains
        if (! in_array($host, $centralDomains, true)) {
            $domainRecord = Domain::where('domain', $host)->with('tenant')->first();

            $isStopped = false;
            $maintenanceData = null;

            if ($domainRecord) {
                if ($domainRecord->is_active === false) {
                    $isStopped = true;
                }
                if ($domainRecord->tenant && ! empty($domainRecord->tenant->maintenance_mode)) {
                    $isStopped = true;
                    $maintenanceData = $domainRecord->tenant->maintenance_mode;
                }
            } elseif (function_exists('tenant') && tenant()) {
                if (! empty(tenant('maintenance_mode'))) {
                    $isStopped = true;
                    $maintenanceData = tenant('maintenance_mode');
                }
            }

            if ($isStopped) {
                // If user is currently using the application, terminate the session immediately
                if (Auth::guard('tenant')->check()) {
                    Auth::guard('tenant')->logout();
                }
                if (Auth::guard('web')->check()) {
                    Auth::guard('web')->logout();
                }
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                $message = is_array($maintenanceData) && !empty($maintenanceData['message'])
                    ? $maintenanceData['message']
                    : 'This domain has been stopped by the administrator and is currently offline.';

                // Return JSON for API or AJAX requests
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'error' => 'Service Unavailable',
                        'message' => $message,
                        'is_maintenance' => true,
                    ], 503);
                }

                // Return 503 error view for browser requests
                return response()->view('errors.503', [
                    'domain' => $domainRecord?->domain ?? $host,
                    'company' => $domainRecord?->company ?? $host,
                    'message' => $message,
                ], 503);
            }
        }

        return $next($request);
    }
}

