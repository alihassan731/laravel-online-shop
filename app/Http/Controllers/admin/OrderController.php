<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function index(Request $request){
        
        $data = [];
        $orders = Order::latest('orders.created_at')->select('orders.*', 'users.name','users.email');
        $orders = $orders->leftJoin('users','users.id','orders.user_id');
        if($request->get('keyword') != ''){
            $orders = $orders->where('users.name','like','%'.$request->keyword.'%');
            $orders = $orders->orwhere('users.email','like','%'.$request->keyword.'%');
            $orders = $orders->orwhere('orders.id','like','%'.$request->keyword.'%');
        }

        $orders = $orders->paginate(5);
        $data['orders'] = $orders;
        return view('admin.orders.list', $data);
    }

    public function detail(Request $request, $orderId) {
        $data = [];
        $order = Order::select('orders.*','countries.name as countryName')
                    ->where('orders.id',$orderId)
                    ->leftJoin('countries','countries.id','orders.country_id')
                    ->first();
        $data['order'] = $order;

        $orderItems = OrderItem::where('order_id',$orderId)->get();
        $data['orderItems'] = $orderItems;
        return view('admin.orders.detail', $data);
    }

    public function changeOrderStatus(Request $request, $orderId) {
        
        $order = Order::find( $orderId);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        $message = "Order status updated successfully";
        
        session()->flash('success', $message);
        return response()->json([
            'status' => true, 
            'message'=> $message
        ]);
    }

    public function sendInvoiceEmail(Request $request,  $orderId){
        orderEmail($orderId, $request->userType);

        $message = "Order email send successfully";
        
        session()->flash('success', $message);
        return response()->json([
            'status' => true, 
            'message'=> $message
        ]);
    }
}
