<?php

namespace App\Http\Controllers;

use App\Jobs\AwaitingApproval;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            "email" => ['required', 'string', 'email', 'unique:users'],
            "first_name" => ['required', 'min:2', 'max:255'],
            "last_name" => ['required', 'min:2', 'max:255'],
            "password" => ['required', 'min:8', 'max:255']
        ]);

        return $this->requestAprroval($request, "CREATE");


    }

    /**
     * @param Request $request
     * @return string[]
     */
    public function requestAprroval(Request $request, $requestType): array
    {
        AwaitingApproval::dispatch($request->user()->id, $request->all(), $requestType);

        return [
            "status" => "success",
            "message" => "Request Received: Awaiting Approval "
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return string[]
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            "email" => ['sometimes', 'string', 'email', 'unique:users'],
            "first_name" => ['sometimes', 'min:2', 'max:255'],
            "last_name" => ['sometimes', 'min:2', 'max:255'],
        ]);
        $request->offsetSet('candidate', $user->id);

        return $this->requestAprroval($request, "UPDATE");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $user
     * @return string[]
     */
    public function destroy(Request $request, User $user)
    {
        $request->offsetSet('candidate', $user->id);

        return $this->requestAprroval($request, "DELETE");
    }


}
