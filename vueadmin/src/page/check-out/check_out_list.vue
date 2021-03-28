<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>退房管理</el-breadcrumb-item>
                <el-breadcrumb-item>退房列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

	 	<div class="container">

			<div class="search">
                <el-form label-width="90px">
                    <el-row :gutter=24>
                        <el-col :span="6">
                            <el-form-item label="会员名称：">
                                <el-input v-model="search.member_name" placeholder="会员名称" clearable></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="房间名称：">
                                <el-input v-model="search.room_name" placeholder="房间名称" clearable></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="房间类型：">
                                <el-select style="width: 100%" v-model="search.type_id" clearable filterable placeholder="选择房间类型">
                                    <el-option
                                        v-for="item in options"
                                        :key="item.type_id"
                                        :label="item.type_name"
                                        :value="item.type_id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row style="margin-top: 10px;" :gutter=24>
                        <el-col :span="7">
                            <el-form-item label="入住时间：">
                                <el-date-picker
                                    v-model="search.inDate"
                                    type="datetimerange"
                                    align="right"
                                    start-placeholder="开始日期"
                                    end-placeholder="结束日期"
                                    :default-time="['00:00:00', '23:59:59']">
                                </el-date-picker>
                            </el-form-item>
                        </el-col>
                        <el-col :span="15" style="margin-left: 76px;">
                            <el-form-item label="退房日期：">
                                <el-date-picker
                                    v-model="search.outDate"
                                    type="datetimerange"
                                    align="right"
                                    start-placeholder="开始日期"
                                    end-placeholder="结束日期"
                                    :default-time="['00:00:00', '23:59:59']">
                                </el-date-picker>
                                <el-button :loading="ifload" type="primary" @click="searchRes" icon="el-icon-search">搜索</el-button>
                                <el-button :loading="ifload" type="danger" @click="exportExecl" icon="el-icon-download">导出</el-button>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-form>
			</div>

			<el-table
				:data="list"
				border
				v-loading="ifload"
				>
				<el-table-column
					prop="check_out_id"
					label="退房ID"
                    align="center">
				</el-table-column>
                <el-table-column
                    prop="user_name"
                    align="center"
                    label="操作人名称">
                    <template slot-scope="scope">
                        <span>{{scope.row.user_name===null?'无':scope.row.user_name}}</span>
                    </template>
                </el-table-column>
				<el-table-column
					prop="member_name"
                    align="center"
					label="会员名称">
				</el-table-column>
                <el-table-column
                    prop="second_member_id"
                    align="center"
                    label="次会员编号">
                    <template slot-scope="scope">
                        <span>{{scope.row.second_member_name===null?'无':scope.row.second_member_name}}</span>
                    </template>
                </el-table-column>
                <el-table-column
                    prop="room_name"
                    align="center"
                    label="房间名称">
                </el-table-column>
                <el-table-column
                    prop="type_name"
                    align="center"
                    label="房间类型">
                </el-table-column>
                <el-table-column
                    prop="deposit"
                    align="center"
                    label="押金">
                </el-table-column>
                <el-table-column
                    prop="charge"
                    align="center"
                    label="消费金额">
                </el-table-column>
				<el-table-column
					prop="in_time"
                    align="center"
					label="入住时间">
					<template slot-scope="scope">
						<span>{{scope.row.in_time*1000 | formatDate}}</span>
					</template>
				</el-table-column>
                <el-table-column
                    prop="out_time"
                    align="center"
                    label="退房时间">
                    <template slot-scope="scope">
                        <span>{{scope.row.out_time*1000 | formatDate}}</span>
                    </template>
                </el-table-column>
                <el-table-column
                    show-overflow-tooltip
                    prop="mark"
                    align="center"
                    label="备注">
                </el-table-column>
			</el-table>

            <div class="pagination">
                <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page.sync="page"
                    :page-sizes="[10, 30, 50, 100]"
                    :page-size="page_size"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="pages">
                </el-pagination>
            </div>
		</div>

        <!-- 删除提示框 -->
        <el-dialog title="退房提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">退房不可恢复，是否确定退房？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="delVisible = false">取 消</el-button>
                <el-button :loading="ifload" type="primary" @click="delData">确 定</el-button>
            </span>
        </el-dialog>
<!--        <check_in_add ref="check_in_add" @callbackCheckIn="callbackCheckIn"></check_in_add>
        <check_in_edit ref="check_in_edit" @callbackCheckIn="callbackCheckIn"></check_in_edit>-->

 	</div>
</template>

<script type="text/javascript">
import {download} from '@/components/js/request'
/*import check_in_add from "./check_in_add";
import check_in_edit from "./check_in_edit";*/
export default{
    components:{
        /*'check_in_add':check_in_add,
        'check_in_edit':check_in_edit*/
    },
	data() {
		return {
			ifload:true,
			list:[],
            gutter:24,

			page:1,
			page_size:10,
			pages:0,

            //当前操作对象
			curId:0,
			curIndex:-1,
			delVisible:false,
            eidtVisible:false,

			search:{
				room_name:'',
                member_name:'',
                type_id:'',
                inDate:'',
                outDate:'',
			},
            options:[],
            pickerOptions: {
                shortcuts: [{
                    text: '最近一周',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        end.setTime(end.getTime() + 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近一个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        end.setTime(end.getTime() + 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近三个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        end.setTime(end.getTime() + 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }]
            },
		}
	},
	created() {
		this.$nextTick(()=>{
			this.getData();
			this.getListAll();
		})
	},
	computed:{
	},
	methods:{
		getData() {
			let params = {
				page:this.page,
				page_size:this.page_size,
				search:JSON.stringify(this.search),
			}
			this.ifload = true;
			this.$post_('check/check-out/check-out-list',params,(res) => {
				console.log(res);
				if(res.code == '0'){
                    this.list = res.data;
					this.pages = Number(res.extend.pages);
					this.ifload = false;
				}
			});
		},
        //获取下拉数据
        getListAll() {
            this.$post_('room/room/get-list-all', {},(res) => {
                if (res.code ==='0') {
                    this.options = res.data.room_type;
                }
            });
        },
        // 分页条数
        handleSizeChange(val){
            this.page_size = val;
            this.getData();
        },
        // 分页导航
        handleCurrentChange(val) {
            this.page = val;
            this.getData();
        },
        //修改
		handleEidt(row) {
            this.$refs.check_in_edit.open(row.check_in_id);
		},
        //删除确认
		handleDel(index,row) {
			this.delVisible = true;
			this.curId = row.check_in_id;
			this.curIndex = index;
		},
		//删除
		delData() {
			this.ifload = true;
			let param = {check_in_id:this.curId};
			this.$post_('check/check-out/check_out_del',param,(res) => {
				console.log(res);
				if(res.code==='0'){
					this.$message.success(res.msg);
					this.list.splice(this.curIndex,1);
					this.ifload = false;
					this.delVisible = false;
				}else{
					this.$message.error(res.msg);
				}
			})
		},
		//搜索
		searchRes() {
			this.page = 1;
			this.getData();
		},
		//导出
		exportExecl() {
            this.ifload = true;
			let params = {
				page:this.page,
				page_size:1000,
				search:JSON.stringify(this.search),
			}
			this.$post_('check/check-out/export',params,(res) => {
				console.log(res);
				if(res.code==='0'){
					download(res.data.url);
				}
                this.ifload = false;
			})
		},
        callbackCheckIn(){
            this.getData();
        },
	}
}
</script>

<style type="text/css">
    thead tr th{
        text-align: center;
    }
    .red{
        color: #ff0000;
    }
	.search{
		margin-bottom: 10px;
	}
</style>
