<?php
namespace apiadmin\modules\controllers\room;
use apiadmin\modules\controllers\CoreController;
use apiadmin\modules\models\room\CheckIn;
use common\models\member\MemberModel;
use common\models\room\Room;
use common\models\room\RoomState;

/**
 * 入住相关控制器
 */

class CheckInController extends CoreController
{
    /*
        *入住列表
    */
    public function actionCheckInList()
    {
        $where  = $this->formartWhere();
        $params = array(
            'field'	=> ['check_in_id','member_name','room_name','type_name','ci.deposit','ci.charge','ci.mark','in_time','out_time'],
            'order' => 'check_in_id desc',
            'page'	=> $this->request('page','1'),
            'limit' => $this->request('page_size',10),
        );
        $list = CheckIn::CheckInList($where,$params);
        $pages = CheckIn::$pages;
        $this->out('入住列表',$list,array('pages'=>$pages));
    }

    //获取未入住下拉数据
    public function actionGetListAll()
    {
        $params = $this->request;
        $roomId = isset($params['room_id']) ? $params['room_id'] : 0;
        $list['room'] = Room::getNullRoom($roomId);

        $memberId = isset($params['member_id']) ? $params['member_id'] : 0;
        $list['member'] = MemberModel::getMemberAll($memberId);
        $this->out('会员入住',$list);
    }

    //组装条件
    public function formartWhere()
    {
        $where = [];
        $whereAnd = [];
        $searchKeys = json_decode($this->request('search'),1);
        if(!$searchKeys) return array('where'=>$where,'whereAnd'=>$whereAnd);
        foreach($searchKeys as $k=>$val)
        {
            if(!$val) continue;
            if($k=='inDate' || $k=='outDate')
            {
                if(!$val['0'] || !$val['1']) continue;
                $date = $k=='inDate' ? 'in_time' :'out_time';
                $whereAnd[] = ['between', $date, strtotime($val[0]),strtotime($val[1])];
            }elseif ($k=='member_name' || $k=='room_name') {
                $whereAnd[] = ['like',$k,$val];
            }else
            {
                if ($k=='type_id') {
                    $k = 'r.type_id';
                }
                $where[$k] = $val;
            }
        }

        return array('where'=>$where,'whereAnd'=>$whereAnd);
    }

    /*
        删除入住
        CheckIn_id
    */
    public function actionCheck_in_del()
    {
        if(!$CheckInId = $this->request('check_in_id')) $this->error('参数错误');
        $checkInfo = CheckIn::getInfo(['check_in_id' => $CheckInId]);
        $res = CheckIn::CheckInDel($CheckInId);
        if($res){
            //修改用户和房间状态
            MemberModel::setStateId($checkInfo['member_id'],RoomState::getRoomStateId());
            Room::setStateId($checkInfo['room_id'],RoomState::getRoomStateId());
            //添加退房信息
            $checkOut = $this->model('room\CheckOut',$checkInfo,'Reg');
            $checkOut->charge = $checkOut->charge - $checkOut->deposit;
            $checkOut->out_time = time();
            $checkOut->save();
            $this->out('退房成功');
        }
        $this->error('退房失败');
    }

    /*
        获取单入住信息
        * CheckIn_id 入住ID
    */
    public function actionCheck_in()
    {
        if(!$CheckInId = $this->request('check_in_id')) $this->error('参数错误');
        $field = ['ci.*','member_card_id','room_name','type_name','price','discount'];
        $CheckIn = CheckIn::getCheckIdById($CheckInId,$field);
        $this->out('入住信息',$CheckIn);
    }

    //入住信息修改
    public function actionCheck_in_edit()
    {
        $params = $this->request;
        $checkIn = $this->model('room\CheckIn',$params,'Edit',$params['check_in_id']);
        $checkIn->charge = bcadd(bcmul($params['price'],$params['discount'],2),$params['deposit'],2);
        $msg = '';
        if ($params['room_id'] != $params['old_room_id']) {
            $msg .= '【原房间名称：'.$params['room_name'].'】';
            Room::setStateId($params['room_id'],RoomState::getRoomStateId('已入住'));
            Room::setStateId($params['old_room_id'],RoomState::getRoomStateId());
        }
        if ($params['charge'] != $checkIn->charge) {
            if ($params['charge'] < $checkIn->charge) {
                $msg .= '【原消费：'.$params['charge'].'，需补交：'.($checkIn->charge - $params['charge']).'元】';
            } else {
                $msg .= '【原消费：'.$params['charge'].'，需退还：'.($params['charge'] - $checkIn->charge).'元】';
            }
        }
        $checkIn->mark = $msg;
        if(!$checkIn->save(false)) $this->error('修改失败');
        $this->out('修改成功');
    }

    //入住添加
    public function actionCheck_in_add()
    {
        $params = $this->request;
        $checkIn = $this->model('room\CheckIn',$params,'Reg');
        $checkIn->charge = bcadd(bcmul($params['price'],$params['discount'],2),$params['deposit'],2);
        if(!$checkIn->save(false)) $this->error('添加失败');
        //修改用户和房间状态
        MemberModel::setStateId($params['member_id'],RoomState::getRoomStateId('已入住'));
        Room::setStateId($params['room_id'],RoomState::getRoomStateId('已入住'));
        //返回数据
        $this->out('添加成功');
    }

    //会员导出
    public function actionExport()
    {
        /*$where  = $this->formartWhere();
        $params = array(
            'field'	=> ['member_id','member_name','member_mobile','invite_id','state',
                'create_time','update_time'],
            'order' => 'member_id desc',
            'page'	=> $this->request('page','1'),
            'limit' => $this->request('page_size',10),
        );
        $list = Member::MemberList($where,$params);

        //组织导出数据
        $exportData = array();
        foreach($list as $val)
        {

            $temp = [];
            $temp[] = $val['member_id'];
            $temp[] = $val['member_name'];
            $temp[] = $val['member_mobile'];
            $temp[] = $val['state']?'正常':'冻结';
            $temp[] = date("Y-m-d H:i:s",$val['create_time']);
            $exportData[] = $temp;
        }
        $headData = array('A1'=>'会员ID','B1'=>'会员姓名','C1'=>'会员电话','D1'=>'会员状态','E1'=>'注册时间');
        $fileName = 'member-'.date('Y-m-d').'.xls';
        $execlObj = new OutputExecl();
        $res = $execlObj->output($headData,$exportData,$fileName);
        if($res)
            $this->out('下载地址',array('url'=>$res));
        else
            $this->error('导出失败');*/
    }
}