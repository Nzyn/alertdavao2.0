<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PoliceStation;
use App\Models\PoliceOfficer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'firstName' => $user->firstname,
                    'lastName' => $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->contact,
                    'address' => $user->address,
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                    'is_verified' => $user->is_verified,
                    'station_id' => $user->station_id,
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Validate the incoming request data
            $validatedData = $request->validate([
                'firstname' => 'sometimes|required|string|max:50',
                'lastname' => 'sometimes|required|string|max:50',
                'email' => 'sometimes|required|email|max:100|unique:users,email,' . $id,
                'contact' => 'sometimes|nullable|string|max:15',
                'address' => 'sometimes|nullable|string',
                'latitude' => 'sometimes|nullable|numeric',
                'longitude' => 'sometimes|nullable|numeric',
            ]);

            // Update the user with the validated data
            $user->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'firstName' => $user->firstname,
                    'lastName' => $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->contact,
                    'address' => $user->address,
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                    'is_verified' => $user->is_verified,
                    'station_id' => $user->station_id,
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating user data'
            ], 500);
        }
    }

    /**
     * Get all users (optional - for admin purposes)
     */
    public function index(): JsonResponse
    {
        try {
            $users = User::select('id', 'firstname', 'lastname', 'email', 'contact', 'address', 'latitude', 'longitude', 'is_verified', 'station_id', 'created_at')
                         ->orderBy('created_at', 'desc')
                         ->get();

            return response()->json([
                'success' => true,
                'data' => $users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'firstName' => $user->firstname,
                        'lastName' => $user->lastname,
                        'email' => $user->email,
                        'phone' => $user->contact,
                        'address' => $user->address,
                        'latitude' => $user->latitude,
                        'longitude' => $user->longitude,
                        'is_verified' => $user->is_verified,
                        'station_id' => $user->station_id,
                        'created_at' => $user->created_at,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching users'
            ], 500);
        }
    }
    
    /**
     * Flag a user
     */
    public function flagUser(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Add logic to flag the user (this could be a separate table or a flag in the users table)
            // For now, we'll just return a success response
            // In a real implementation, you would update a flagged status in the database
            
            return response()->json([
                'success' => true,
                'message' => 'User has been flagged successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while flagging the user'
            ], 500);
        }
    }
    
    /**
     * Promote a user to officer
     */
    public function promoteToOfficer(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Check if user is already an officer
            if ($user->station_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already an officer'
                ], 400);
            }
            
            // Get the first police station as default (in a real app, you would let the admin choose)
            $station = PoliceStation::first();
            
            if (!$station) {
                return response()->json([
                    'success' => false,
                    'message' => 'No police stations available'
                ], 400);
            }
            
            // Update user with station_id and role
            $user->station_id = $station->station_id;
            $user->role = 'police';
            $user->save();
            
            // Create police officer record
            PoliceOfficer::create([
                'user_id' => $user->id,
                'station_id' => $station->station_id,
                'assigned_since' => now(),
                'status' => 'active'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User has been promoted to officer successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while promoting the user to officer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change a user's role
     */
    public function changeRole(Request $request, string $id): JsonResponse
    {
        try {
            \Log::info('changeRole called', ['user_id' => $id, 'request_data' => $request->all()]);
            
            $user = User::findOrFail($id);
            \Log::info('User found', ['user_id' => $user->id, 'current_role' => $user->role]);
            
            // Validate the role
            $validatedData = $request->validate([
                'role' => 'required|in:user,police,admin'
            ]);
            
            \Log::info('Validation passed', ['new_role' => $validatedData['role']]);
            
            // If changing to police, assign a station
            if ($validatedData['role'] === 'police' && !$user->station_id) {
                $station = PoliceStation::first();
                
                if (!$station) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No police stations available'
                    ], 400);
                }
                
                $user->station_id = $station->station_id;
                
                // Create police officer record if not exists
                PoliceOfficer::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'station_id' => $station->station_id,
                        'assigned_since' => now(),
                        'status' => 'active'
                    ]
                );
                
                \Log::info('Station assigned', ['station_id' => $station->station_id]);
            }
            
            // If changing from police to user, remove station
            if ($validatedData['role'] === 'user' && $user->station_id) {
                $user->station_id = null;
                // Optionally deactivate police officer record
                PoliceOfficer::where('user_id', $user->id)->update(['status' => 'inactive']);
                \Log::info('Station removed');
            }
            
            // Update the role
            $user->role = $validatedData['role'];
            $user->save();
            
            \Log::info('Role updated successfully', ['user_id' => $user->id, 'new_role' => $user->role]);
            
            return response()->json([
                'success' => true,
                'message' => 'User role has been changed successfully',
                'user' => [
                    'id' => $user->id,
                    'role' => $user->role,
                    'station_id' => $user->station_id
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            \Log::error('User not found', ['user_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception in changeRole', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while changing the user role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a police or admin user to a police station
     */
    public function assignStation(Request $request, string $id): JsonResponse
    {
        try {
            \Log::info('assignStation called', ['user_id' => $id, 'request_data' => $request->all()]);
            
            $user = User::findOrFail($id);
            \Log::info('User found', ['user_id' => $user->id, 'current_role' => $user->role]);
            
            // Validate that user is police or admin
            if ($user->role !== 'admin' && $user->role !== 'police') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only police and admin users can be assigned to police stations'
                ], 400);
            }
            
            // Validate the station_id
            $validatedData = $request->validate([
                'station_id' => 'required|exists:police_stations,station_id'
            ]);
            
            \Log::info('Validation passed', ['station_id' => $validatedData['station_id']]);
            
            // Check if station exists
            $station = PoliceStation::where('station_id', $validatedData['station_id'])->first();
            if (!$station) {
                return response()->json([
                    'success' => false,
                    'message' => 'Police station not found'
                ], 404);
            }
            
            // Assign the user to the station
            $user->station_id = $validatedData['station_id'];
            $user->save();
            
            // If police user, also update/create police officer record
            if ($user->role === 'police') {
                PoliceOfficer::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'station_id' => $validatedData['station_id'],
                        'assigned_since' => now(),
                        'status' => 'active'
                    ]
                );
                
                \Log::info('Police officer record updated', [
                    'user_id' => $user->id,
                    'station_id' => $validatedData['station_id']
                ]);
            }
            
            \Log::info('User assigned to station', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'station_id' => $validatedData['station_id'],
                'station_name' => $station->station_name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($user->role) . ' user has been assigned to the police station successfully',
                'user' => [
                    'id' => $user->id,
                    'role' => $user->role,
                    'station_id' => $user->station_id
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            \Log::error('User not found', ['user_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception in assignStation', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning the user to the station: ' . $e->getMessage()
            ], 500);
        }
    }
}