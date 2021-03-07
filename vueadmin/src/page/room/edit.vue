<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>房间管理</el-breadcrumb-item>
                <el-breadcrumb-item>房间列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <div class="container">
            <el-tabs v-model="roomName" type="card">
                <el-tab-pane label="房间修改" name="room">
                    <roomRdit v-if="roomName=='room'"></roomRdit>
                </el-tab-pane>
                <el-tab-pane label="配置修改" name="basics">
                    <basicsEdit v-if="roomName=='basics'"></basicsEdit>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script type="text/javascript">
import roomRdit from '@/page/room/room_edit';
import basicsEdit from '@/page/room/basics_edit';
export default{
    components:{roomRdit,basicsEdit},
    data() {
        return {
            roomName: 'room',
        }
    },
    created() {
    },
    methods: {
    }
}
</script>

<style>
</style>
