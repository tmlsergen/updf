<?php

namespace App\Models;

use App\Helpers\ProcessCloud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Cloud extends Model
{
    protected $fillable = ["title", "user_id", "code", "session", "html", "pdf", "svg", "content", "options", "png", "jpg", "spng", "sjpg"];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function startProcess(Request $request)
    {

        // $data['session'] = Session::getId();
        $data['user_id'] = $request->user()->id ?? null;
        $data['options'] = json_encode($request->options) ?? null;
        $data['code'] = self::randomCode();
        $data['content'] = '<!doctype html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <style>

        @import url(https://fonts.googleapis.com/css2?family=Indie+Flower);
        @import url(https://fonts.googleapis.com/css2?family=Anton);
        @import url(https://fonts.googleapis.com/css2?family=Bangers);
        @import url(https://fonts.googleapis.com/css2?family=Dancing+Script);
        @import url(https://fonts.googleapis.com/css2?family=Lobster);
        @import url(https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Inconsolata&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Amatic+SC:wght@700&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Kalam:wght@300;400;700&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Monoton&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Shadows+Into+Light+Two&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Bungee&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Bungee+Inline&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Shojumaru&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Bungee+Shade&display=swap);
        @import url(https://fonts.googleapis.com/css2?family=Limelight&display=swap);
        @import url(https://db.onlinewebfonts.com/c/e91f4ee1fa3e86ac219fbfa0d175e774?family=Special+Elite);
        @import url(https://db.onlinewebfonts.com/c/36377b090a4774f2023149c49d851d50?family=Duality);
        @import url(https://db.onlinewebfonts.com/c/209cc623f7e199b28118233d2d3be7bb?family=STHeiti+TC);

        @charset "UTF-8";

        @page {
            margin: 0;
            size : A3 landscape;
        }

        body{
            background-color:none;
        }

        #cloud{
            margin:0 auto;
            position:relative;
            width:50%;
        }'
        . str_replace('transform:', '-webkit-transform:', $request->head ?? '') .'

        </style>
        </head>
        <body><div id="cloud">'
            . str_replace('transform:', '-webkit-transform:', $request->html ?? '')
            . '</div></body></html>';

        if ($cloud = self::create($data)){
             $proc = new ProcessCloud($cloud);
             return $proc->makeProcess();
        }
        return [
            'code' => null,
            'sjpg' => null
        ];
    }

    public function getUrl($type)
    {

        $url = Storage::cloud()->temporaryUrl(
            $this->attributes[$type], Carbon::now()->addMinutes(5)
        );

        return $url;
    }

    public static function randomCode($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }
}
