<?php

namespace App\GraphQL\Resolvers;

use App\Models\Base;
use App\Services\BaseService;

readonly class BaseContent
{
    public function __construct(private BaseService $baseService)
    {
    }

    public function __invoke(Base $rootValue): string
    {
        return $this->baseService->getContent($rootValue);
    }
}
