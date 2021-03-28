<?php
/*
	房间相关
*/
namespace apiadmin\modules\models\room;
use common\models\room\RoomModel;
use common\models\room\RoomFloorModel;
use common\models\room\RoomStateModel;
use common\models\room\RoomTypeModel;

class Room extends RoomModel
{
    /*
        房间列表显示
        * whereArr 条件
        * params 基本参数 包含 field order page limit
        * extends  扩展信息 一些相关的信息
    */
    public static function RoomList($whereArr,$params)
    {
        $model  = self::find()->from(['r'=>self::tableName()])
            ->leftJoin(RoomStateModel::tableName().' rs','rs.state_id=r.state_id')
            ->leftJoin(RoomTypeModel::tableName().' rt','rt.type_id=r.type_id')
            ->leftJoin(RoomFloorModel::tableName().' rf','rf.floor_id=r.floor_id');

        return self::getlist($model,$whereArr,$params);
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