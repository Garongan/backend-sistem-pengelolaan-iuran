<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Resident;
use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class HouseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $houses = House::paginate(8);
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
            'house_code' => request()->house_code,
            'is_occupied' => request()->is_occupied
        ];

        return CommonResponse::commonResponse(
            201,
            'Created',
            ['data' => House::create($house)]
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
                ['message' => 'House not found']
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

        $house = House::find($id);
        if ($house == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['message' => 'House not found']
            );
        }

        $house->subs_id = request()->subs_id;
        $house->is_occupied = request()->is_occupied;
        $house->save();

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $house]
        );
    }

    public function addResident()
    {
        $validated = Validator::make(request()->all(), [
            'resident_id' => 'required|string',
            'house_id' => 'required|string',
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
                ['message' => 'Resident not found']
            );
        }

        $houseId = request('house_id');

        $house = House::find($houseId);
        if ($house == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['message' => 'House not found']
            );
        }

        $resident->house_id = $house->id;
        $house->is_occupied = true;
        $resident->save();
        $house->save();

        $response = [
            'newResident' => $resident,
        ];

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $response]
        );
    }

    public function updateResident()
    {
        $validated = Validator::make(request()->all(), [
            'old_resident_id' => 'required|string',
            'new_resident_id' => 'required|string',
            'house_id' => 'required|string',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        
    }
}
