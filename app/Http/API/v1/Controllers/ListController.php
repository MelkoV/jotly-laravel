<?php

declare(strict_types=1);

namespace App\Http\API\v1\Controllers;

use App\Contracts\Services\ListServiceContract;
use App\Data\List\ListViewData;
use App\Enums\DeleteListType;
use App\Http\API\v1\Requests\List\CreateRequest;
use App\Http\API\v1\Requests\List\DeleteRequest;
use App\Http\API\v1\Requests\List\DeleteTypesRequest;
use App\Http\API\v1\Requests\List\FilteredListRequest;
use App\Http\API\v1\Requests\List\LeftRequest;
use App\Http\API\v1\Requests\List\UpdateRequest;
use App\Http\API\v1\Requests\List\ViewRequest;
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

    public function view(ViewRequest $request): JsonResponse
    {
        $data = $request->toData();
        return response()->json(new ListViewData(
            model: $this->listService->findById($data->id),
            items: $this->listService->getListItems($data->id)
        ));
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $data = $this->listService->update($request->toData());
        return response()->json($data);
    }

    public function left(LeftRequest $request): JsonResponse
    {
        $data = $request->toData();
        $this->listService->leftUser($data->id, $data->user_id);
        return response()->json(['success' => true]);
    }

    public function deleteTypes(DeleteTypesRequest $request): JsonResponse
    {
        $data = $request->toData();
        $list = $this->listService->findById($data->id);
        return response()->json([
            DeleteListType::Left->value => true,
            DeleteListType::Delete->value => $list->owner_id === $data->user_id,
        ]);
    }

    public function delete(DeleteRequest $request): JsonResponse
    {
        $data = $request->toData();
        $this->listService->delete($data->id);
        return response()->json(['success' => true]);
    }
}
