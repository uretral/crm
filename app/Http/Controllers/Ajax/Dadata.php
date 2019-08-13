<?php


namespace App\Http\Controllers\Ajax;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Dadata extends Controller
{
    public function address(){

        $url = "https://dadata.ru/api/v2/suggest/address";

        $headers = array(
            "Content-type: application/json",
            "Accept: application/json",
            "Authorization: Token ccd4a5e4c462095321ff5b6068cea3a6e175e9ce"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU");

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{ "query": "' . $_GET['a'] . '", "count": 5 }');

        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            return  "Error: " . curl_error($ch);
        } else {
            curl_close($ch);
        }
        return $data;
    }

    public function osm(Request $request){
//        $flat = 55.77321337302965;
//        $flon = 37.50043094158173;

        $flat = $request->get('flat');
        $flon = $request->get('flon');

        $tlat = $request->get('lat');//55.875310835696816; //
        $tlon =  $request->get('lon'); //37.69958496093751

        $u = 'http://www.yournavigation.org/api/1.0/gosmore.php?flat='.$flat.'&flon='.$flon.'&tlat='.$tlat.'&tlon='.$tlon.'&format=geojson';

        return file_get_contents($u);
    }

}
