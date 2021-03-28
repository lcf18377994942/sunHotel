<template>
    <el-dialog
        :before-close="handleClose"
        :visible.sync="dialogVisible"
        title="修改预订"
        width="40%">
        <el-form ref="form" :model="form" label-width="100px">
            <el-row :gutter=24>
                <el-col :span="12">
                    <el-form-item label="会员名称">
                        <el-select v-model="form.member_id" filterable placeholder="请选择会员"
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
                    <el-form-item label="折扣比例">
                        <el-input v-model="form.discount" :disabled="disable" placeholder="0.00"></el-input>
                    </el-form-item>
                    <el-form-item label="入住时间">
                        <el-date-picker
                            v-model="form.in_time"
                            :picker-options="pickerOptions"
                            style="width: 100%"
                            align="right"
                            placeholder="选择日期时间"
                            type="datetime">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="房间名称">
                        <el-select v-model="form.room_id" filterable placeholder="请选择房间" @change="getRoom">
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
                    <el-form-item label="押金">
                        <el-input v-model="form.deposit" :disabled="disable" placeholder="默认：20"></el-input>
                    </el-form-item>
                    <el-form-item label="退房时间">
                        <el-date-picker
                            v-model="form.out_time"
                            style="width: 100%"
                            default-time="12:00:00"
                            placeholder="选择日期时间"
                            type="datetime">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>

        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">取 消</el-button>
            <el-button type="primary" :disabled="disableSubmit" @click="handleSubmit">确 定</el-button>
        </span>
    </el-dialog>
</template>

<script type="text/javascript">
import {formatDate} from "../../../components/js/filters";

export default {
    data() {
        return {
            dialogVisible:false,
            disable: true,
            disableSubmit: false,
            form: {
                reserve_id:'',
                member_id: '',
                room_id: '',
                old_room_id: '',
                room_name: '',
                price: 0.00,
                discount: 1,
                deposit: '',
                old_deposit: '',
                mark: '',
                in_time: '',
                out_time: '',
            },
            type_name: '',
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
        open(reserve_id){
            this.form.reserve_id = reserve_id;
            this.getData();
        },
        handleSubmit(){
            this.saveData();
        },
        handleClose(){
            this.dialogVisible=false;
        },
        //初始化信息
        getData() {
            this.$post_('check/reserve/reserve',{reserve_id:this.form.reserve_id},(res) => {
                if(res.code==='0'){
                    this.form.member_id = res.data.member_id;
                    this.type_name = res.data.type_name;
                    this.form.room_id = res.data.room_id;
                    this.form.old_room_id = res.data.room_id;
                    this.form.room_name = res.data.room_name;
                    this.form.discount = res.data.discount;
                    this.form.price = res.data.price * this.form.discount;
                    this.form.deposit = res.data.deposit;
                    this.form.old_deposit = res.data.deposit;
                    this.form.in_time =formatDate(res.data.in_time*1000)
                    this.form.out_time = formatDate(res.data.out_time*1000)
                    this.getListAll();
                }
            });
        },
        //获取下拉数据
        getListAll() {
            this.$post_('check/check-in/get-list-all', {member_id: this.form.member_id,room_id: this.form.room_id}, (res) => {
                if(res.code ==='0') {
                    this.options = res.data.member;
                    this.rOptions = res.data.room;
                    this.dialogVisible = true;
                    this.disableSubmit = false;
                }
            });
        },
        //获取会员信息
        getMember(member_id) {
            this.$post_('member/member/member_info', {member_id: member_id}, (res) => {
                if(res.code ==='0') {
                    this.form.discount = res.data.discount;
                    this.form.price = this.form.price * res.data.discount;
                }
            });
        },
        //获取房间信息
        getRoom(room_id) {
            this.$post_('room/room/room', {room_id: room_id}, (res) => {
                if(res.code==='0') {
                    this.type_name = res.data.type_name;
                    this.form.price = res.data.price * this.form.discount;
                }
            });
        },
        //保存数据
        saveData() {
            this.disableSubmit = true;
            if (this.form.in_time !== '' && this.form.out_time !== '') {
                this.form.in_time = Date.parse(new Date(this.form.in_time)) / 1000;
                this.form.out_time = Date.parse(new Date(this.form.out_time)) / 1000;
            }
            this.$post_('check/reserve/reserve_edit', this.form, (res) => {
                if (res.code === '0') {
                    this.$emit('callbackReserve');
                    this.dialogVisible=false;
                } else {
                    this.form.in_time = formatDate(this.form.in_time*1000);
                    this.form.out_time = formatDate(this.form.out_time*1000);
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
