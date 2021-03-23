<?php

namespace common\models\room;
use apiadmin\modules\models\room\Room;
use common\models\BaseModel;
use common\models\member\MemberModel;
use common\models\member\MemberTypeModel;

/**
 * This is the model class for table "{{%reserve}}".
 *
 * @property int $reserve_id 预定编号
 * @property int $member_id 会员编号
 * @property int $room_id 房间编号
 * @property float $deposit 预付押金
 * @property string $mark 备注
 * @property int $in_time 入住时间
 * @property int $out_time 退房时间
 */
class Reserve extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%reserve}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'room_id', 'in_time', 'out_time'], 'integer'],
            [['deposit'], 'number'],
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
            'reserve_id' => '预定编号',
            'member_id' => '会员编号',
            'room_id' => '房间编号',
            'deposit' => '预付押金',
            'mark' => '备注',
            'in_time' => '入住时间',
            'out_time' => '退房时间',
        ];
    }

    /*
        通过id获取入住信息
    */
    public static function getReserveIdById($Id,$field=['*'])
    {
        if(!$Id) return false;
        return self::find()->select($field)->from(['re'=>self::tableName()])
            ->leftJoin(MemberModel::tableName().' m','m.member_id=re.member_id')
            ->leftJoin(MemberTypeModel::tableName().' mt','mt.member_type_id=m.member_type_id')
            ->leftJoin(Room::tableName().' r','r.room_id=re.room_id')
            ->leftJoin(RoomType::tableName().' rt','rt.type_id=r.type_id')->where(['reserve_id' => $Id])->asArray()->one();
    }
}
