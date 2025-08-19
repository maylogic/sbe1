<?php

namespace App\GraphQL\Resolvers;

use App\Models\Base;
use App\Models\BasePreview;
use App\Services\BaseService;

readonly class BaseContent
{
    public function __construct(private BaseService $baseService)
    {
    }

    public function __invoke(Base|BasePreview $rootValue): string
    {
        return $this->baseService->getContent($rootValue);
    }
}
