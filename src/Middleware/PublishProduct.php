<?php

namespace Mcms\Products\Middleware;

use Carbon\Carbon;
use Closure;
use Mcms\Products\Models\Product;

/**
 * Look for all products about to be published and activate them
 *
 * Class PublishProduct
 * @package Mcms\Products\Middleware
 */
class PublishProduct
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Product::where('published_at', '>=', Carbon::now())->update(['active'=> true]);

        return $next($request);
    }
}