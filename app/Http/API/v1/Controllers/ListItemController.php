<?php

declare(strict_types=1);

namespace App\Http\API\v1\Controllers;

use App\Contracts\Services\ListServiceContract;

class ListItemController extends Controller
{
    public function __construct(
        private readonly ListServiceContract $listService,
    ) {
    }

    public function create()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function complete()
    {

    }
}
