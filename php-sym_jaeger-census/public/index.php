<?php


// // $hexId = '7bba131ec63747139faca910a8300e43';
// $hexId = '7bba131ec6374713ffffffffffffffff';

// var_dump(str_split(
//     substr(
//         str_pad($hexId, 32, "0", STR_PAD_LEFT),
//         -32
//     ),
//     16
// ));
// function gmp_hexdec_int64s($hex) {
//     $MAX_INT = '9223372036854775807';
//     $dec = 0;
//     $len = strlen($hex);
//     for ($i = 1; $i <= $len; $i++) {
//         $dec = gmp_add($dec, gmp_mul(strval(hexdec($hex[$i - 1])), gmp_pow('16', strval($len - $i))));
//     }
//     if (gmp_cmp($dec, $MAX_INT) > 0) {
//         $dec = gmp_sub(gmp_and($dec, $MAX_INT), gmp_add($MAX_INT, '1'));
//     }
//     return intval($dec);
// }
// function bc_hexdec_int64s($hex) {
//     $MAX_INT = '9223372036854775807';
//     $dec = 0;
//     $len = strlen($hex);
//     for ($i = 1; $i <= $len; $i++) {
//         $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
//     }
//     if (bccomp($dec, $MAX_INT) > 0) {
//         $dec = bcsub(bcsub($dec, $MAX_INT), bcadd($MAX_INT, '1'));
//     }
//     return intval($dec);
// }
// var_dump(array_slice(
//     array_map(
//         'gmp_hexdec_int64s',
//         // 'bc_hexdec_int64s',
//         str_split(
//             substr(
//                 str_pad($hexId, 32, "0", STR_PAD_LEFT),
//                 -32
//             ),
//             16
//         )
//     ),
//     0,
//     2
// ));

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

// The check is to ensure we don't use .env in production
if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__.'/../.env');
}

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? ('prod' !== $env));

if ($debug) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);