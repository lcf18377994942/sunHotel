<?php
namespace apiadmin\modules\controllers\check;
use apiadmin\modules\controllers\CoreController;
use apiadmin\modules\models\check\Reserve;
use common\models\member\MemberModel;
use common\models\room\RoomModel;
use common\models\room\RoomStateModel;

/**
 * 预订相关控制器
 */

class ReserveController extends CoreController
{
    /*
        *预订列表
    */
    public function actionReserveList()
    {
        $where  = $this->formartWhere();
        $params = array(
            'field'	=> ['reserve_id','m.member_name','sm.member_name second_member_name','room_name','type_name','re.deposit','re.mark','in_time','out_time'],
            'order' => 'reserve_id desc',
            'page'	=> $this->request('page','1'),
            'limit' => $this->request('page_size',10),
        );
        $list = Reserve::reserveList($where,$params);
        $pages = Reserve::$pages;
        $this->out('预订列表',$list,array('pages'=>$pages));
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
        删除预订
        reserve_id
    */
    public function actionReserve_del()
    {
        if(!$reserveId = $this->request('reserve_id')) $this->error('参数错误');
        $reserveInfo = Reserve::getReserveId($reserveId);
        $res = Reserve::reserveDel($reserveId);
        if($res){
            //修改用户和房间状态
            MemberModel::setStateId($reserveInfo['member_id'],RoomStateModel::getRoomStateId());
            RoomModel::setStateId($reserveInfo['room_id'],RoomStateModel::getRoomStateId());
            if (!empty($params['second_member_id'])) {
                MemberModel::setStateId($params['second_member_id'],RoomStateModel::getRoomStateId());
            }
            $this->out('退订成功');
        }
        $this->error('退订失败');
    }

    /*
        删除预订，并添加入住
        reserve_id
    */
    public function actionCheck_in_add()
    {
        if(!$reserveId = $this->request('reserve_id')) $this->error('参数错误');
        $params = Reserve::getReserveId($reserveId);
        $params['user_id'] = $this->_uid;
        $params['charge'] = $params['deposit'];
        $params['deposit'] = RoomModel::getRoomById($params['room_id'])['deposit'];

        $checkIn = $this->model('check\CheckIn',$params,'Reg');
        if(!$checkIn->save(false)) $this->error('添加失败');
        //删除预订数据
        Reserve::reserveDel($reserveId);
        //修改用户和房间状态
        MemberModel::setStateId($params['member_id'],RoomStateModel::getRoomStateId('已入住'));
        if (isset($params['second_member_id'])) {
            MemberModel::setStateId($params['second_member_id'],RoomStateModel::getRoomStateId('已入住'));
        }
        RoomModel::setStateId($params['room_id'],RoomStateModel::getRoomStateId('已入住'));
        //返回数据
        $this->out('添加成功',$params);
    }

    /*
        获取单预订信息
        * reserve_id 预订ID
    */
    public function actionReserve()
    {
        if(!$reserveId = $this->request('reserve_id')) $this->error('参数错误');
        $field = ['reserve_id','re.member_id','member_card_id','re.room_id','room_name','type_name','price','discount','r.deposit','in_time','out_time'];
        $reserve = Reserve::getReserveIdById($reserveId,$field);
        $this->out('预订信息',$reserve);
    }

    //预订信息修改
    public function actionReserve_edit()
    {
        $params = $this->request;
        $params['user_id'] = $this->_uid;
        $reserve = $this->model('check\reserve',$params,'Edit',$params['reserve_id']);
        $reserve->deposit = bcadd(bcmul($params['price'],$params['discount'],2),RoomModel::getRoomById($reserve['room_id'])['deposit'],2);
        $msg = '';
        if ($params['room_id'] != $params['old_room_id']) {
            $msg .= '【原房间名称：'.$params['room_name'].'】';
            RoomModel::setStateId($params['room_id'],RoomStateModel::getRoomStateId('已预订'));
            RoomModel::setStateId($params['old_room_id'],RoomStateModel::getRoomStateId());
        }
        if ($params['deposit'] != $reserve->deposit) {
            if ($params['deposit'] < $reserve->deposit) {
                $msg .= '【原消费：'.$params['deposit'].'，需补交：'.($reserve->deposit - $params['deposit']).'元】';
            } else {
                $msg .= '【原消费：'.$params['deposit'].'，需退还：'.($params['deposit'] - $reserve->deposit).'元】';
            }
        }
        $reserve->mark = $msg;
        if(!$reserve->save(false)) $this->error('修改失败');
        //返回数据
        $this->out('修改成功');
    }

    //预订添加
    public function actionReserve_add()
    {
        $params = $this->request;
        $params['user_id'] = $this->_uid;
        $reserve = $this->model('check\reserve',$params,'Reg');
        $reserve->deposit = bcadd(bcmul($params['price'],$params['discount'],2),RoomModel::getRoomById($reserve['room_id'])['deposit'],2);
        if(!$reserve->save(false)) $this->error('添加失败');
        //修改用户和房间状态
        MemberModel::setStateId($params['member_id'],RoomStateModel::getRoomStateId('已预订'));
        RoomModel::setStateId($params['room_id'],RoomStateModel::getRoomStateId('已预订'));
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