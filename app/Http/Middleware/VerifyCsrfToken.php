<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    //图片上传的路径
    protected $except = [
        '/service/pay/ali_notify',
        '/service/pay/wx_notify',
        '/service/upload/images',
    ];
}
