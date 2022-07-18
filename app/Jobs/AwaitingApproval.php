<?php

namespace App\Jobs;

use App\Models\Maker;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AwaitingApproval implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $requestType;
    private $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId,$data, $requestType)
    {
        //
        $this->data = $data;
        $this->userId = $userId;
        $this->requestType = $requestType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //store Request
        $requstId = DB::table('request')->insertGetId([
            "type" => strtoupper($this->requestType),
            "data" => json_encode($this->data),
        ]);

        Maker::create([
            "user_id" => $this->userId,
            "request_id" => $requstId,
            "ref" => Str::orderedUuid(),
        ]);

        //Notify other admins that a request has been created;
        $users = User::query()->where('id', '!=', $this->userId)->get();
        Notification::send($users, new \App\Notifications\AwaitingApproval($this->requestType,$this->data));
    }
}
