<?php
/*
	房间相关
*/
namespace apiadmin\modules\models\room;
use common\models\room\Room as RoomModel;
use common\models\room\RoomFloor;
use common\models\room\RoomState;
use common\models\room\RoomType;

class Room extends RoomModel
{
    /*
        房间列表显示
        * whereArr 条件
        * params 基本参数 包含 field order page limit
        * extends  扩展信息 一些相关的信息
    */
    public static function RoomList($whereArr,$params,$extends=array())
    {
        $model  = self::find();
        $where  = $whereArr['where'];
        $whereAnd = isset($whereArr['whereAnd'])?$whereArr['whereAnd']:[];
        $models = self::queryFormart($model,$where,$params,$whereAnd);
        $model  = $models['model'];
        self::$pages = $models['pages'];

        $data  = $model->asArray()->all();
        if(!$data) return array();
        //扩展信息
        if(!$extends) return $data;

        $type = RoomType::getRoomTypeAll();
        $state = RoomState::getRoomStateAll();
        $floor = RoomFloor::getRoomFloorAll();
        foreach($data as &$val)
        {
            foreach($extends as $eType)
            {
                //获取分类
                if($eType=='room_type'){
                    $val['type_id'] = isset($type[$val['type_id']]) ? $type[$val['type_id']]['type_name']:'无';
                    $val['price'] = isset($type[$val['type_id']]) ? $type[$val['type_id']]['price']:'0';
                } elseif ($eType=='room_state') {
                    $val['state_id'] = isset($state[$val['state_id']]) ? $state[$val['state_id']]['state_name']:'无';
                } elseif ($eType=='room_floor') {
                    $val['floor_id'] = isset($floor[$val['floor_id']]) ? $floor[$val['floor_id']]['floor_number']:'无';
                }
            }
        }
        /*$data['type'] = $type;
        $data['state'] = $state;*/
        return $data;
    }

    /*
        删除房间
        member_id
    */
    public static function RoomDel($roomId)
    {
        return self::deleteAll(['room_id'=>$roomId]);
    }

}