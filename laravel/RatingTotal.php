<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Rating as RatingResource;

class RatingTotal extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, User $user)
    {
        $limit = $request->input('limit');
        $offset = $request->input('offset');

        $data = Payment::select('users.id as user_id', DB::raw('sum(amount) as totalAmount'))
            ->join('users', 'users.id','=','payments.user_id')
            ->groupBy('users.id')
            ->with('user')
            ->orderByRaw('sum(amount) desc, users.last_payment_date asc')
            ->limit($limit)->offset($offset)
            ->get();

        // показать текущего юзера
        // сумма платежей
        $user_total_amount = Payment::where('user_id', $user->id)
            ->sum('amount');

        $user_position = DB::select('select count(distinct user_id) from (
            select sum(amount) as total_amount,user_id from payments
            group by user_id
            having sum(amount) > ?
        ) as p', [$user_total_amount])[0]->count;

        $user_position2 = DB::select('select count(distinct user_id) from (
            select sum(amount) as total_amount,users.id as user_id from payments
            join users on users.id = payments.user_id
            group by users.id
            having last_payment_date < ? and sum(amount) = ?
        ) as p', [$user->last_payment_date, $user_total_amount])[0]->count;

        $user_position = $user_position + $user_position2 + 1;

        $data = RatingResource::collection($data);
        $ratingResource = new RatingResource([
            "user_id" => $user->id,
            "totalamount" => $user_total_amount,
            "user" => $user,
        ]);
        return response()->json([
            'success' => true,
            'data' => [
                'data' => $data,
                'userPosition' => $user_position,
                'limit' => (int)$limit,
                'offset' => (int)$offset,
                'userItem' => $ratingResource,
            ],
        ]);
    }
}
