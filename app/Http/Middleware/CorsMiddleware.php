<?php
namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('OPTIONS'))
        {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        // List of allowed origins
        $allowedOrigins = [
            'https://ouis.unsa.edu.pe',
            'https://sg.unsa.edu.pe',
            // Add more origins as needed
        ];

        $origin = $request->header('Origin');

        if (in_array($origin, $allowedOrigins)) {
            $headers = [
                'Access-Control-Allow-Origin'      => $origin,
                'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age'           => '86400',
                'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
            ];

            $response = $next($request);

            foreach($headers as $key => $value)
            {
                $response->header($key, $value);
            }

            return $response;
        }

        // Deny access for disallowed origins
        return response('Unauthorized', 401);
    }
}