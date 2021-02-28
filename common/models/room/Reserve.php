<?php

namespace common\models\room;
use common\models\BaseModel;

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
            [['mark'], 'string', 'max' => 20],
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
}
