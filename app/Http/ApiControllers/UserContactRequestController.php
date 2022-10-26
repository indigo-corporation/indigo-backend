<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\PaginatedCollection;
use App\Http\Resources\UserContactRequestFromResource;
use App\Http\Resources\UserContactRequestToResource;
use App\Http\Resources\UserShortResource;
use App\Models\UserContact;
use App\Models\UserContactRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserContactRequestController extends Controller
{
    public function incomes()
    {
        $users = Auth::user()->contact_requests_incomes_pivot();

        return response()->success_paginated(
            new PaginatedCollection($users->paginate(20), UserContactRequestFromResource::class)
        );
    }

    public function outcomes()
    {
        $users = Auth::user()->contact_requests_outcomes_pivot();

        return response()->success_paginated(
            new PaginatedCollection($users->paginate(20), UserContactRequestToResource::class)
        );
    }

    public function create(UserRequest $request)
    {
        $user = Auth::user();

        if (
            $request->user_id == $user->id ||
            UserContactRequest::query()
                ->where('user_id', $request->user_id)
                ->where('contact_id', $user->id)
                ->exists()
        ) {
            return response()->error();
        }

        UserContactRequest::firstOrCreate([
            'user_id' => $user->id,
            'contact_id' => $request->user_id,
        ]);

        return response()->success(null, 201);
    }

    public function destroy($request_id)
    {
        $request = UserContactRequest::findOrFail($request_id);

        if ($request->user_id != Auth::id() && $request->contact_id != Auth::id()) {
            return response()->error([
                'code' => '403',
                'message' => 'Forbidden'
            ]);
        }

        $request->delete();

        return response()->success(null, 204);
    }

    public function accept($request_id)
    {
        $request = UserContactRequest::findOrFail($request_id);

        if ($request->user_id != Auth::id() && $request->contact_id != Auth::id()) {
            return response()->error([
                'code' => '403',
                'message' => 'Forbidden'
            ]);
        }

        try {
            DB::beginTransaction();

            $request->delete();

            UserContact::create([
                'user_id' => Auth::id(),
                'contact_id' => $request->user_id
            ]);

            UserContact::create([
                'contact_id' => Auth::id(),
                'user_id' => $request->user_id
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return $e;
        }

        return response()->success(null, 201);
    }
}
