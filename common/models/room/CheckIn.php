<?php

namespace common\models\room;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%check_in}}".
 *
 * @property int $check_in_id 入住编号
 * @property int $member_id 会员编号
 * @property int $room_id 房间编号
 * @property float $deposit 押金
 * @property float $charge 消费金额
 * @property string $mark 备注
 * @property int $in_time 入住时间
 * @property int $out_time 退房时间
 */
class CheckIn extends BaseModel
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
            [['member_id', 'room_id', 'in_time', 'out_time'], 'integer'],
            [['deposit', 'charge'], 'number'],
            [['mark'], 'string', 'max' => 20],
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
            'room_id' => '房间编号',
            'deposit' => '押金',
            'charge' => '消费金额',
            'mark' => '备注',
            'in_time' => '入住时间',
            'out_time' => '退房时间',
        ];
    }
}
