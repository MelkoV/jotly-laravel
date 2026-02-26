<?php

declare(strict_types=1);

namespace App\Http\API\v1\Controllers;

use App\Contracts\Services\ListServiceContract;
use App\Http\API\v1\Requests\List\CreateRequest;
use App\Http\API\v1\Requests\List\FilteredListRequest;
use App\Http\API\v1\Requests\List\UpdateRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ListController extends Controller
{
    public function __construct(
        private readonly ListServiceContract $listService,
    ) {
    }

    public function index(FilteredListRequest $request): JsonResponse
    {
        return response()->json($this->listService->getFilteredLists($request->toData()));
    }

    public function create(CreateRequest $request): JsonResponse
    {
        $data = $this->listService->create($request->toData());
        return response()->json($data, Response::HTTP_CREATED);
    }

    public function view()
    {

    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $data = $this->listService->update($request->toData());
        return response()->json($data);
    }

    public function deleteTypes()
    {

    }

    public function delete()
    {

    }
}
