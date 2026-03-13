<?php

namespace App\Http\Middleware;

use App\Models\AuditoriaLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        try {
            $route = $request->route();
            $routeUri = $route?->uri() ?? '';
            $segments = array_values(array_filter(explode('/', $routeUri)));
            $modulo = $segments[1] ?? $segments[0] ?? 'api';
            $payload = $request->except(['password', 'password_confirmation', 'token']);
            $user = $request->user();

            $entidade = null;
            $entidadeId = null;

            if ($route) {
                foreach ($route->parameters() as $parameter) {
                    if (is_object($parameter) && method_exists($parameter, 'getKey')) {
                        $entidade = class_basename($parameter);
                        $entidadeId = (string) $parameter->getKey();
                        break;
                    }

                    if (is_scalar($parameter)) {
                        $entidadeId = (string) $parameter;
                    }
                }
            }

            AuditoriaLog::query()->create([
                'user_id' => $user?->id,
                'acao' => strtolower($request->method()),
                'modulo' => $modulo,
                'entidade' => $entidade,
                'entidade_id' => $entidadeId,
                'metodo' => $request->method(),
                'rota' => '/'.$routeUri,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => $payload,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Never block request flow because of audit persistence issues.
        }

        return $response;
    }
}
