<?php

namespace App\Services;

use App\Models\Base;
use App\Models\BasePreview;

class BaseService
{
    public function getContent(Base|BasePreview $base): string
    {
        $file = storage_path('app/public/' . $base->filename);
        return file_get_contents($file);
    }
}
