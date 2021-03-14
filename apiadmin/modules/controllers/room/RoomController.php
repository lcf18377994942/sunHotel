<?php
namespace apiadmin\modules\controllers\room;
use apiadmin\modules\controllers\CoreController;
use common\models\room\RoomFloor;
use common\models\room\RoomState;
use common\models\room\RoomType;
use Yii;
use common\utils\OutputExecl;
use apiadmin\modules\models\room\Room;

/**
 * 房间相关控制器
 */

class RoomController extends CoreController
{
    /*
        *房间列表
    */
    public function actionRoom_list()
    {
        $where  = $this->formartWhere();
        $params = array(
            'field'	=> ['room_id','room_name','type_id','state_id','floor_id','mark','create_time','update_time'],
            'order' => 'room_id desc',
            'page'	=> $this->request('page','1'),
            'limit' => $this->request('page_size',10),
        );
        $extends = array('room_type','room_state','room_floor');
        $list = Room::RoomList($where,$params,$extends);
        $pages = Room::$pages;
        $this->out('房间列表',$list,array('pages'=>$pages));
    }

    //获取房间下拉数据
    public function actionGetListAll()
    {
        $list['type'] = RoomType::getRoomTypeAll();
        $list['state'] = RoomState::getRoomStateAll();
        $list['floor'] = RoomFloor::getRoomFloorAll();
        $this->out('类型状态',$list);
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
            }elseif ($k=='room_name') {
                $whereAnd[] = ['like',$k,$val];
            }else
            {
                $where[$k] = $val;
            }
        }

        return array('where'=>$where,'whereAnd'=>$whereAnd);
    }

    /*
        删除房间
        room_id
    */
    public function actionRoom_del()
    {
        if(!$roomId = $this->request('room_id')) $this->error('参数错误');
        $res = Room::RoomDel($roomId);
        if($res) $this->out('删除成功');
        $this->error('删除失败');
    }

    /*
        获取单房间信息
        * room_id 房间ID
    */
    public function actionRoom()
    {
        if(!$roomId = $this->request('room_id')) $this->error('参数错误');
        $field  = ['room_id','room_name',Room::tableName().'.type_id','price','type_name','state_id','floor_id','mark'];
        $room = Room::getRoomById($roomId,$field);
        $this->out('房间信息',$room);
    }

    /*
        通过id或是电话 搜索房间信息
        * room
    */
    public function actionQuery_room()
    {
        if(!$roomKey = $this->request('room_key')) $this->error('参数错误');
        $field  = ['room_id','room_name','type_id'];
        $room = Room::queryRoom($roomKey,$field);
        $this->out('房间信息',$room);
    }

    //房间信息修改
    public function actionRoom_edit()
    {
        $this->save('room\Room','Edit',$this->request('room_id'));
    }

    //房间添加
    public function actionRoom_add()
    {
        $this->save('room\Room');
    }

    //房间配置添加
    public function actionRoom_basics_add()
    {
        if (!empty($this->request('type_name'))) {
            $Model = $this->model('room\RoomType',$this->request,'Reg');
            if(!$Model->save(false)) $this->error('添加失败');
        }
        if (!empty($this->request('state_name'))) {
            $Model = $this->model('room\RoomState',$this->request,'Reg');
            if(!$Model->save(false)) $this->error('添加失败');
        }
        if (!empty($this->request('floor_number'))) {
            $Model = $this->model('room\RoomFloor',$this->request,'Reg');
            if(!$Model->save(false)) $this->error('添加失败');
        }
        $this->out('添加成功');
    }

    //房间信息修改
    public function actionRoom_basics_edit()
    {
        if (!empty($this->request('type_id'))) {
            $Model = $this->model('room\RoomType',$this->request,'Edit',$this->request('type_id'));
            if(!$Model->save(false)) $this->error('类型修改失败');
        }
        if (!empty($this->request('state_id'))) {
            $Model = $this->model('room\RoomState',$this->request,'Edit',$this->request('state_id'));
            if(!$Model->save(false)) $this->error('状态修改失败');
        }
        if (!empty($this->request('floor_id'))) {
            $Model = $this->model('room\RoomFloor',$this->request,'Edit',$this->request('floor_id'));
            if(!$Model->save(false)) $this->error('楼层修改失败');
        }
        $this->out('修改成功');
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