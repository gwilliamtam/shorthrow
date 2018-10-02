<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Box extends Model
{
    protected $table = 'boxes';
    protected $configArray = array();
    protected $bannedUriNames = array('api', 'box', 'login', 'password', 'register');
//    protected $contentTypes = array('text', 'code', 'url', 'image');
    protected $contentTypes = [
        'text' => '<i class="fas fa-font"></i>',
        'code' => '<i class="fas fa-code"></i>',
        'url' => '<i class="fas fa-link"></i>',
        'image' => '<i class="far fa-image"></i>',
    ];

    public function __construct()
    {
        Box::whereNotNull('expires_at')->where('expires_at', '<', date('Y-m-d H:i:s'))->delete();
    }

    public function validate($fields, $isUpdate = false)
    {

        if(in_array($fields['uri'], $this->bannedUriNames)){
            flash('The word you entered is already in use')->error();
            return false;
        }

        // uri must be unique
        if($isUpdate){
            $boxCnt = 0;
        }else{
            $boxCnt = Box::where( 'uri', '=', $fields['uri'] )->count();
        }
        if($boxCnt == 0){

            $this->user = Auth::user()->id;
            $this->uri = $fields['uri'];

            $this->setContent($fields['content']);

            if(empty($fields['content_type'])) {
                $this->content_type = $this->contentTypes[0];
            }else{
                $this->content_type = $fields['content_type'];
            }

            if(empty($this->created_at)){
                $this->created_at = date("Y-m-d H:i:s");
            }

            $this->updated_at = date("Y-m-d H:i:s");

            $this->setExpirationDate($fields['expires_at']);

            $this->config = json_encode($this->configArray);

            return true;
        }

        return false;
    }

    public function setConfigValue($key, $value)
    {
        $this->configArray[$key] = $value;
    }

    public function setContent($content)
    {
        // remove here any nasty stuff from content
        $this->content = $content;
    }

    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    public function getExpireOptions()
    {
        $expireOptions = [
            "never" => "never",
            "five-minutes" => "in five minutes",
            "one-hour"=> "in one hour",
            "tomorrow"=> "tomorrow",
            "week"=> "in one week",
            "month"=> "in one month",
            "year"=> "in one year",
            "after-open"=> "after open",
        ];
        return $expireOptions;
    }

    public function remainingTime($type = null)
    {
        $response = "never";
        if(empty($this->expires_at)){
            if(!empty($this->expires_other)){
                $response = $this->expires_other;
            }
        }else{
            $seconds = strtotime($this->expires_at) - strtotime(date("Y-m-d H:i:s"));
            if($seconds<=0){
                $response = "now";
            }else{
                if($type === null || $type == "seconds"){
                    $response = $seconds . " seconds";
                }
                if($type === "minutes"){
                    $seconds = strtotime($this->expires_at) - now();
                    $time = intval($seconds/60);
                    $response = $time . " minutes";
                    if($time==0){
                        $response = $seconds . " seconds";
                    }
                }
                if($type === "auto"){
                    if($seconds > 60){
                        $time = $seconds/60;
                        $response = number_format($time,1) . " minutes";
                        if($time < 1){
                            $response = remainingTime("seconds");
                        }
                    }
                    if($seconds > 3600){
                        $time = $seconds/3600;
                        $response = number_format($time,1) . " hours";
                        if($time < 1){
                            $response = remainingTime("minutes");
                        }
                    }
                    if($seconds > 86400){
                        $time = $seconds/86400;
                        $response = number_format($time) . " days";
                        if($time<=1){
                            $response = remainingTime("hours");
                        }
                    }
                }
            }

        }
        return $response;
    }

    public function setExpirationDate($expType)
    {
        $this->expires_at = null;
        $this->expires_other = null;

        if(!empty($expType)){
            if($expType == "after-open"){
                $this->expires_other = "after-open";
            }else{
                $expireDate = null;
                // check if $expType is a datetime string
                if (date('Y-m-d H:i:s', strtotime($expType)) == $expType) {
                    $this->expires_at = $expType;
                } else {
                    if(!empty($expType)){
                        switch ($expType){
                            case 'five-minutes':
                                $expireDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +5 minutes"));
                                break;
                            case 'one-hour':
                                $expireDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +1 hour"));
                                break;
                            case 'tomorrow':
                                $expireDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +24 hours"));
                                break;
                            case 'week':
                                $expireDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +1 week"));
                                break;
                            case 'month':
                                $expireDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +1 month"));
                                break;
                            case 'year':
                                $expireDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +1 year"));
                                break;
                        }
                    }
                    $this->expires_at = $expireDate;
                }
            }
        }
    }
}
