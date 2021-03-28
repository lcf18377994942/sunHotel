<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>房间管理</el-breadcrumb-item>
                <el-breadcrumb-item>房间列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

	 	<div class="container">

			<div class="search">
                <el-form label-width="90px">
                    <el-row :gutter=24>
                        <el-col :span="5">
                            <el-form-item label="房间名称：">
                                <el-input v-model="search.room_name" placeholder="房间名称" clearable></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="5">
                            <el-form-item label="房间类型：">
                                <el-select v-model="search.type_id" clearable filterable placeholder="选择房间类型">
                                    <el-option
                                        v-for="item in options"
                                        :key="item.type_id"
                                        :label="item.type_name"
                                        :value="item.type_id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="5">
                            <el-form-item label="房间状态：">
                                <el-select v-model="search.state_id" clearable filterable placeholder="选择房间状态">
                                    <el-option
                                        v-for="item in sOptions"
                                        :key="item.state_id"
                                        :label="item.state_name"
                                        :value="item.state_id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-button :loading="ifload" type="primary" @click="searchRes" icon="el-icon-search">搜索</el-button>
                        <el-button :loading="ifload" type="danger" @click="exportExecl" icon="el-icon-download">导出</el-button>
                    </el-row>
                    <el-row style="margin-top: 20px;">
                        <el-col>
                            <el-button type="success" icon="el-icon-plus" @click="addRoom" >添加房间</el-button>
                            <el-button type="success" icon="el-icon-plus" @click="addBasics" >添加配置</el-button>
                            <el-button type="success" icon="el-icon-plus" @click="editBasics" >修改配置</el-button>
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
					prop="room_id"
					label="房间ID"
                    align="center">
				</el-table-column>
				<el-table-column
					prop="room_name"
                    align="center"
					label="房间名称">
				</el-table-column>
                <el-table-column
                    prop="type_name"
                    align="center"
                    label="类型名称">
                </el-table-column>
                <el-table-column
                    prop="price"
                    align="center"
                    label="价格">
                </el-table-column>
                <el-table-column
                    prop="state_name"
                    align="center"
                    label="状态名称">
                </el-table-column>
				<el-table-column
					prop="floor_number"
                    align="center"
					label="楼层">
				</el-table-column>
                <el-table-column
                    show-overflow-tooltip
                    prop="mark"
                    align="center"
                    label="备注"
                    width="120">
                </el-table-column>
				<el-table-column
					prop="create_time"
                    align="center"
					label="创建时间">
					<template slot-scope="scope">
						<span>{{scope.row.create_time*1000 | formatDate}}</span>
					</template>
				</el-table-column>
                <el-table-column
                    prop="update_time"
                    align="center"
                    label="修改时间">
                    <template slot-scope="scope">
                        <span>{{scope.row.update_time*1000 | formatDate}}</span>
                    </template>
                </el-table-column>
				<el-table-column
                    align="center"
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-document" @click="handleEidt(scope.row.room_id)">
						修改
						</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDel(scope.$index, scope.row)">
						删除
						</el-button>
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

        <!-- 删除提示框 -->
        <el-dialog title="删除提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">删除不可恢复，是否确定删除？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="delVisible = false">取 消</el-button>
                <el-button :loading="ifload" type="primary" @click="delData">确 定</el-button>
            </span>
        </el-dialog>
        <!-- 添加房间弹出框-->
        <room_add ref="room_add" @callbackRoom="callbackRoom"></room_add>
        <!-- 房间修改弹出框-->
        <room_edit ref="room_edit" @callbackRoom="callbackRoom"></room_edit>
        <!-- 房间配置添加弹出框-->
        <basics_add ref="basics_add" @callbackRoom="callbackRoom"></basics_add>
        <!-- 房间配置修改弹出框-->
        <basics_edit ref="basics_edit" @callbackRoom="callbackRoom"></basics_edit>

 	</div>
</template>

<script type="text/javascript">
import {download} from '@/components/js/request';
import room_add from "./room_add";
import room_edit from "./room_edit";
import basics_add from "./basics_add";
import basics_edit from "./basics_edit";
export default{
    components:{
        'room_add':room_add,
        'room_edit':room_edit,
        'basics_add':basics_add,
        'basics_edit':basics_edit,
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

			search:{
				room_name:'',
                type_id:'',
                state_id:'',
                date:'',
			},
            options:[],
            sOptions:[],
		}
	},
	created() {
		this.$nextTick(()=>{
			this.getData();
            this.getListAll();
		})
	},
	methods:{
		getData() {
			let params = {
				page:this.page,
				page_size:this.page_size,
				search:JSON.stringify(this.search),
			}
			this.ifload = true;
			this.$post_('room/room/room_list',params,(res) => {
				console.log(res);
				if(res.code==='0'){
                    this.list = res.data;
					this.pages = Number(res.extend.pages);
					this.ifload = false;
				}
			});
		},
        //获取下拉数据
        getListAll() {
            this.$post_('room/room/get-list-all', {},(res) => {
                if (res.code === '0') {
                    this.options = res.data.room_type;
                    this.sOptions = res.data.room_state;
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
		handleEidt(room_id) {
		    this.$refs.room_edit.open(room_id);
		},
        //新增
        addRoom() {
		    this.$refs.room_add.open();
        },
        //新增房间配置
        addBasics() {
            this.$refs.basics_add.open();
        },
        //修改房间配置
        editBasics() {
            this.$refs.basics_edit.open();
        },

        //删除确认
		handleDel(index,row) {
			this.delVisible = true;
			this.curId = row.room_id;
			this.curIndex = index;
		},
		//删除
		delData() {
			this.ifload = true;
			let param = {room_id:this.curId};
			this.$post_('room/room/room_del',param,(res) => {
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
			this.$post_('room/room/export',params,(res) => {
				console.log(res);
				if(res.code==='0'){
					download(res.data.url);
				}
                this.ifload = false;
			})
		},
        //执行房间添加回调方法
        callbackRoom() {
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
