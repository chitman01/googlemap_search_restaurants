<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use File;

class IndexController extends Controller
{
    public $key = '';
    public $maxwidth = 100;

    public function index()
    {
        $return['key'] = $this->key;

        return view('page.index',$return);
    }

    // Api สำหรับเรียกรายการร้านอาหาร
    public function getLocationList(Request $request){
        $textSearch = $request->textSearch;
        $type = $request->type;
        $getCache = Cache::get($textSearch);

        if($getCache){
            $return['status'] = 1;
            $return['detail'] = json_decode($getCache);
            $return['cache'] = 1;
        }else{
            $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query='.$textSearch.'&type='.$type.'&key='.$this->key;

            $api_get = json_decode($this->api_get($url));
            if ($api_get->status) {
                $dataBefore = json_decode($api_get->data)->results;
                $dataAfterEncode = json_encode($dataBefore);

                Cache::put($textSearch, $dataAfterEncode, now()->addMinutes(60));
                $return['status'] = 1;
                $return['detail'] = $dataBefore;
            } else {
                $return['status'] = 0;
                $return['detail'] = $api_get;
            }
            $return['cache'] = 0;

        }
        return json_encode($return);
    }

    // แปลงลิ้งค์รูปที่ได้จาก google map api เป็น base64
    function link_to_image($image_link)
    {
        // $filetype = substr($image_link, -3);
        $filetype = '.jpeg';
        $b64image = 'data:image/' . $filetype . ';base64,' . base64_encode(file_get_contents($image_link));

        return  $b64image;
    }

    // Api เรียกรายละเอียดของสถานที่นั้นๆ
    public function getDetailData(Request $request){
        $textSearch = $request->textSearch;
        $type = $request->type;
        $place_id = $request->place_id;

        // Cache::forget($place_id);
        $getCache = Cache::get($place_id);
        if($getCache){
            $result = json_decode($getCache);

            $result->photosImage = $this->getImageAndCacheFile($result->photos,$result->place_id);

            $return['status'] = 1;
            $return['detail'] = $result;
            $return['cache'] = 1;
        }else{
            $url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id='.$place_id.'&key='.$this->key;
            $api_get = json_decode($this->api_get_image($url));
    
            if ($api_get->status) {
                $dataBefore = json_decode($api_get->data)->result;
                $dataBefore->photosImage = $this->getImageAndCacheFile($dataBefore->photos,$dataBefore->place_id);

                $dataAfterEncode = json_encode($dataBefore);

                Cache::put($place_id, $dataAfterEncode, now()->addMinutes(60));

                $return['status'] = 1;
                $return['detail'] = $dataBefore;
            } else {
                $return['status'] = 0;
                $return['detail'] = $api_get;
            }
            $return['cache'] = 0;
        }
        return json_encode($return);
    }

    // แปลงรูปเป็นไฟล์ text เก็บไว้ใน /uploads/json/ ใช้ชื่อตาม place_id สำหรับเก็บแคช
    public function getImageAndCacheFile($photoAr,$place_id){
        $fileType = '.txt';
        try {
            $checkfile = File::get(public_path('/uploads/json/'.$place_id . $fileType));
        } catch (\Throwable $th) {
            $checkfile = null;
        }

        if($checkfile){
            // dd('find');
            $array_file = json_decode($checkfile);
        }else{
            // dd('else');
            $image = [];
            foreach ($photoAr as $key => $value) {
                $url = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&key='.$this->key.'&photo_reference='.$value->photo_reference;
    
                $base64Img = $this->link_to_image($url);
    
                $image[] = $base64Img;
            }
    
            $array_file = (object) $image;
    
            $fileName = $place_id . $fileType;
            $fileStorePath = public_path('/uploads/json/'.$fileName);
    
            File::put($fileStorePath, json_encode($array_file));
        }
        
        
        return $array_file;
    }
        
}
