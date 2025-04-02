<?php

namespace App\Http\Controllers\Api;

use App\DTO\Appointments\CreateAppointmentDTO;
use App\DTO\Appointments\EditAppointmentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAppointmentRequest;
use App\Http\Requests\Api\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Repositories\AppointmentRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $appointments = $this->appointmentRepository->getPaginate(
            totalPerPage: $request->total_per_page ?? 15,
            page: $request->page ?? 1,
            filter: $request->get('filter', '')
        );
        return AppointmentResource::collection($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = $this->appointmentRepository->createNew(new CreateAppointmentDTO(... $request->validated()));
        return new AppointmentResource($appointment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$appointment = $this->appointmentRepository->findById($id, ['user', 'location'])){
            return response()->json(['message' => 'appointment not found'], Response::HTTP_NOT_FOUND);
        }
        return new AppointmentResource($appointment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$response = $this->appointmentRepository->delete($id)){
            return response()->json(['message' => 'appointment not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

}
