<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\HouseResident;
use App\Models\Resident;
use App\Utils\CommonResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HouseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $houseCode = request()->query('houseCode');
        $size = request()->query('size', 8);
        $houses = [];
        if ($houseCode != null) {
            $houses = House::with('currentResident.resident:id,fullname')
                ->where('house_code', 'like', '%' . $houseCode . '%')->paginate($size);
        } else {
            $houses = House::with('currentResident.resident:id,fullname')->paginate($size);
        }
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $houses]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $validated = Validator::make(request()->all(), [
            'house_code' => 'required|string',
            'is_occupied' => 'required|boolean',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $house = [
            'house_code' => request('house_code'),
            'is_occupied' => request('is_occupied')
        ];

        $statusCodeResponse = Response::HTTP_CREATED;
        $messageResponse = 'Created';
        $dataResponse = [];

        try {
            DB::beginTransaction();
            $dataResponse = House::create($house);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $statusCodeResponse = Response::HTTP_INTERNAL_SERVER_ERROR;
            $messageResponse = 'Error';
            $dataResponse = $e->getMessage();
        }

        return CommonResponse::commonResponse(
            $statusCodeResponse,
            $messageResponse,
            ['data' => $dataResponse]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $house = House::find($id);
        if ($house == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'House not found']
            );
        }
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $house]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id)
    {
        $validated = Validator::make(request()->all(), [
            'house_code' => 'string',
            'is_occupied' => 'boolean',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $house = House::find($id);
        if ($house == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'House not found']
            );
        }

        $updatedHouse = [
            'house_code' => request('house_code'),
            'is_occupied' => request('is_occupied')
        ];

        $house->update($updatedHouse);
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $house]
        );
    }

    public function addResident(string $houseId)
    {
        $validated = Validator::make(request()->all(), [
            'resident_id' => 'required|string',
            'start_date' => 'required|date',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $residentId = request('resident_id');

        $resident = Resident::find($residentId);
        if ($resident == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Resident not found']
            );
        }

        $house = House::find($houseId);
        if ($house == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'House not found']
            );
        }

        if ($house->is_occupied == false) {
            $house->is_occupied = true;
            $house->save();
        }

        $parsedDate = Carbon::parse(request('start_date'))->setTimezone(env('APP_TIMEZONE'))->toIso8601String();

        $house->houseResidents()->create([
            'resident_id' => request('resident_id'),
            'start_date' => $parsedDate
        ]);

        $response = [
            'newResident' => $resident,
        ];

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $response]
        );
    }

    public function deleteResident(string $id)
    {
        $validated = Validator::make(request()->all(), [
            'resident_id' => 'required|string',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $residentId = request('resident_id');

        $resident = Resident::find($residentId);
        if ($resident == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Resident not found']
            );
        }

        $houseResident = HouseResident::where('house_id', $id)
            ->where('resident_id', $residentId)
            ->whereNull('end_date')
            ->first();

        if ($houseResident == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'House resident not found']
            );
        }

        $houseResident->end_date = now();
        $houseResident->save();

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => null]
        );
    }
}
