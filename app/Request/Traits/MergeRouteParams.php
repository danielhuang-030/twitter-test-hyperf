<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Request\Traits;

use Hyperf\HttpServer\Router\Dispatched;

trait MergeRouteParams
{
    /**
     * validation data.
     */
    protected function validationData(): array
    {
        $routeParams = [];
        $route = $this->getAttribute(Dispatched::class);
        if (! empty($route)) {
            $routeParams = $route->params;
        }
        return array_merge_recursive($routeParams, parent::validationData());
    }
}
