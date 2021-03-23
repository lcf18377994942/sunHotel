<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>预订管理</el-breadcrumb-item>
                <el-breadcrumb-item>预订列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

	 	<div class="container">

			<div class="search">
				<el-row type="flex"  justify="space-between">
                    <el-col :span="3">
                        <el-input v-model="search.member_name" placeholder="会员名称" clearable></el-input>
                    </el-col>
					<el-col :span="3">
                        <el-input v-model="search.room_name" placeholder="房间名称" clearable></el-input>
					</el-col>
                    <el-col :span="3">
                        <el-select v-model="search.type_id" clearable filterable placeholder="选择房间类型">
                            <el-option
                                v-for="item in options"
                                :key="item.type_id"
                                :label="item.type_name"
                                :value="item.type_id">
                            </el-option>
                        </el-select>
                    </el-col>
					<el-col :span="4">
						<el-date-picker
						  v-model="search.inDate"
						  type="daterange"
						  format="yyyy-MM-dd"
						  range-separator="至"
						  start-placeholder="房间预订开始日期"
						  end-placeholder="房间预订结束日期">
						</el-date-picker>
					</el-col>
                    <el-col :span="6">
                        <el-date-picker
                            v-model="search.outDate"
                            type="daterange"
                            format="yyyy-MM-dd"
                            range-separator="至"
                            start-placeholder="房间预订开始日期"
                            end-placeholder="房间预订结束日期">
                        </el-date-picker>
                    </el-col>
				</el-row>
				<el-row style="margin-top: 10px;">
                    <el-col :span="2">
                        <el-button type="success" icon="el-icon-plus" @click="addReserve" >预订添加</el-button>
                    </el-col>
					<el-col :span="4" :offset='18'>
						<el-button :loading="ifload" type="primary" @click="searchRes" icon="el-icon-search">搜索</el-button>
						<el-button :loading="ifload" type="danger" @click="exportExecl" icon="el-icon-download">导出</el-button>
					</el-col>
				</el-row>
			</div>

			<el-table
				:data="list"
				border
				v-loading="ifload"
				>
				<el-table-column
					prop="reserve_id"
					label="预订ID"
                    align="center">
				</el-table-column>
				<el-table-column
					prop="member_name"
                    align="center"
					label="会员名称">
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
					prop="in_time"
                    align="center"
					label="预订时间">
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
                    prop="mark"
                    align="center"
                    label="备注"
                    width="120">
                </el-table-column>
				<el-table-column
                    align="center"
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-office-building" @click="handleAdd(scope.$index,scope.row.reserve_id)">入住</el-button>
                        <el-button type="text" icon="el-icon-document" @click="handleEidt(scope.row.reserve_id)">修改</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDel(scope.$index, scope.row)">退订</el-button>
                    </template>
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

        <!-- 退订提示框 -->
        <el-dialog title="预订提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">退订不可恢复，是否确定退订？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="delVisible = false">取 消</el-button>
                <el-button :loading="ifload" type="primary" @click="delData">确 定</el-button>
            </span>
        </el-dialog>
        <!-- 入住提示框 -->
        <el-dialog title="入住提示" :visible.sync="addVisible" width="300px" center>
            <div class="del-dialog-cnt">入住不可恢复，是否确定入住？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="addVisible = false">取 消</el-button>
                <el-button :loading="ifload" type="primary" @click="addData">确 定</el-button>
            </span>
        </el-dialog>
        <reserve_add ref="reserve_add" @callbackReserve="callbackReserve"></reserve_add>
        <reserve_edit ref="reserve_edit" @callbackReserve="callbackReserve"></reserve_edit>
 	</div>
</template>

<script type="text/javascript">
import {download} from '@/components/js/request'
import reserve_add from "./reserve_add";
import reserve_edit from "./reserve_edit";
export default{
    components:{
        'reserve_add':reserve_add,
        'reserve_edit':reserve_edit,
    },
	data() {
		return {
			ifload:true,
			list:[],

			page:1,
			page_size:10,
			pages:0,

            //当前操作对象
			curId:0,
			curIndex:-1,
			delVisible:false,
            eidtVisible:false,
            addVisible:false,

			search:{
				room_name:'',
                member_name:'',
                type_id:'',
                inDate:'',
                outDate:'',
			},
            options:[],
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
			this.$post_('room/reserve/reserve-list',params,(res) => {
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
                this.options = res.data.room_type;
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
		handleEidt(reserve_id) {
            this.$refs.reserve_edit.open(reserve_id);
		},
        //新增
        addReserve() {
		    this.$refs.reserve_add.open()
        },
        //删除确认
		handleDel(index,row) {
			this.delVisible = true;
			this.curId = row.reserve_id;
			this.curIndex = index;
		},
		//删除
		delData() {
			this.ifload = true;
			let param = {reserve_id:this.curId};
			this.$post_('room/reserve/reserve_del',param,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.list.splice(this.curIndex,1);
					this.ifload = false;
					this.delVisible = false;
				}else{
					this.$message.error(res.msg);
				}
			})
		},
        //删除确认
        handleAdd(index,id) {
            this.addVisible = true;
            this.curId = id;
            this.curIndex = index;
        },
        //删除
        addData() {
            this.ifload = true;
            let param = {reserve_id:this.curId};
            this.$post_('room/reserve/check_in_add',param,(res) => {
                if(res.code=='0'){
                    this.$message.success(res.msg);
                    this.list.splice(this.curIndex,1);
                    this.ifload = false;
                    this.addVisible = false;
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
			this.$post_('room/reserve/export',params,(res) => {
				console.log(res);
				if(res.code=='0'){
					download(res.data.url);
				}
                this.ifload = false;
			})
		},
        callbackReserve(){
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
