<?php
 
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\Country;
use App\Mail\OrderEmail;
use App\Models\Page;
use Illuminate\Support\Facades\Mail;

function getCategories(){
    return Category::orderBy('name','ASC')
    ->with('sub_category')   //sub_category is the function name that created  in category model.to access it use with() method and pass the function name as parameter.
    ->orderBy('id','DESC')
    ->where('status',1)
    ->where('showHome','Yes')
    ->get();
}


function getProductImage($productId){
    return ProductImage::where( 'product_id' , $productId )->first();
}

// function orderEmail($orderId){
//     $order = Order::where('id', $orderId)->with('items')->first();

//     $mailData = [
//         'subject' => 'Thanks for your Order',
//         'order' => $order,
//     ];

//     Mail::to($order->email)->send(new OrderEmail($mailData)); // $mailData will receive in the __construct function that have OrderEmail.php 
// }
// after modify for implement admin orders dashboard 
function orderEmail($orderId, $userType="customer"){
    $order = Order::where('id', $orderId)->with('items')->first();

    if ($userType == 'customer'){
        $subject = "Thanks for your Order";
        $email = $order->email;
    } else{
        $subject = "You have received an order";
        $email = env('ADMIN_EMAIL');
    }

    $mailData = [
        'subject' => $subject,
        'order' => $order,
        'userType' => $userType,
    ];

    Mail::to($email)->send(new OrderEmail($mailData)); // $mailData will receive in the __construct function that have OrderEmail.php 
}


function getCountryInfo($id){
    return Country::where('id',$id)->first();
}

function staticPage(){
    $pages = Page::orderBy('name','ASC')->get();
    return $pages;
}
?>