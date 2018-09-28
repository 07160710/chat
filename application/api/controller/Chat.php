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

    /*
     * 获取头像信息
     */
    public function get_head()
    {
        if(Request::instance()->isAjax()){
            $fromid = input('fromid');
            $toid = input('toid');
            $frominfo = Db::name('user')->where('id',$fromid)->field('headimgurl')->find();
            $toinfo = Db::name('user')->where('id',$toid)->field('headimgurl')->find();

            return [
                'from_head' => $frominfo['headimgurl'],
                'to_head' => $toinfo['headimgurl']
            ];
        }
    }

    /**
     * 用户id获取用户姓名
     */
    public function get_name()
    {
        if(Request::instance()->isAjax()){
            $uid = input('uid');
            $toinfo = Db::name('user')->where('id',$uid)->field('nickname')->find();
            return ["toname" => $toinfo['nickname']];
        }
    }

    /**
     * 页面加载返回聊天记录
     */
    public function load()
    {
        if(Request::instance()->isAjax()){
            $fromid = input('fromid');
            $toid = input('toid');

            $count = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)',['fromid'=>$fromid,'toid'=>$toid,'toid1'=>$toid,'fromid1'=>$fromid])->count();

            if($count >= 10){
                $message = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)',['fromid'=>$fromid,'toid'=>$toid,'toid1'=>$toid,'fromid1'=>$fromid])->limit($count-10,10)->order('id')->select();
            }else {
                $message = Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)', ['fromid' => $fromid, 'toid' => $toid, 'toid1' => $toid, 'fromid1' => $fromid])->select();
            }

            return $message;
            //Db::name('communication')->where('(fromid=:fromid and toid=:toid) || (fromid=:toid1 and toid=:fromid1)',['fromid'=>$fromid,'toid'=>$toid,'toid1'=>$toid,'fromid1'=>$fromid])->select();

        }
    }
}