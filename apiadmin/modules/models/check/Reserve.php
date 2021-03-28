<?php
/*
	预订登记相关
*/
namespace apiadmin\modules\models\check;
use common\models\member\MemberModel;
use common\models\check\ReserveModel;
use common\models\room\RoomModel;
use common\models\room\RoomTypeModel;

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
            ->leftJoin(MemberModel::tableName().' sm','sm.member_id=re.second_member_id')
            ->leftJoin(RoomModel::tableName().' r','r.room_id=re.room_id')
            ->leftJoin(RoomTypeModel::tableName().' rt','rt.type_id=r.type_id');

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