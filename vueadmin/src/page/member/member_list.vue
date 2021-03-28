<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>会员管理</el-breadcrumb-item>
                <el-breadcrumb-item>会员列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

	 	<div class="container">

			<div class="search">
                <el-form label-width="90px">
                    <el-row :gutter="gutter">
                        <el-col :span="4">
                            <el-form-item label="会员ID：">
                                <el-input v-model="search.member_id" placeholder="会员ID" clearable></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="4">
                            <el-form-item label="会员姓名：">
                                <el-input v-model="search.member_name" placeholder="会员姓名" clearable></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="4">
                            <el-form-item label="会员电话：">
                                <el-input v-model="search.member_mobile" placeholder="会员电话" clearable></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="注册时间：">
                                <el-date-picker
                                    v-model="search.date"
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
                    <el-row style="margin-top: 10px;">
                        <el-col :span="2">
                            <el-button type="success" icon="el-icon-plus" @click="addMember" >添加会员</el-button>
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
					prop="member_avatar"
					label="头像"
                    align="center">
					<template slot-scope="scope">
						<img :src="scope.row.member_avatar?scope.row.member_avatar:avatarDefault" style="width:48px;border-radius: 50%;"/>
                    </template>
				</el-table-column>

				<el-table-column
					prop="member_id"
					label="会员ID"
                    align="center">
				</el-table-column>
				<el-table-column
					prop="member_name"
                    align="center"
					label="会员姓名">
				</el-table-column>
                <el-table-column
                    prop="sex"
                    align="center"
                    label="性别">
                    <template slot-scope="scope">
                        <span>{{scope.row.sex==='1'?'男':'女'}}</span>
                    </template>
                </el-table-column>
                <el-table-column
                    prop="member_card_id"
                    align="center"
                    label="身份证号">
                </el-table-column>
				<el-table-column
					prop="member_mobile"
                    align="center"
					label="会员电话">
				</el-table-column>
                <el-table-column
                    prop="charge"
                    align="center"
                    label="消费">
                </el-table-column>
                <el-table-column
                    prop="member_type_name"
                    align="center"
                    label="会员等级"
                    width="120">
                </el-table-column>
				<el-table-column
					prop="state_id"
                    align="center"
					label="状态">
				</el-table-column>
				<el-table-column
					prop="create_time"
                    align="center"
					label="注册时间">
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
                        <el-button type="text" icon="el-icon-document" @click="handleEidt(scope.row.member_id)">
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
        <!-- 添加会员弹出框-->
        <member_add ref="member_add" @callbackMember="callbackMember"></member_add>
        <!-- 修改会员弹出框-->
        <member_edit ref="member_edit" @callbackMember="callbackMember"></member_edit>

 	</div>
</template>

<script type="text/javascript">
import {download} from '@/components/js/request'
import member_add from "./member_add";
import member_edit from "./member_edit";
export default{
    components:{
        'member_add': member_add,
        'member_edit': member_edit,
    },
	data() {
		return {
			ifload:true,
            avatarDefault:require("../../assets/avatar_default.png" ),
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
				member_id:'',
				member_name:'',
				member_mobile:'',
                date:'',
			}
		}
	},
	created() {
		this.$nextTick(()=>{
			this.getData();
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
			this.$post_('member/member/member_list',params,(res) => {
				console.log(res);
				if(res.code==='0'){
					this.list = res.data;
					this.pages = Number(res.extend.pages);
					this.ifload = false;
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
		handleEidt(member_id) {
            this.$refs.member_edit.open(member_id);
		},
        //新增
        addMember() {
            this.$refs.member_add.open();
        },

        //删除确认
		handleDel(index,row) {
			this.delVisible = true;
			this.curId = row.member_id;
			this.curIndex = index;
		},
		//删除
		delData() {
			this.ifload = true;
			let param = {member_id:this.curId};
			this.$post_('member/member/member_del',param,(res) => {
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
			this.$post_('member/member/export',params,(res) => {
				console.log(res);
				if(res.code==='0'){
					download(res.data.url);
				}
                this.ifload = false;
			})
		},
        callbackMember() {
		    this.getData();
        }

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
