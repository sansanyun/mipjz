<style>
	
</style>
<template>
<div>
    <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">插件</h4> <h5 class="sub-title">全部</h5>
        </div>
        <button type="button" class="float-right ivu-btn ivu-btn-primary ivu-btn-circle" @click='itemSave'>
            <span><Icon type="checkmark-round"></Icon> 保存设置</span>
        </button>
    </header>
    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
            <section class="mip-box">
                <section class="mip-box-body">
                    <!--内容列表-->
                    <section class="diy-table-list" v-cloak>
                
                        <div class="diy-table-item diy-table-item-header">
                            <ul class="list-unstyled row">
                                <li class="col-md-2">
                                    <span>名称</span>
                                </li>
                                <li class="col-md-1">
                                    <span>标识</span>
                                </li>
                                <li class="col-md-3">
                                    <span>描述</span>
                                </li>
                                <li class="col-md-1">
                                    <span>版本</span>
                                </li>
                                
                                <li class="col-md-1">
                                    菜单 <Tooltip content="将含有管理项的插件，添加至左侧菜单显示" placement="top"><Icon type="ios-help-outline"></Icon></Tooltip>
                                </li>
                                
                                <li class="col-md-1">
                                    <span>导航  <Tooltip content="将含有管理项的插件，添加至顶部导航显示" placement="top"><Icon type="ios-help-outline"></Icon></Tooltip>
                                </li>
                                
                                <li class="col-md-1">
                                    <span>状态</span>
                                </li>
                                <li class="col-md-2">
                                    <span>操作</span>
                                </li>
                            </ul>
                        </div>
                        <div v-if='itemList.length' class="diy-table-body">
                            <div class="diy-table-item" v-for='item in itemList' >
                                 <ul class="list-unstyled row">
                                    <li class="col-md-2 over-h-e">
                                        <span>{{item.title}}</span>
                                    </li>
                                    <li class="col-md-1">
                                        <span>{{item.name}}</span>
                                    </li>
                                    <li class="col-md-3 over-h-e">
                                        <poptip style='width: 100px;' trigger="hover" transfer :content="item.description">
                                            <span>{{item.description}}</span>
                                        </poptip>
                                    </li>
                                    <li class="col-md-1">
                                        <span>{{item.version}}</span>
                                    </li>
                                        
                                    <li class="col-md-1">
                                         <i-switch size="small" v-if='item.admin_url' v-model='item.side_status' @on-change="changeMenu">
                                            <span slot="open"></span>
                                            <span slot="close"></span>
                                        </i-switch>
                                    </li>
                                    
                                    <li class="col-md-1">
                                         <i-switch size="small" v-if='item.admin_url' v-model='item.header_status' @on-change="changeMenu">
                                            <span slot="open"></span>
                                            <span slot="close"></span>
                                        </i-switch>
                                    </li>
                                    
                                    <li class="col-md-1">
                                        <span v-if='item.status == 1' style='color: #2796ea;font-weight: 700;'>已启用</span>
                                        <span v-if='item.status == 0'>未启用</span>
                                        <span v-if='item.status == null'>未安装</span>
                                    </li>
                                    <li class="col-md-2" v-if='item.uninstall == 0'>
                                        <i-button  v-if='item.status == 0' type="text" size="small" @click='enable(item)'>启用</i-button>
                                        <i-button v-if='item.status == 1' type="text" size="small" @click='disable(item)'>禁用</i-button>
                                        <i-button  type="text" size="small" @click='uninstall(item)'>卸载</i-button>
                                        <i-button  v-if='item.admin_url' type="text" size="small" @click='manage(item)'>管理</i-button>
                                    </li>
                                    <li class="col-md-2" v-else>
                                        <i-button v-if='item.status == null' type="text" size="small" @click='install(item)'>安装</i-button>
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
    

</div>
</template>

<script>
    export default {
     data () {
       return {
            
            itemList: [], 
       }
     },
     watch: {
            
        },
        mounted() {
            this.getItemList();
        },
        methods: {
            changeMenu() {
                this.itemSave();
                location.href = location.href;
            },
            manage(item) {
            	if (this.$route.params.dataId) {
            		this.$router.push({
	                    name: item.name, params: { mod:'addons',dataId: this.$route.params.dataId}
	                });
            	} else {
	                this.$router.push({
	                    name: item.name, params: { mod:'addons'}
	                });
            	}
            },
            itemSave() {
                this.$mip.ajax('{$domain}/addons/ApiAdminAddons/save', {
                    itemList: JSON.stringify(this.itemList),
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('操作成功');
                        location.reload();
                    }
                });
            },
            enable(item) {
                this.$mip.ajax('{$domain}/addons/ApiAdminAddons/enable', {
                    id: item.id,
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('操作成功');
                        location.reload();
                        this.getItemList();
                    }
                });
            },
            
            disable(item) {
                this.$mip.ajax('{$domain}/addons/ApiAdminAddons/disable', {
                    id: item.id,
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('操作成功');
                        location.reload();
                        this.getItemList();
                    }
                });
            },
            
            install(item) {
                this.$mip.ajax('{$domain}/addons/ApiAdminAddons/install', {
                    name: item.name,
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('操作成功');
                        this.getItemList();
                    }
                });
            },
            
            uninstall(item) {
                this.$Modal.confirm({
                    title: '消息提示',
                    content: '<p>确定卸载么？卸载后数据表将会被删除</p>',
                    onOk: () => {
                        this.$mip.ajax('{$domain}/addons/ApiAdminAddons/uninstall', {
                            id: item.id,
                        }).then(res =>{
                            if(res.code == 1) {
                                this.$Message.success('卸载成功');
                                this.getItemList();
                            }
                        });
                    },
                    onCancel: () => {
                    }
                });
            },
              
            getItemList() {
                this.loading = true;
                this.$mip.ajax('{$domain}/addons/ApiAdminAddons/itemList', {
                }).then(res => {
                    this.itemList = [];
                    if(res.code == 1) {
                        var itemList = res.data.itemList;
                        for (var i = 0; i < itemList.length; i++) {
                        	   itemList[i].side_status = itemList[i].side_status ? true : false;
                            itemList[i].header_status = itemList[i].header_status ? true : false;
                        }
                        this.itemList = itemList;
                    }
                    this.loading = false;
                });
            },
            
            
        }
    }
</script>
