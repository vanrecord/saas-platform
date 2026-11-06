<?php 
// app/Supports/Trait/ApiResponse.php
namespace App\Supports\Trait;

use App\Enums\ApiStatusCode;

trait ApiResponseTrait
{    
    /**
     * Return a success response.
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data = null, $message = null, $statusCode = ApiStatusCode::SUCCESS)
    {
        $response = [
            'success' => true,
            'status'  => $statusCode,
            'message' => empty($message) ? __('locale.Successfully') : $message,
        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response);
    }
    /**
     * Return an error response.
     * @param mixed $errors
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($errors = null, $message = null, $statusCode = ApiStatusCode::ERROR)
    {
        $response = [
            'success' => false,
            'status'  => $statusCode,
            'message' => empty($message) ? __('locale.Error') : $message,
        ];
        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response);
    }
    /**
     * Return validation error response.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    public function validationErrorResponse($validator)
    {
        return $this->errorResponse($validator->errors(), __('locale.Validation failed'), ApiStatusCode::VALIDATION_ERROR);
    }
}
