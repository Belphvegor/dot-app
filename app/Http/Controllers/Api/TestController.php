<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Helpers\ApiResponseFormat;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\City;

class TestController extends Controller
{
    public function __construct()
    {
        $this->format = new ApiResponseFormat();
        $this->province = new Provinsi();
        $this->city = new City();
        $this->fetch = new Http();
    }

    public function getProvinsi(Request $request)
    {
        try {
            if ($request->has('id')) {
                $result = $this->province->find($request->id);
            } else {
                $result = $this->province->get();
            }
            return response()->json($this->format->formatResponseWithPages("Success", $this->format->STAT_OK(), $result));
        } catch (\QueryException $th) {
            return response()->json($this->format->formatResponseWithPages("Error SQL", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), $this->format->STAT_INTERNAL_SERVER_ERROR());
        } catch (\Exception $th) {
            return response()->json($this->format->formatResponseWithPages("Internal Server Error", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), $this->format->STAT_INTERNAL_SERVER_ERROR());
        }
    }

    public function getCity(Request $request)
    {
        try {
            if ($request->has('id')) {
                $result = $this->city->findOrFail($request->id);
            } else {
                $result = $this->city->get();
            }
            return response()->json($this->format->formatResponseWithPages("Success", $this->format->STAT_OK(), $result));
        } catch (\QueryException $th) {
            return response()->json($this->format->formatResponseWithPages("Error SQL", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), $this->format->STAT_INTERNAL_SERVER_ERROR());
        } catch (\Exception $th) {
            return response()->json($this->format->formatResponseWithPages("Internal Server Error", $this->format->STAT_INTERNAL_SERVER_ERROR(), $th->getMessage()), $this->format->STAT_INTERNAL_SERVER_ERROR());
        }
    }
}
