<?php
/*
	预订登记相关
*/
namespace apiadmin\modules\models\room;
use common\models\member\MemberModel;
use common\models\room\Reserve as ReserveModel;
use common\models\room\RoomType;

class Reserve extends ReserveModel
{
    /*
        预订列表显示
        * whereArr 条件
        * params 基本参数 包含 field order page limit
    */
    public static function ReserveList($whereArr,$params)
    {
        $model  = self::find()->from(['re'=>self::tableName()])
            ->leftJoin(MemberModel::tableName().' m','m.member_id=re.member_id')
            ->leftJoin(Room::tableName().' r','r.room_id=re.room_id')
            ->leftJoin(RoomType::tableName().' rt','rt.type_id=r.type_id');

        return self::getlist($model,$whereArr,$params);
    }

    /*
        删除预订
        member_id
    */
    public static function ReserveDel($reserveId)
    {
        return self::deleteAll(['reserve_id'=>$reserveId]);
    }

}