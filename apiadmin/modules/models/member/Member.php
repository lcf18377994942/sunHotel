<?php
/*
	会员相关
*/
namespace apiadmin\modules\models\member;
use common\models\member\MemberTypeModel;
use common\models\member\MemberModel;
use common\models\room\RoomStateModel;

class Member extends MemberModel
{

	/*
		会员列表显示
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息
	*/
	public static function MemberList($whereArr,$params,$extends=array())
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

        $state = RoomStateModel::find()->indexBy('state_id')->asArray()->all();
        foreach($data as &$val)
        {
            foreach($extends as $eType)
            {
                //获取分类
                if($eType=='member_type'){
                    $type = MemberTypeModel::getMemberTypeById($val['member_type_id']);
                    $val['member_type_name'] = $type?$type['member_type_name']:'无';
                } elseif ($eType=='room_state') {
                    $val['state_id'] = isset($state[$val['state_id']]) ? $state[$val['state_id']]['state_name']:'无';
                }
            }
        }

		return $data;
	}

	/*
		删除会员
		member_id
	*/
	public static function MemberDel($memberId)
	{
		$where = ['member_id'=>$memberId];
		$res   = self::deleteAll($where);
		return $res;
	}

}