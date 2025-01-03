<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Resident;
use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ResidentController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $residents = Resident::paginate(8);
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $residents]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $validated = Validator::make(request()->all(), [
            'data.*.fullname' => 'required|string',
            'data.*.is_permanent_resident' => 'required|boolean',
            'data.*.phone_number' => 'required|min:10',
            'data.*.is_married' => 'required|boolean',
            'identity_card_image' => 'required|image:jpg,png|max:2048',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $data = json_decode(request('data'));
        $identityCardImageUrl = request()->file('identity_card_image')->store('identity_cards', 'public');

        $resident = [
            'fullname' => $data->fullname,
            'indentity_card_url' => $identityCardImageUrl,
            'is_permanent_resident' => $data->is_permanent_resident,
            'phone_number' => $data->phone_number,
            'is_married' => $data->is_married
        ];

        $response = Resident::create($resident);
        return CommonResponse::commonResponse(
            201,
            'Created',
            ['data' => $response]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resident = Resident::find($id);
        if ($resident == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['message' => 'Resident not found']
            );
        }
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $resident]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id)
    {
        $validated = Validator::make(request()->all(), [
            'data.*.fullname' => 'nullable|string',
            'data.*.is_permanent_resident' => 'nullable|boolean',
            'data.*.phone_number' => 'nullable|min:10',
            'data.*.is_married' => 'nullable|boolean',
            'identity_card_image' => 'nullable|image:jpg,png|max:2048',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $resident = Resident::find($id);
        if ($resident == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['message' => 'Resident not found']
            );
        }

        $identityCardImageUrl = request()->file('identity_card_image')->store('identity_cards', 'public');
        $data = json_decode(request('data'));

        $resident->fullname = $data->fullname;
        $resident->indentity_card_url = $identityCardImageUrl;
        $resident->is_permanent_resident = $data->is_permanent_resident;
        $resident->phone_number = $data->phone_number;
        $resident->is_married = $data->is_married;
        $resident->save();

        unlink(storage_path('app/public/' . $resident->indentity_card_url));
        return CommonResponse::commonResponse(
            200,
            'Updated',
            ['data' => $resident]
        );
    }
}
