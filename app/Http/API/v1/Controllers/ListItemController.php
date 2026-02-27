<?php

declare(strict_types=1);

namespace App\Http\API\v1\Controllers;

use App\Contracts\Services\ListServiceContract;
use App\Http\API\v1\Requests\ListItem\CreateRequest;
use App\Http\API\v1\Requests\ListItem\UpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ListItemController extends Controller
{
    public function __construct(
        private readonly ListServiceContract $listService,
    ) {
    }

    public function create(CreateRequest $request): JsonResponse
    {
        return response()->json(
            $this->listService->createListItem($request->toData()),
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        try {
            return response()->json($this->listService->updateListItem($request->toData()));
        } catch (\Exception $e) {
            throw new HttpResponseException(response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    public function delete()
    {

    }

    public function complete()
    {

    }
}
