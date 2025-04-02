<?php

namespace App\Http\Controllers\Api;

use App\DTO\Locations\CreateLocationDTO;
use App\DTO\Locations\EditLocationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLocationRequest;
use App\Http\Requests\Api\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    public function __construct(
        private LocationRepository $locationRepository,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locations = $this->locationRepository->getPaginate(
            totalPerPage: $request->total_per_page ?? 15,
            page: $request->page ?? 1,
            filter: $request->get('filter', '')
        );
        return LocationResource::collection($locations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        $location = $this->locationRepository->createNew(new CreateLocationDTO(... $request->validated()));
        return new LocationResource($location);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$location = $this->locationRepository->findById($id)){
            return response()->json(['message' => 'location not found'], Response::HTTP_NOT_FOUND);
        }
        return new LocationResource($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, string $id)
    {
        $response = $this->locationRepository->update(new EditLocationDTO(...[$id, ...$request->validated()]));
        if (!$response){
            return response()->json(['message' => 'location not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'location updated with sucess']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$response = $this->locationRepository->delete($id)){
            return response()->json(['message' => 'location not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
