<?php

namespace common\models\member;
use common\models\BaseModel;

/**
 * This is the model class for table "{{%member_type}}".
 *
 * @property int $member_type_id 类别编号
 * @property string $member_type_name 类别名称
 * @property float $trate 折扣
 * @property int $created_time 创建时间
 * @property int $updated_time 更新时间
 */
class MemberTypeModel extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'member_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_type_name'], 'required'],
            [['trate'], 'number'],
            [['created_time', 'updated_time'], 'integer'],
            [['member_type_name'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'member_type_id' => '类别编号',
            'member_type_name' => '类别名称',
            'trate' => '折扣',
            'created_time' => '创建时间',
            'updated_time' => '更新时间',
        ];
    }

    /**
     * 获取一条数据
     */
    public static function getMemberTypeById($id)
    {
        return MemberTypeModel::findOne(['member_type_id'=>$id]);
    }
}
