<?php

namespace App\Http\Middleware;

@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

use App\Module\Base;
use App\Module\Doo;
use App\Module\Rsa;
use Closure;

class WebApi
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
        global $_A;
        $_A = [];

        if ($request->isMethod('post')) {
            $version = $request->header('version');
            if ($version && version_compare($version, '0.25.48', '<')) {
                // Older versions are compatible php://input
                parse_str($request->getContent(), $content);
                if ($content) {
                    $request->merge($content);
                }
                unset($content);
            } elseif ($request->header('encrypt') === "rsa") {
                // New version decrypts submitted content
                $encrypt = Rsa::decryptData($request->input('encrypt'));
                if ($encrypt) {
                    $request->merge(Base::json2array(urldecode($encrypt)));
                }
                unset($encrypt);
            }
        }

        $APP_SCHEME = env('APP_SCHEME', 'auto');
        if (in_array(strtolower($APP_SCHEME), ['https', 'on', 'ssl', '1', 'true', 'yes'], true)) {
            $request->setTrustedProxies([$request->getClientIp()], $request::HEADER_X_FORWARDED_PROTO);
        }

        Doo::load();

        return $next($request);
    }
}
