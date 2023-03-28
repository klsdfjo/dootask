<?php

namespace App\Http\Middleware;

@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

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

        Rsa::load();
        Doo::load();

        $encrypt = Rsa::parseStr($request->header('encrypt'));
        if ($request->isMethod('post')) {
            $version = $request->header('version');
            if ($version && version_compare($version, '0.25.48', '<')) {
                // 旧版本兼容 php://input
                parse_str($request->getContent(), $content);
                if ($content) {
                    $request->merge($content);
                }
                unset($content);
            } elseif ($encrypt['encrypt_version'] === "rsa1.0") {
                // 新版本解密提交的内容
                $content = Rsa::decryptApiData($request->input('encrypted'));
                if ($content) {
                    $request->merge($content);
                }
                unset($content);
            }
        }

        // 强制 https
        $APP_SCHEME = env('APP_SCHEME', 'auto');
        if (in_array(strtolower($APP_SCHEME), ['https', 'on', 'ssl', '1', 'true', 'yes'], true)) {
            $request->setTrustedProxies([$request->getClientIp()], $request::HEADER_X_FORWARDED_PROTO);
        }

        $response = $next($request);

        // 加密返回内容
        if ($encrypt['client_public_key'] && !empty($response->getContent())) {
            $response->setContent(json_encode([
                'encrypted' => Rsa::encryptApiData($response->getContent(), $encrypt['client_public_key'])
            ]));
        }

        return $response;
    }
}
