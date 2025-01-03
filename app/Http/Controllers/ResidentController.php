<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ResidentController extends Controller
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
            'data.*.house_id' => 'required|string',
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


        $identityCardImageUrl = request()->file('identity_card_image')->store('identity_cards', 'public');
        $data = json_decode(request('data'));
        $resident = [
            'house_id' => $data->house_id,
            'fullname' => $data->fullname,
            'indentity_card_url' => $identityCardImageUrl,
            'is_permanent_resident' => $data->is_permanent_resident,
            'phone_number' => $data->phone_number,
            'is_married' => $data->is_married
        ];

        return CommonResponse::commonResponse(
            201,
            'Created',
            ['data' => Resident::create($resident)]
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
            'data.*.house_id' => 'required|string',
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
        
        $resident = Resident::find($id);
        if ($resident == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['message' => 'Resident not found']
            );
        }
        
        unlink(storage_path('app/public/' . $resident->indentity_card_url));
        $identityCardImageUrl = request()->file('identity_card_image')->store('identity_cards', 'public');

        $data = json_decode(request('data'));
        $resident->house_id = $data->house_id;
        $resident->fullname = $data->fullname;
        $resident->indentity_card_url = $identityCardImageUrl;
        $resident->is_permanent_resident = $data->is_permanent_resident;
        $resident->phone_number = $data->phone_number;
        $resident->is_married = $data->is_married;
        $resident->save();

        return CommonResponse::commonResponse(
            200,
            'Updated',
            ['data' => $resident]
        );
    }
}
