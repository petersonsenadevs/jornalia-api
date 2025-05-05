<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\HourSession\ShowHourSession;

use App\Exceptions\HourSessionNotFoundException;
use App\Http\Requests\ShowHourSessionRequest;
use App\Services\HourSession\FindHourSessionService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowHourSessionController
{
    /**
     * Summary of __construct
     */
    public function __construct(private FindHourSessionService $hourSessionShowService) {}

    /**
     * Summary of __invoke
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function __invoke(ShowHourSessionRequest $request): JsonResponse
    {
        try {
            $query = $request->query('date');

            $employee = $request->user()->employee;
            $hourSession = $this->hourSessionShowService->execute($employee->id, $query);

            return response()->json(['hour_session' => $hourSession], 200);
        } catch (HourSessionNotFoundException $exception) {
            throw new HttpResponseException(response()->json(['message' => $exception->getMessage()], $exception->getCode()));
        }

    }
}
