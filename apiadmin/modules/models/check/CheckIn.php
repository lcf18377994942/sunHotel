<?php
/*
	入住登记相关
*/
namespace apiadmin\modules\models\check;
use common\models\member\MemberModel;
use common\models\check\CheckInModel;
use common\models\room\RoomModel;
use common\models\room\RoomTypeModel;

class CheckIn extends CheckInModel
{
    /*
        入住列表显示
        * whereArr 条件
        * params 基本参数 包含 field order page limit
    */
    public static function CheckInList($whereArr,$params)
    {
        $model  = self::find()->from(['ci'=>self::tableName()])
            ->leftJoin(MemberModel::tableName().' m','m.member_id=ci.member_id')
            ->leftJoin(MemberModel::tableName().' sm','sm.member_id=ci.second_member_id')
            ->leftJoin(RoomModel::tableName().' r','r.room_id=ci.room_id')
            ->leftJoin(RoomTypeModel::tableName().' rt','rt.type_id=r.type_id');

        return self::getlist($model,$whereArr,$params);
    }

    /*
        删除入住
        member_id
    */
    public static function CheckInDel($CheckInId)
    {
        return self::deleteAll(['check_in_id'=>$CheckInId]);
    }

}