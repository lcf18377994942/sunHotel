<?php
namespace apiadmin\modules\controllers\room;
use apiadmin\modules\controllers\CoreController;
use apiadmin\modules\models\room\CheckIn;
use common\models\member\MemberModel;
use common\models\room\CheckOut;
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
            'field'	=> ['check_in_id','member_name','room_name','type_name','deposit','ci.charge','ci.mark','in_time','out_time'],
            'order' => 'check_in_id desc',
            'page'	=> $this->request('page','1'),
            'limit' => $this->request('page_size',10),
        );
        $list = CheckIn::CheckInList($where,$params);
        $pages = CheckIn::$pages;
        $this->out('入住列表',$list,array('pages'=>$pages));
    }

    //获取入住下拉数据
    public function actionGetListAll()
    {
        $list['member'] = MemberModel::getMemberAll();
        $list['room'] = Room::getRoomAll();
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
            if($k=='date')
            {
                if(!$val['0'] || !$val['1']) continue;
                $whereAnd[] = ['between', 'update_time', strtotime($val[0]),strtotime($val[1])];
            }elseif ($k=='CheckIn_name') {
                $whereAnd[] = ['like',$k,$val];
            }else
            {
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
        $checkInfo = CheckIn::getCheckIdById($CheckInId);
        $res = CheckIn::CheckInDel($CheckInId);
        if($res){
            //修改用户和房间状态
            MemberModel::setStateId($checkInfo['member_id'],RoomState::getRoomStateId());
            Room::setStateId($checkInfo['room_id'],RoomState::getRoomStateId());
            //添加退房信息
            $checkOut = $this->model('room\CheckOut',$checkInfo,'Reg');
            $checkOut->save();
            $this->out('删除成功');
        }
        $this->error('删除失败');
    }

    /*
        获取单入住信息
        * CheckIn_id 入住ID
    */
    public function actionCheck_in()
    {
        if(!$CheckInId = $this->request('check_in_id')) $this->error('参数错误');
        $field  = ['CheckIn_id','CheckIn_name','type_id','state_id','floor_id','mark'];
        $CheckIn = CheckIn::getCheckIdById($CheckInId,$field);
        $this->out('入住信息',$CheckIn);
    }

    /*
        通过id或是电话 搜索入住信息
        * CheckIn
    */
    public function actionQuery_Check_in()
    {
        if(!$CheckInKey = $this->request('CheckIn_key')) $this->error('参数错误');
        $field  = ['CheckIn_id','CheckIn_name','type_id'];
        $CheckIn = CheckIn::queryCheckIn($CheckInKey,$field);
        $this->out('入住信息',$CheckIn);
    }

    //入住信息修改
    public function actionCheck_in_edit()
    {
        $this->save('room\CheckIn','Edit',$this->request('CheckIn_id'));
    }

    //入住添加
    public function actionCheck_in_add()
    {
        $params = $this->request;
        $checkIn = $this->model('room\CheckIn',$params,'Reg');
        $checkIn->charge = bcadd(bcmul($params['price'],$params['trate'],2),$params['deposit'],2);
        if(!$checkIn->save(false)) $this->error('添加失败');
        //修改用户和房间状态
        MemberModel::setStateId($params['member_id'],RoomState::getRoomStateId('已入住'));
        Room::setStateId($params['room_id'],RoomState::getRoomStateId('已入住'));
        //返回数据
        $this->out('添加成功',$params);
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