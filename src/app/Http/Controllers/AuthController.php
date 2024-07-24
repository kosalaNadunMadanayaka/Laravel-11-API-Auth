<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="Laravel 11 Authentication",
 *     version="0.0.1",
 *     description="API Documentation for Laravel Auth",
 *     @OA\Contact(
 *         email="nadun@thesanmark.com"
 *     )
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     operationId="register",
     *     tags={"Authentication"},
     *     summary="Register user",
     *     description="Returns access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User Created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="token", type="string")
     *         )
     *      ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="json")
     *         )
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="json")
     *         )
     *      )
     * )
     */
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'validation error',
                        'errors' => $validateUser->errors(),
                    ],
                    400,
                );
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'User created successfully',
                    'token' => $user->createToken('API TOKEN', ['*'], now()->addweek())->plainTextToken,
                ],
                201,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                    'error' => $th,
                ],
                500,
            );
        }
    }

     /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Returns access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Logged",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="token", type="string")
     *         )
     *      ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="json")
     *         )
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="json")
     *         )
     *      )
     * )
     */
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'validation error',
                        'errors' => $validateUser->errors(),
                    ],
                    400,
                );
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Email & password does not match with our record',
                    ],
                    401,
                );
            }

            $user = User::where('email', $request->email)->first();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'User logged in successfully',
                    'token' => $user->createToken('API TOKEN', ['*'], now()->addweek())->plainTextToken,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                    'error' => $th,
                ],
                500,
            );
        }
    }

     /**
     * @OA\Get(
     *     path="/api/profile",
     *     operationId="profile",
     *     tags={"Account"},
     *     summary="user profile",
     *     description="get user profile details",
     *     @OA\Response(
     *         response=200,
     *         description="User Logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="user data"),
     *             @OA\Property(property="user id", type="integer"),
     *         )
     *      )
     * )
     */

    public function profile()
    {
        $userData = auth()->user();
        return response()->json(
            [
                'status' => true,
                'message' => 'Profile information',
                'data' => $userData,
                'id' => auth()->user()->id,
            ],
            200,
        );
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     summary="logout user",
     *     description="User logged out",
     *     @OA\Response(
     *         response=200,
     *         description="User Logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolen"),
     *             @OA\Property(property="message", type="string"),
     *         )
     *      )
     * )
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(
            [
                'status' => true,
                'message' => 'User logged out',
                'data' => [],
            ],
            200,
        );
    }
}
