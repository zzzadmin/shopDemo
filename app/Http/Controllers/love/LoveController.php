<?php

namespace App\Http\Controllers\love;
use App\Http\Tools\Wechat;
use App\Http\Model\User_openid;
use App\Http\Model\Wechat_openid;//模型
use App\Http\Model\Admin;
use App\Http\Model\Love;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoveController extends Controller
{
	public $wechat;
    public function __construct(Wechat $wechat)
    {
        $this->wechat = $wechat;
    }

    // 粉丝列表
    public function index(){
        // easy WeChat查询粉丝列表
        $openid_list = $this->wechat->app->user->list($nextOpenId = null);  // $nextOpenId 可选
        // dd($openid_list);
        return view('love.index',['info'=>$openid_list['data']['openid']]);
    }

    // 我要表白视图
	public function add(){
		$redirect_uri = 'http://www.shopdemo.com/love/code';
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WECHAT_APPID').'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect ';
        header('Location:'.$url);
		return view('love.add');
	}

	public function code(Request $request){
		$data = $request->all();
		dd($data);
	}

    // 把openid传过去
	public function send(Request $request){
		$openid = $request->all()['openid'];
		return view('love.send',['openid'=>$openid]);	
	}

	public function send_do(Request $request){
        // 接收传来的信息
        $data = $request->all();
		$openid = $request->all()['openid'];
        $user = $this->wechat->app->user->get($openid);
        //模板消息 easy WeChat
        $this->wechat->app->template_message->send([
            'touser' => $openid,
            'template_id' => '1j5MZutLOkf0WtNv6Eci7CQ5PIhu1z7hbVhCb57cIMc',
            'url' => env('APP_URL').'/love/index',
            'data' => [
                'first' => $data['user_type'] == 2?'匿名用户':$user['nickname'],
                'keyword1' => $data['content'],
            ],
        ]);
        // 入库
        $result = Love::insert([
            'from_user'=>$openid,
            'content'=>$data['content'],
            'to_user'=>$openid,
            'add_time'=>time()
        ]);
        dd($result);
        dd();

    	$access_token = $this->wechat->get_access_token();
    	// 用户信息
    	$Wechat_openid_user = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN");
    	$Wechat_openid_info = json_decode($Wechat_openid_user,1);
    	$nickname = $Wechat_openid_info['nickname'];//名称
    	// dd();

    	// dd($openid);
    	// $this->wechat->push_template($openid);

    	$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->wechat->get_access_token();
        $data = [
            'touser'=>$openid,
            'template_id'=>'axAay26XpP_zxvrCkIGejrSaY-VjftDAUljevOeC19Y',
            'url'=>'http://www.baidu.com',
            'data' => [
                'first' => [
                    'value' => $nickname,
                    'color' => ''
                ],
                'keyword1' => [
                    'value' => $content,
                    'color' => ''
                ]
            ]
        ];
        $re = $this->wechat->post($url,json_encode($data));
        dd($re);
	}


}
