<?php


namespace App\Http\Controllers\Customer;

use App\Transactions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;

class TransactionController extends Controller
{
    /**
     * Get customer transaction data.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerTranscationData(Request $request)
    {
        try {
            $requestData = $request->all();
            $searchFilters = array_key_exists('filters', $requestData)? $requestData['filters']:[];
            $orderBy = array_key_exists('orderBy', $requestData)? $requestData['orderBy']:[];

            $loggedInCustomer = JWTAuth::parseToken()->authenticate();
            if (!$loggedInCustomer) {
                return new JsonResponse([
                    'message' => 'invalid_token'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return new JsonResponse([
                'message' => 'Data processed successfully',
                'data' => Transactions::customerTransaction(
                    $loggedInCustomer->id, $searchFilters, $orderBy)
            ]);
        } catch (\Exception $e){
            return new JsonResponse([
                'message' => 'Something went wrong. Please try after sometime'
            ], 422);
        }
    }

    /**
     * Get individual transaction details
     * @param Request $request
     * @return \Exception|JsonResponse
     */
    public function getCustomerTranscationDetails(Request $request)
    {
        try {
            $requestData = $request->all();

            if(!$request->has('transaction_id')){
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Transaction id is required'
                ], 422);
            }

            $loggedInCustomer = JWTAuth::parseToken()->authenticate();
            if (!$loggedInCustomer) {
                return new JsonResponse([
                    'message' => 'invalid_token'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return new JsonResponse([
                'message' => 'Data processed successfully',
                'data' => Transactions::getTransactionDetails(
                    $loggedInCustomer->id, $requestData['transaction_id'])
            ]);
        } catch (\Exception $e){
            return new JsonResponse([
                'message' => 'Something went wrong. Please try after sometime'
            ], 422);
        }
    }
}