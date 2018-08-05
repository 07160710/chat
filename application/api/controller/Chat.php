<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/5
 * Time: 23:25
 */
namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class Chat extends Controller
{
    /**
     * 文本数据持久化储存
     */
    public function save_message()
    {
        if(Request::instance()->isAjax()){
            $message = input('post.');

            $datas['fromid'] = $message['fromid'];
            $datas['fromname'] = $this->getName($datas['fromid']);
            $datas['toid'] = $message['toid'];
            $datas['toname'] = $this->getName($datas['toid']);
            $datas['content'] = $message['data'];
            $datas['time'] = $message['time'];
            $datas['isread'] = $message['isread'];
            $datas['type'] = 1;

            Db::name('communication')->insert($datas);
        }
    }

    //根据用户id返回姓名
    public function getName($uid)
    {
        $userinfo = Db::name('user')->where('id',$uid)->field('nickname')->find();

        return $userinfo['nickname'];
    }
}