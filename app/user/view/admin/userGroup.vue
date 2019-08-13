<style>
	
</style>
<template>
<div>
	
<header class="mipcms-container-header">
    <div class="float-left header-group">
        <h4 class="title">分组</h4> <h5 class="sub-title">分组管理</h5>
    </div>
    <div class="float-right">
         <button type="button" class="pull-right ivu-btn ivu-btn-primary ivu-btn-circle" @click='itemAdd'>
            <span><Icon type="plus-round"></Icon> 添加分组</span>
        </button>
    </div>
</header>

<main class="mipcms-container-body" style="height: calc(100% - 50px)">
        <section class="mip-box">
            <section class="mip-box-body">
                <!--内容列表-->
                <section class="diy-table-list" v-cloak>
                    <div class="diy-table-item diy-table-item-header">
                        <ul class="list-unstyled row">
                            <li class="col-md-2">
                                <span>ID</span>
                            </li>
                            <li class="col-md-2">
                                <span>名称</span>
                            </li>
                            <li class="col-md-2">
                                <span>操作</span>
                            </li>
                        </ul>
                    </div>
                    <div v-if='itemList.length' class="diy-table-body">
                        <div class="diy-table-item" v-for='item in itemList'>
                             <ul class="list-unstyled row">
                                <li class="col-md-2">{{item.group_id}}</li>
                                <li class="col-md-2">
                                    <span>{{item.name}}</span>
                                </li>
                                <li class="col-md-2">
                                    <a @click='itemDel(item)'>删除</a>
                                    <a @click='itemEdit(item)' class="ml-1">修改</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="no-block" v-else>
                        <Icon type="ios-filing-outline"></Icon>
                        <p>暂无数据</p>
                    </div>
                </section>
                <!--内容列表结束-->
            </section>
        </section>
    </main>
	<Modal :title="dialogItemTitle" size="small" v-model='dialogItemStatus' v-cloak>
        <i-form :model="item" ref="item" :label-width="80" >
            <Form-Item label="分组名称" prop="name">
                <i-input v-model="item.name" placeholder="例：VIP会员"></i-input>
            </Form-Item>
        </i-form>
        <div slot="footer" class="dialog-footer">
            <i-button @click="dialogItemStatus = false">取 消</i-button>
            <i-button type="primary" @click="itemPost('item')">确 定</i-button>
        </div>
    </Modal>
</div>
</template>

<script>
export default {
    data () {
        return {
         	itemList: [], 
            item: {
                id: '',
                name: '',
            },
            dialogItemTitle: '添加分组',
            dialogItemStatus: false,
        }
    },
    watch: {
    },
    mounted() {
            this.getItemList();
    },
    methods: {
        itemAdd() {
            this.dialogItemTitle = '添加分组';
            this.item.name = '';
            this.dialogItemStatus = true;
        },

        itemDel(item) {
            this.$Modal.confirm({
                title: '消息提示',
                content: '<p>确定删除么？删除后不可恢复</p>',
                onOk: () => {
                    this.$mip.ajax('{$domain}/user/ApiAdminUserGroup/itemDel', {
                        id: item.group_id,
                    }).then(res => {
                        if(res.code == 1) {
                            this.$Message.success('删除成功');
                            this.getItemList();
                        }
                    });
                },
                onCancel: () => {
                }
            });

        },
        itemEdit(item) {
            this.item.id = item.group_id;
            this.dialogItemTitle = '修改分组';
            this.item.name = item.name;
            this.dialogItemStatus = true;
        },

        itemPost(val) {
            if (this.item.id) {
                this.$mip.ajax('{$domain}/user/ApiAdminUserGroup/itemEdit', {
                    id: this.item.id,
                    name: this.item.name,
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('修改成功');
                        this.getItemList();
                        this.dialogItemStatus = false;
                    }
                });

            } else {
                this.$mip.ajax('{$domain}/user/ApiAdminUserGroup/itemAdd', {
                    name: this.item.name,
                    url_name: this.item.url_name,
                    description: this.item.description,
                    cid: this.item.cid,
                }).then(res =>{
                    if(res.code == 1) {
                        this.$Message.success('添加成功');
                        this.getItemList();
                        this.dialogItemStatus = false;
                    }
                });
            }
        },

        getItemList() {
            this.$mip.ajax('{$domain}/user/ApiAdminUserGroup/itemList', {
            }).then(res => {
                if(res.code == 1) {
                    this.itemList = res.data;
                }
            });
        },

    }
}
</script>
