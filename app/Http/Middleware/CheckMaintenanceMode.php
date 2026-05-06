<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se estiver no painel administrativo, permite acesso
        if ($request->is('admin*') || $request->is('livewire*')) {
            return $next($request);
        }

        $maintenanceMode = Setting::get('security.maintenance_mode', false);

        if ($maintenanceMode) {
            // Se o usuário estiver logado como admin, permite o acesso para "preview"
            if (auth()->check()) {
                return $next($request);
            }

            $message = Setting::get('security.maintenance_message', 'Estamos em manutenção. Voltamos em breve!');
            
            return response()->view('errors.maintenance', [
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
