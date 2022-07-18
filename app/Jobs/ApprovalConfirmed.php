<?php

namespace App\Jobs;

use App\Models\Maker;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Notifications\UserUpdatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApprovalConfirmed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $ref;
    private int $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ref, $userId)
    {
        //
        $this->ref = $ref;

        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $maker = Maker::query()->where('ref', $this->ref)->first();
        if (isset($maker)) {
            $requestQuery = DB::table('request')->where('id', $maker->request_id);

            $request = $requestQuery->first();
            if (!empty($request)) {
                $data = json_decode($request->data);
                //handle based on type
                if ($request->type === "CREATE") {

                    //Register User and store;
                    $user = User::create([
                        "first_name" => $data->first_name,
                        "last_name" => $data->last_name,
                        "email" => $data->email,
                        "password" => Hash::make($data->password),
                    ]);
                    $token = $user->createToken("Access Token")->plainTextToken;
                    //trigger notification
                    $user->notify(new UserCreatedNotification($token));
                    //update Request status
                    $requestQuery->update([
                        'status' => 1
                    ]);

                    //create checker
                    $maker->checker()->create([
                        "ref" => Str::orderedUuid(),
                        "user_id" => $this->userId,
                    ]);

                }
                if ($request->type === "UPDATE") {

                    //Fetch User and Update;
                    $user = User::find($data->candidate);
                    $userDetails = (array)$data;
                    unset($userDetails['candidate']);

                    $user->update($userDetails);
                    //trigger notification
                    $user->notify(new UserUpdatedNotification());


                    //update Request status
                    $requestQuery->update([
                        'status' => 1
                    ]);

                    //create checker
                    $maker->checker()->create([
                        "ref" => Str::orderedUuid(),
                        "user_id" => $this->userId,
                    ]);

                }
                if ($request->type === "DELETE") {


                    //Fetch User and DELETE;
                    User::destroy($data->candidate);

                    //update Request status
                    $requestQuery->update([
                        'status' => 1
                    ]);

                    //create checker
                    $maker->checker()->create([
                        "ref" => Str::orderedUuid(),
                        "user_id" => $this->userId,
                    ]);

                }

            }
        }
    }
}
