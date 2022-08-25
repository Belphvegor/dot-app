<?php
namespace App\Helpers;

class ApiResponseFormat {

    public function STAT_OK() {
        return 200;
    }

    public function STAT_REQUIRED() {
        return 201;
    }

    public function STAT_BAD_REQUEST() {
        return 400;
    }

    public function STAT_UNAUTHORIZED() {
        return 401;
    }

    public function STAT_NOT_FOUND() {
        return 404;
    }

    public function STAT_REQUEST_TIMEOUT() {
        return 408;
    }

    public function STAT_REQUEST_CONFLICT() {
        return 409;
    }

    public function STAT_INTERNAL_SERVER_ERROR() {
        return 500;
    }

    public function STAT_SERVICE_UNAVAILABLE() {
        return 503;
    }

    public function STAT_UNPROCESSABLE_ENTITY() {
        return 422;
    }

    public function formatResponseWithPages($status, $code = 200, $data = null, $page = null) {

        // var for box response
        $response = "";

        // format diagnostic
        $dgn = [
            'code'  => $code,
            'status' => $status,
        ];

        // collecting and format result
        if ($code == 200) {

            // success
            if ($page == null) {

                // if pagination null / not set
                if ($data == null) {
                    $response = [
                        'diagnostic' => $dgn,
                    ];
                } else {
                    $response = [
                        'diagnostic' => $dgn,
                        'response' => $data
                    ];
                }
            } else {

                // if pagination set / not null
                $response = [
                    'diagnostic' => $dgn,
                    'pagination' => $page,
                    'response' => $data
                ];
            }
        } else if ($code == 201) {

            // for code error = 201, its mean for reqired data
            // using for validation data
            // inside response is like error message for required data
            if ($data == null) {
                $response = [
                    'diagnostic' => $dgn,
                ];
            } else {
                $response = [
                    'diagnostic' => $dgn,
                    'response' => $data
                ];
            }
        } else {
            $dgn['errors'] = $data;
            // for remain list error code
            $response = [
                'diagnostic' => $dgn
            ];
        }

        // return data for convert to json response
        return $response;
    }
}
