<?php

namespace App\Http\Controllers;

use App\Jobs\ApprovalConfirmed;
use App\Models\Maker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginateTreshold = 15;
        $allowedTypes = ["CREATE","DELETE","UPDATE"];
        $allowedStatus = [0,1];
        $request->offsetSet('type', strtoupper($request->type));
        //validate
        $request->validate([
            "type" => ['sometimes',Rule::in($allowedTypes)],
            "status" => ['sometimes',Rule::in($allowedStatus)]
        ]);

        if (empty($request->type) && is_null($request->status)){

            return DB::table('request')->leftJoin('makers', 'request.id', '=', 'makers.request_id')->select(['type','status','data','ref','makers.created_at','user_id'])->simplePaginate($paginateTreshold);
        }
        if (!empty($request->type) && isset($request->status)){

            return DB::table('request')->leftJoin('makers', 'request.id', '=', 'makers.request_id')->where([ ['request.type', $request->type],['request.status', $request->status ] ])->select(['type','status','data','ref','makers.created_at','user_id'])->simplePaginate($paginateTreshold);
        }
        if (isset($request->type) && !isset($request->status)){

            return DB::table('request')->leftJoin('makers', 'request.id', '=', 'makers.request_id')->where('request.type', $request->type)->select(['type','status','data','ref','makers.created_at','user_id'])->simplePaginate($paginateTreshold);
        }
        if (isset($request->status) && empty($request->type)){
            return DB::table('request')->leftJoin('makers', 'request.id', '=', 'makers.request_id')->where('request.status', $request->status)->select(['type','status','data','ref','makers.created_at','user_id'])->simplePaginate($paginateTreshold);
        }


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function approve($ref)
    {
        $userId = auth()->user()->id;

        $trnx = Maker::join('request','request.id', '=', 'makers.request_id')->where("ref", $ref)->first();
        if ($trnx){
            //if status is complete
            if ($trnx->status){
                return ["status" => "SUCCESS", "message" => "ALREADY APPROVED"];
            }

            //The initiator of the request cannot approve the request;
            if ($userId === $trnx->user_id){
                return ["message" => "You are not authorized to make this action!"];
            }

            //Dispatch to Job to handle approval
            ApprovalConfirmed::dispatch($trnx->ref,$userId);
            return ["status" => "SUCCESS", "message" => "APPROVED"];

        }
        return ["message" => "Invalid Ref Provided"];


    }
}
