<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\HouseResident;
use App\Models\Resident;
use App\Utils\CommonResponse;
use Carbon\Carbon;
use Illuminate\Http\Response;
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
            $houses = House::with('currentResident.resident')->where('house_code', 'like', '%' . $houseCode . '%')->paginate($size);
        } else {
            $houses = House::with('currentResident.resident')->paginate($size);
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

        try {
            $response = House::create($house);
            return CommonResponse::commonResponse(
                Response::HTTP_CREATED,
                'Created',
                ['data' => House::create($response)]
            );
        } catch (\Throwable $th) {
            return CommonResponse::commonResponse(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Error',
                ['data' => $th->errorInfo[2]]
            );
        }

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

        $parsedDate = Carbon::parse(request('start_date'));

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
