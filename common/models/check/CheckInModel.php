<?php

namespace common\models\check;
use common\models\room\RoomModel;
use common\models\room\RoomTypeModel;
use common\models\BaseModel;
use common\models\member\MemberModel;
use common\models\member\MemberTypeModel;

/**
 * This is the model class for table "{{%in}}".
 *
 * @property int $check_in_id 入住编号
 * @property int $member_id 会员编号
 * @property int $second_member_id 次会员编号
 * @property int $room_id 房间编号
 * @property int $user_id 操作人编号
 * @property float $deposit 押金
 * @property float $charge 消费金额
 * @property string $mark 备注
 * @property int $in_time 入住时间
 * @property int $out_time 退房时间
 */
class CheckInModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%check_in}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'second_member_id', 'room_id', 'in_time', 'out_time', 'user_id'], 'integer'],
            [['deposit', 'charge'], 'number'],
            [['mark'], 'string', 'max' => 100],
            [['member_id','room_id','in_time','out_time','deposit'],'required','on'=>'Reg'],
            [['member_id','room_id'],'unique','on'=>'Reg'],
            [['member_id','room_id','in_time','out_time','deposit'],'required','on'=>'Edit'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'check_in_id' => '入住编号',
            'member_id' => '会员编号',
            'second_member_id' => '次会员编号',
            'room_id' => '房间编号',
            'user_id' => '操作员编号',
            'deposit' => '押金',
            'charge' => '消费金额',
            'mark' => '备注',
            'in_time' => '入住时间',
            'out_time' => '退房时间',
        ];
    }

    /*
        通过id获取入住信息
    */
    public static function getCheckIdById($Id,$field=['*'])
    {
        if(!$Id) return false;
        return self::find()->select($field)->from(['ci'=>self::tableName()])
            ->leftJoin(MemberModel::tableName().' m','m.member_id=ci.member_id')
            ->leftJoin(MemberTypeModel::tableName().' mt','mt.member_type_id=m.member_type_id')
            ->leftJoin(RoomModel::tableName().' r','r.room_id=ci.room_id')
            ->leftJoin(RoomTypeModel::tableName().' rt','rt.type_id=r.type_id')->where(['check_in_id' => $Id])->asArray()->one();
    }
}
