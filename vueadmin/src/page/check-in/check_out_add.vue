<template>
    <el-dialog
        :before-close="handleClose"
        title="入住编辑"
        :visible.sync="dialogVisible"
        width="40%">
        <el-form ref="form" :model="form" label-width="100px">
            <el-row :gutter=24>
                <el-col :span="12">
                    <el-form-item label="会员名称">
                        <el-select style="width: 100%" v-model="form.member_id" :disabled="disable" filterable placeholder="请选择会员"
                                   @change="getMember">
                            <el-option
                                v-for="item in options"
                                :key="item.member_id"
                                :label="item.member_name"
                                :value="item.member_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间类型">
                        <el-input v-model="type_name" :disabled="disable"></el-input>
                    </el-form-item>
                    <el-form-item label="押金">
                        <el-input v-model="form.deposit" :disabled="disable" placeholder="默认：20"></el-input>
                    </el-form-item>
                    <el-form-item label="入住时间">
                        <el-date-picker
                            v-model="form.in_time"
                            :picker-options="pickerOptions"
                            style="width: 100%"
                            :readonly="disable"
                            align="right"
                            placeholder="选择日期时间"
                            type="datetime">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="房间名称">
                        <el-select style="width: 100%" v-model="form.room_id" :disabled="disable" filterable placeholder="请选择房间" @change="getRoom">
                            <el-option
                                v-for="item in rOptions"
                                :key="item.room_id"
                                :label="item.room_name"
                                :value="item.room_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="价格">
                        <el-input v-model="form.price" :disabled="disable" placeholder="0.00"></el-input>
                    </el-form-item>
                    <el-form-item label="折扣比例">
                        <el-input v-model="discount" :disabled="disable" placeholder="1.00"></el-input>
                    </el-form-item>
                    <el-form-item label="退房时间">
                        <el-date-picker
                            v-model="form.out_time"
                            style="width: 100%"
                            :readonly="disable"
                            default-time="12:00:00"
                            placeholder="选择日期时间"
                            type="datetime">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-form-item label="备注">
                    <el-input
                        type="textarea"
                        :autosize="{ minRows: 2, maxRows: 4}"
                        placeholder="默认为空"
                        maxlength="100"
                        show-word-limit
                        resize="none"
                        v-model="form.mark">
                    </el-input>
                </el-form-item>
            </el-row>
        </el-form>

        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">取 消</el-button>
            <el-button type="primary" :disabled="disableSubmit" @click="handleSubmit">确 定</el-button>
        </span>
    </el-dialog>
</template>

<script type="text/javascript">
import {formatDate} from "../../components/js/filters";

export default {
    data() {
        return {
            dialogVisible:false,
            disable: true,
            disableSubmit: false,
            form: {
                check_in_id:'',
                member_id: '',
                room_id: '',
                old_room_id: '',
                room_name: '',
                charge: '',
                price: 0.00,
                deposit: '',
                mark:'',
                in_time: '',
                out_time: '',
            },
            type_name: '',
            time: '',
            discount: 1,
            pickerOptions: {
                shortcuts: [{
                    text: '今天',
                    onClick(picker) {
                        picker.$emit('pick', new Date());
                    }
                }, {
                    text: '明天',
                    onClick(picker) {
                        const date = new Date();
                        date.setTime(date.getTime() + 3600 * 1000 * 24);
                        picker.$emit('pick', date);
                    }
                }, {
                    text: '一周后',
                    onClick(picker) {
                        const date = new Date();
                        date.setTime(date.getTime() + 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', date);
                    }
                }]
            },
            options: [],
            rOptions: [],
        }
    },
    methods: {
        open(data){
            this.form.check_in_id = data;
            this.getData();
        },
        handleSubmit(){
            this.saveData();
        },
        handleClose(){
            this.dialogVisible=false
        },
        //初始化信息
        getData() {
            this.$post_('check/check-in/check_in',{check_in_id:this.form.check_in_id},(res) => {
                if(res.code==='0'){
                    this.form.member_id = res.data.member_id;
                    this.type_name = res.data.type_name;
                    this.form.room_id = res.data.room_id;
                    this.form.old_room_id = res.data.room_id;
                    this.form.room_name = res.data.room_name;
                    this.form.charge = res.data.charge;
                    this.form.price = res.data.price * res.data.discount;
                    this.discount = res.data.discount;
                    this.form.deposit = res.data.deposit;
                    this.form.in_time = formatDate(res.data.in_time*1000)
                    this.time = res.data.in_time;
                    this.form.out_time = formatDate(res.data.out_time*1000)
                    this.form.mark = res.data.mark;
                    this.getListAll();
                }
            });
        },
        //获取下拉数据
        getListAll() {
            this.$post_('check/check-in/get-list-all', {member_id: this.form.member_id,room_id: this.form.room_id}, (res) => {
                if (res.code) {
                    this.options = res.data.member;
                    this.rOptions = res.data.room;

                    var day = Math.ceil(((Date.parse(new Date()) / 1000) - this.time) / 86400);
                    var charge = day * this.form.price;
                    this.form.mark = "【客户已交：" + this.form.charge + "元，入住：" + day + "天，一共消费：" + charge + "元，";
                    if (this.form.charge >= charge) {
                        this.form.mark += "需退款：" + (this.form.charge - charge) +"元】";
                    } else {
                        this.form.mark += "需补交：" + (charge - this.form.charge) +"元】";
                    }
                    this.form.charge = charge;
                    this.dialogVisible=true;
                    this.disableSubmit = false;
                }
            });
        },
        //获取会员信息
        getMember(member_id) {
            this.$post_('member/member/member_info', {member_id: member_id}, (res) => {
                if (res.code === '0') {
                    this.form.discount = res.data.discount;
                    this.form.price = this.form.price * res.data.discount;
                }
            });
        },
        //获取房间信息
        getRoom(room_id) {
            this.$post_('room/room/room', {room_id: room_id}, (res) => {
                if (res.code === '0') {
                    this.type_name = res.data.type_name;
                    this.form.price = res.data.price * this.form.discount;
                }
            });
        },
        //保存数据
        saveData() {
            this.disableSubmit = true;
            this.$post_('check/check-in/check_in_del',{check_in_id:this.form.check_in_id,charge:this.form.charge,mark:this.form.mark},(res) => {
                if (res.code === '0') {
                    this.$emit('callbackCheckIn');
                    this.dialogVisible=false
                } else {
                    this.disableSubmit = false;
                    this.$message.error(res.msg);
                }
            })
        },
    }
}
</script>

<style scoped="scoped">
</style>
