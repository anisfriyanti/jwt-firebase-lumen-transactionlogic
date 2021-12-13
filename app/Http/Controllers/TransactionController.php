<?php

namespace App\Http\Controllers;
use DB;
use App\Models\Transactions;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;
class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function test(Request $request){
        echo 'test token';
    }

    public function index(Request $request){
        $controller = new AuthController;
        $key = explode(' ',$request->header('Authorization'));
        $user_id = $controller->decodejwt($key);

      

        $start = $request->get('start');
        $end = $request->get('end');
        $page = $request->get('page');
        $perPage = $request->get('limit');
        // $getPost = Transactions::OrderBy("id", "DESC")->paginate(10);
        if (empty($start) && empty($end))
        {
            $end = date("Y-m-d H:i:s");
            $start = date('Y-m-d H:i:s', strtotime('-1 month'));

        }
        else if (empty($start))
        {

            $date1 = str_replace('-', '/', $end);
            $start = date('Y-m-d H:i:s', strtotime($date1 . "-1 months"));

        }
        else if (empty($end))
        {
            $date1 = str_replace('-', '/', $start);
            $end = date('Y-m-d H:i:s', strtotime($date1 . "+1 months"));

        }
        else
        {
            $start = $start;
            $end = $end;

        }

         if (empty($page))
        {
            $page = 1;
        }
        if (empty($perPage))
        {
            $perPage = 10;
        }
        

       

            $getPost=DB::table(DB::raw('Transactions , Outlets , Merchants '))
            ->select(DB::raw('ANY_VALUE(Merchants.merchant_name) as merchant_name,
            SUM(Transactions.bill_total) AS omzet, 
            ANY_VALUE(Transactions.created_at) as transaction_date'))
            ->where( 'Transactions.outlet_id',DB::raw('Outlets.id'))
            ->where( 'Outlets.merchant_id',DB::raw('Merchants.id'))
            ->where( 'Merchants.user_id',DB::raw($user_id))
            ->whereRaw("DATE(Transactions.created_at) between DATE('$start') and DATE('$end')")
            //->whereBetween(DB::raw('Transactions.created_at'),[$start,$end])
            ->groupBy(DB::raw('DATE(Transactions.created_at)'))->paginate($perPage);



        $out = [
            "message" => "List Transactions",
            "results" => $getPost
        ];

        return response()->json($out, 200);


    }


    public function indexoutlet(Request $request){
        $controller = new AuthController;
        $key = explode(' ',$request->header('Authorization'));
        $user_id = $controller->decodejwt($key);
        $start = $request->get('start');
        $end = $request->get('end');
        $page = $request->get('page');
        $perPage = $request->get('limit');
        // $getPost = Transactions::OrderBy("id", "DESC")->paginate(10);
        if (empty($start) && empty($end))
        {
            $end = date("Y-m-d H:i:s");
            $start = date('Y-m-d H:i:s', strtotime('-1 month'));

        }
        else if (empty($start))
        {

            $date1 = str_replace('-', '/', $end);
            $start = date('Y-m-d H:i:s', strtotime($date1 . "-1 months"));

        }
        else if (empty($end))
        {
            $date1 = str_replace('-', '/', $start);
            $end = date('Y-m-d H:i:s', strtotime($date1 . "+1 months"));

        }
        else
        {
            $start = $start;
            $end = $end;

        }

         if (empty($page))
        {
            $page = 1;
        }
        if (empty($perPage))
        {
            $perPage = 10;
        }
        

                    $getPost=DB::table(DB::raw('Transactions , Outlets , Merchants '))
            ->select(DB::raw('ANY_VALUE(Merchants.merchant_name) as merchant_name,
            SUM(Transactions.bill_total) AS omzet, 
            ANY_VALUE(Transactions.created_at) as transaction_date,
            ANY_VALUE(Outlets.outlet_name) as outlet_name'))
            ->where( 'Transactions.outlet_id',DB::raw('Outlets.id'))
            ->where( 'Outlets.merchant_id',DB::raw('Merchants.id'))
            ->where( 'Merchants.user_id',DB::raw($user_id))
            ->whereRaw("DATE(Transactions.created_at) between DATE('$start') and DATE('$end')")
            //->whereBetween(DB::raw('Transactions.created_at'),[$start,$end])
            ->groupBy(DB::raw('DATE(Transactions.created_at)'))->paginate($perPage);


        $out = [
            "message" => "List Transactions",
            "results" => $getPost
        ];

        return response()->json($out, 200);



        

    }
     


    //
}