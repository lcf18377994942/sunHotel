<template>
    <div>
	 	<div class="container" style="border: none;">

			<div class="search">
				<el-row type="flex" :gutter="4">
					<el-col :span="3">
						<el-input v-model="search.member_id" placeholder="会员ID" clearable></el-input>
					</el-col>
					<el-col :span="4">
						<el-select v-model="search.balance_type" placeholder="请选择账本类型">
                            <el-option
                                v-for="(name,type) in typeName"
                                :key="type"
                                :value="type"
                                :label="name">
                            </el-option>
                        </el-select>
					</el-col>
					<el-col :span="4" :offset='12'>
						<el-button :loading="ifload" type="primary" @click="searchRes" icon="el-icon-search">搜索</el-button>
					</el-col>
				</el-row>
			</div>

			<el-table
				:data="list"
				border
				v-loading="ifload"
				>

				<el-table-column
					prop="member_id"
					label="会员ID"
                    align="center"
					width="80">
				</el-table-column>
				<el-table-column
					prop="member_name"
                    align="center"
					label="会员姓名"
					width="80">
				</el-table-column>
				<el-table-column
					prop="member_mobile"
                    align="center"
					label="会员电话"
					width="120">
				</el-table-column>
				<el-table-column
					prop="balance"
                    align="center"
					label="数量"
                    sortable
					width="100">
				</el-table-column>
				<el-table-column
					prop="old_balance"
                    align="center"
					label="操作前数量"
					width="100">
				</el-table-column>
				<el-table-column
					prop="log_type"
                    align="center"
					label="操作类型"
					width="100">
					<template slot-scope="scope">
						<span>{{scope.row.log_type=='1'?'增加':'减少'}}</span>
					</template>
				</el-table-column>
				<el-table-column
					prop="type_name"
                    align="center"
					label="账本类型"
					width="100">
					<template slot-scope="scope">
						<span>{{typeName[scope.row.balance_type]}}</span>
					</template>
				</el-table-column>
				<el-table-column
					prop="bonus_from"
                    align="center"
					label="来源记录"
					width="140">
				</el-table-column>
				<el-table-column
					prop="create_time"
                    align="center"
					label="操作时间">
					<template slot-scope="scope">
						<span>{{scope.row.create_time*1000 | formatDate}}</span>
					</template>
				</el-table-column>
			</el-table>

            <div class="pagination">
                <el-pagination background
					@current-change="handleCurrentChange"
					layout="prev, pager, next"
					:total="pages"
					:page-size="page_size">
                </el-pagination>
            </div>
		</div>

 	</div>
</template>

<script type="text/javascript">
import {download} from '@/components/js/request'
export default{
	data() {
		return {
			ifload:true,
            avatarDefault:require("../../assets/avatar_default.png" ),
			list:[],
            typeName:[],

			page:1,
			page_size:10,
			pages:0,

			search:{
				member_id:'',
				balance_type:'',
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
			this.$post_('finance/balance/balance_log',params,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.list = res.data;
					this.pages = Number(res.extend.pages);
                    this.typeName = res.extend.type_name;
					this.ifload = false;
				}
			});
		},
        // 分页导航
        handleCurrentChange(val) {
            this.page = val;
            this.getData();
        },
        //余额明细
        balance_log() {
            this.$router.push({path:'/page/member/member_add'});
        },
		//搜索
		searchRes() {
			this.page = 1;
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
