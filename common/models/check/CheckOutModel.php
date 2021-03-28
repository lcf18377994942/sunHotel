<?php

namespace common\models\check;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%check_out}}".
 *
 * @property int $check_out_id 退房编号
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
class CheckOutModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%check_out}}';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'check_out_id' => '退房编号',
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
}
