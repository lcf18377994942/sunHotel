<template>
    <el-dialog
        :before-close="handleClose"
        :visible.sync="dialogVisible"
        title="预订登记"
        width="40%">
        <el-form ref="form" :model="form" label-width="100px">
            <el-row :gutter=24>
                <el-col :span="12">
                    <el-form-item label="会员名称" >
                        <el-select style="width: 100%" v-model="form.member_id" clearable filterable placeholder="请选择会员"
                                   @change="getMember">
                            <el-option
                                v-for="item in options"
                                :key="item.member_id"
                                :label="item.member_name"
                                :value="item.member_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间名称">
                        <el-input v-model="room_name" :disabled="disable"></el-input>
                    </el-form-item>
                    <el-form-item label="价格">
                        <el-input v-model="form.price" :disabled="disable" placeholder="0.00"></el-input>
                    </el-form-item>
                    <el-form-item label="押金">
                        <el-input v-model="form.deposit" :disabled="disable" placeholder="默认：20"></el-input>
                    </el-form-item>
                    <el-form-item label="入住时间">
                        <el-date-picker
                            v-model="form.in_time"
                            style="width: 100%"
                            :picker-options="pickerOptions"
                            align="right"
                            placeholder="选择日期时间"
                            type="datetime">
                        </el-date-picker>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="次会员名称">
                        <el-select style="width: 100%" v-model="form.second_member_id" :disabled="disSelect" clearable filterable placeholder="请选择会员">
                            <el-option
                                v-for="item in smOptions"
                                :key="item.member_id"
                                :label="item.member_name"
                                :value="item.member_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间类型" >
                        <el-input v-model="type_name" :disabled="disable" ></el-input>
                    </el-form-item>
                    <el-form-item label="折扣比例">
                        <el-input v-model="form.discount" :disabled="disable" placeholder="0.00"></el-input>
                    </el-form-item>
                    <el-form-item label="备注">
                        <el-input v-model="form.mark" placeholder=""></el-input>
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
            disableSubmit: true,
            disSelect: true,
            form: {
                member_id: '',
                second_member_id:'',
                room_id: '',
                price: 0.00,
                discount: 1,
                deposit: '',
                mark:'',
                in_time: new Date(),
                out_time: new Date().setTime(new Date(new Date().toLocaleDateString()).getTime() + 3600 * 1000 * 36),
            },
            room_name:'',
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
            smOptions: []
        }
    },
    methods: {
        open(room_id){
            this.form.room_id = room_id;
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
            this.form.member_id= ''
            this.form.price= ''
            this.form.discount= 1.00;
            this.form.deposit= '';
            this.disSelect = true;
            this.form.in_time= new Date()
            this.form.out_time= new Date().setTime(new Date(new Date().toLocaleDateString()).getTime() + 3600 * 1000 * 36)
            this.getRoom();
        },
        //获取下拉数据
        getListAll() {
            this.$post_('check/check-in/get-list-all', {}, (res) => {
                if (res.code === '0') {
                    this.options = res.data.member;
                    this.rOptions = res.data.room;
                    this.dialogVisible=true;
                    this.disableSubmit = false;
                }
            });
        },
        //获取会员信息
        getMember(member_id) {
            this.$post_('member/member/member_info', {member_id: member_id}, (res) => {
                this.form.second_member_id = '';
                if (res.code === '0') {
                    this.form.discount = res.data.discount;
                    this.form.price = this.form.price * res.data.discount;
                    this.$post_('member/member/get-second-list', {member_id: member_id}, (res) => {
                        if (res.code === '0'){
                            this.smOptions = res.data.member;
                        }
                    });
                }
            });
        },
        //获取房间信息
        getRoom() {
            this.$post_('room/room/room', {room_id: this.form.room_id}, (res) => {
                if (res.code === '0'){
                    this.room_name = res.data.room_name;
                    this.type_name = res.data.type_name;
                    this.form.price = res.data.price * this.form.discount;
                    this.form.deposit = res.data.deposit;
                    this.getListAll();
                    if (res.data.type_id === '3' || res.data.type_id === '4') {
                        this.disSelect = false;
                    }
                }
            });
        },
        //保存数据
        saveData() {
            this.disableSubmit = true;
            if (this.form.second_member_id === '') {
                this.form.second_member_id = 0;
            }
            if (this.form.in_time !== '' && this.form.out_time !== '') {
                this.form.in_time = Date.parse(new Date(this.form.in_time)) / 1000;
                this.form.out_time = Date.parse(new Date(this.form.out_time)) / 1000;
            }
            this.$post_('check/reserve/reserve_add', this.form, (res) => {
                if (res.code === '0') {
                    this.$emit('callbackRoom');
                    this.dialogVisible=false
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
