<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Models\ProductCategory;

use App\Models\Store;
use App\Repository\CityRepository;
use App\Repository\ProductRepository;
use App\Repository\StoreBusinessHoursRepository;
use App\Repository\StoreProductAttrRepository;
use App\Repository\StoreRepository;
use function foo\func;
use http\Env;
use Illuminate\Http\Request;



class   ProductController extends BaseController
{

    private $_model;
    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct();
        $this->_model = $productRepository;
    }

    //  产品分类
    public function productCategory(Request $request)
    {

        $response['code'] = 10000;
        $data = ProductCategory::orderby('sort_order', 'asc')->get()->all();

        if (!empty($data)) {

            //无限分类
            function getTree($data, $pId,$leav)
            {
                $tree = [];
                foreach ($data as $k => $item) {
                    if ($item['parent_id'] == $pId && $leav<=3) {
                        $item['items'] = getTree($data, $item['id'],$leav+1);
                        $item['icon'] = trim(env('HTTP_ADMIN') . $item['icon']);
                        $tree[] = $item;
                    }
                }
                return $tree;
            }

            $tree = getTree($data, 0,1);

            $response['data'] = $tree;
            $response['message'] = '请求成功!';

        } else {

            $response['code'] = 40001;
            $response['message'] = '数据为空!';
        }

        return response()->json($response);
    }


    //获取商品列表
    public function getList(Request $request){

        $response = array('code' => '10000','message'=>'获取成功','data' => []);
        $Product = new Product();

        $sort = $request->input('sort','');

        if($request->get('category_id')){
            //分类查询
            $Product = $Product->where('category_id',$request->input('category_id',0));
        }
        if($request->get('keyword')){
            //关键字查询
            $Product = $Product->where('keyword','like','%'.$request->input('keyword').'%');
        }

        //排序
        if(empty($sort)){

        }elseif($sort == 'sales'){
            //销量排序
            $Product = $Product->orderBy('sales_number','desc');
        }elseif ($sort == 'price_asc'){
            //价格递增排序
            $Product = $Product->orderBy('price','asc');
        }elseif ($sort == 'price_desc'){
            //价格递减排序
            $Product = $Product->orderBy('price','desc');
        }
        try{

            $list = $Product->select('id','title','image','price')->paginate(env('PAGINATE_NUM'));

            $list = $list->toArray();
            $product_list = $list['data'];
            foreach ($product_list as $key => $item){
                $images = explode(',',$item['image']);

                $product_list[$key]['image'] = env('HTTP_ADMIN') .$images[0];
            }

            $data = [
                'data' => $product_list,
                'current_page' => $list['current_page'],
                'last_page' => $list['last_page']
            ];

            $response['data'] = $data;

        }catch (\Exception $exception){
            $response['code'] = '50000';
            $response['message'] = '系统错误';
        }


        return response()->json($response);

    }


    //获取商品详情
    public function details(Request $request,StoreRepository $storeRepository,StoreProductAttrRepository $productAttrRepository){

        $response = array('code' => '10000','message'=>'获取成功','data' => []);

        $product_id = $request->input('product_id',0);

        if(empty($product_id)){
            $response['code'] = '20001';
            $response['message'] = '缺少参数';
        }else {
            try{

                $product = $this->_model->details($product_id);

                $store = [];
                if($product->store){
                    $store=[
                        'store_id' => $product->store->id,
                        'icon' => env('HTTP_ADMIN_STORE') .$product->store->icon,
                        'name' => $product->store->name,
                        'address' => $product->store->address,
                        'is_enter' => $product->store->user_id? true:false
                    ];
                }

                $prodct_data = [
                    'id' => $product->id,
                    'title' => $product->title,
                    'image' => $product->image,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'details' => env('SHOW_HTML_URL') . '?type=product_content&product_id='.$product_id,
                    'store' => $store,

                ];

                //获取商品规格
                $prodct_data['spec'] = [
                    'logistics' => $storeRepository->getLogistics($product->store->id),
                    'attr_list' => $productAttrRepository->getProductAttrList($product_id)
                ];


                $response['data'][0] = $prodct_data;

            }catch (\Exception $exception){
                $response['code'] = '50000';
                $response['message'] = '系统错误';
            }
        }
        return response()->json($response);
    }


    public function getProductStoreList(Request $request,CityRepository $cityRepository,StoreBusinessHoursRepository $storeBusinessHoursRepository){

        $response = array('code' => '10000','message'=>'获取成功','data' => []);

        $product_id = $request->input('product_id',0);

        if(empty($product_id)){
            $response['code'] = '20001';
            $response['message'] = '缺少参数';
        }else {

            $product  = Product::find($product_id);

            if(empty($product_id)){
                $response['code'] = '20002';
                $response['message'] = '商品不存在';
            }else if(empty($product->store_id)) {
                $response['code'] = '20002';
                $response['message'] = '门店不存在';
            }else{

                $store = Store::find($product->store_id);

                if(empty($store)){
                    $response['code'] = '20002';
                    $response['message'] = '门店不存在';
                }else{

                    $address = $cityRepository->getCityNameById($store->province_id);
                    $address .= $cityRepository->getCityNameById($store->city_id);
                    $address .= $cityRepository->getCityNameById($store->district_id);
                    $address .= $store->address;


                    $data[] = [
                        'id' => $store->id,
                        'name' => $store->name,
                        'address' => $address,
                        'busine_hours' => $storeBusinessHoursRepository->getBusinessHoursShow($store->id),
                        'tel' => $store->tel,
                        'longitude' => floatval($store->longitude),
                        'latitude' => floatval($store->latitude)
                    ];

                    $response['data'] = $data;
                }
            }
        }

        return response()->json($response);

    }

}