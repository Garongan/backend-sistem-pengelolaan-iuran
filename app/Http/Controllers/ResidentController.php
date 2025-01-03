<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResidentRequest;
use App\Http\Requests\UpdateResidentRequest;
use App\Models\Resident;
use App\Utils\CommonResponse;
use Error;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResidentController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $residents = Resident::paginate(8);
        return CommonResponse::commonResponse(
            200,
            'Success',
            ['data' => $residents]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        throw new NotFoundHttpException();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResidentRequest $request)
    {
        $identityCardImageUrl = $request->file('identity_card_image')->store('identity_cards', 'public');
        $data = json_decode($request->data);
        $resident = [
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
                404,
                'Error',
                ['message' => 'Resident not found']
            );
        }
        return CommonResponse::commonResponse(
            200,
            'Success',
            ['data' => $resident]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resident $resident)
    {
        throw new NotFoundHttpException();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResidentRequest $request, string $id)
    {
        return $request;
        $resident = Resident::find($id);
        if ($resident == null) {
            return CommonResponse::commonResponse(
                404,
                'Error',
                ['message' => 'Resident not found']
            );
        }
        unlink(storage_path('app/public/' . $resident->indentity_card_url));
        $identityCardImageUrl = $request->file('identity_card_image')->store('identity_cards', 'public');

        $data = json_decode($request->data);
        $resident->fullname = $data->fullname;
        $resident->indentity_card_url = $data->fullname;
        $resident->fullname = $identityCardImageUrl;
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resident = Resident::find($id);
        if ($resident == null) {
            return CommonResponse::commonResponse(
                404,
                'Error',
                ['message' => 'Resident not found']
            );
        }
        unlink(storage_path('app/public/' . $resident->indentity_card_url));
        $resident->delete();
        return CommonResponse::commonResponse(
            200,
            'Success',
            ['data' => 'Resident deleted']
        );
    }
}
