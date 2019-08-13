<style>
	
</style>
<template>
<div>
    <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">用户</h4> <h5 class="sub-title">全部用户</h5>
        </div>
	    <div class="float-right">
	         <button type="button" class="pull-right ivu-btn ivu-btn-primary ivu-btn-circle" @click='itemAdd'>
	            <span><Icon type="plus-round"></Icon> 添加用户</span>
	        </button>
	    </div>
   	</header>
    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
    	<aside class="side-box mip-box" style="height: calc(100% - 50px - 92px);bottom: 15px;">
            <section class="side-box-body">
                <section class="side-list">
                    <ul>
                        <li class="nav-item" :class='{"active" : item.active}' v-for='item in sideCategoryList'>
                            <a @click="menuClick(item.group_id)">{{item.name}}</a>
                            <ul class="list-unstyled children" v-if='item.children'>
                                <li v-for='sub in item.children' :class='{"active" : sub.active}'><a @click="menuClick(sub.group_id)">{{sub.name}}</a></li>
                            </ul>
                        </li>
                    </ul>
                </section>
            </section>
        </aside>
        <section class="content-box">
            <section class="mip-box mip-box-min-width">
                <section class="mip-box-body">
                    <!--内容列表-->
                    <section class="diy-table-list" v-cloak>
                        <div class="content-list-header clearfix">
                             <div class="float-left m-b-xs">
                                <i-input v-model="searchData" placeholder="请输入搜索的关键词" style='width: 300px;'>
                                    <i-Button slot="append" icon="ios-search" @click='itemSearchClick'></i-Button>
                                </i-input>
                            </div>
                            <div class="float-right">
                                <button class="ivu-btn ivu-btn-text" type="text" :disabled='!itemListSelectStatus' @click="itemsDel"><i class="ivu-icon ivu-icon-ios-trash"></i> 批量删除</button>
                            </div>
                        </div>
                        <div class="diy-table-item diy-table-item-header">
                            <ul class="list-unstyled row">
                                <li class="col-md-1 check-width">
                                    <Checkbox v-model="itemListSelectStatus"  :disabled='!itemList.length' @on-change='itemListSelectChange'>全选</Checkbox>
                                </li>
                                <li class="col-md-2 uuid-width">
                                    <span>UID</span>
                                </li>
                                <li class="col-md-1 over-h-e">
                                    <span>分组</span>
                                </li>
                                <li class="col-md-2 over-h-e">
                                    <span>用户名</span>
                                </li>
                                <li class="col-md-2 time-width">
                                    <span>注册时间</span>
                                </li>
                                <li class="col-md-2 time-width">
                                    <span>登录时间</span>
                                </li>
                                <li class="col-md-2">
                                    <span>操作</span>
                                </li>
                            </ul>
                        </div>
                        <div v-if='itemList.length' class="diy-table-body">
                            <div class="diy-table-item" v-for='item in itemList' >
                                 <ul class="list-unstyled row">
                                    <li class="col-md-1 check-width">
                                        <Checkbox v-model="item.itemListSelectStatus"></Checkbox>
                                    </li>
                                    <li class="col-md-2 uuid-width">{{item.uid}}</li>
                                    <li class="col-md-1 over-h-e"><span v-if='item.groupInfo'>{{item.groupInfo.name}}</span></li>
                                    <li class="col-md-2 over-h-e">
                                        <span>{{item.username}}</span>
                                    </li>
                                    <li class="col-md-2 time-width">
                                        <span>{{item.reg_time | time}}</span>
                                    </li>
                                    <li class="col-md-2 time-width">
                                        <span v-if='item.last_login_time'>{{item.last_login_time | time}}</span><span v-else>-</span>
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
                    <!--分页-->
                    <div class="text-right clearfix mt-3">
                        <div class="diy-table-item float-left" style="margin-left: 10px;">
                            <Checkbox v-model="itemListSelectStatus" :disabled='!itemList.length' @on-change='itemListSelectChange'>全选</Checkbox>
                            <button class="ivu-btn ivu-btn-text" type="text" :disabled='!itemListSelectStatus' @click="transferClick"><Icon type="arrow-swap"></Icon> 批量转移</button>
                        </div>
                        <Page :total="pagination.total" @on-page-size-change='itemPaginationSelect' :page-size-opts='[10,100,500,1000,5000]' show-total show-sizer placement='top' @on-change='itemPaginationClick'></Page>
                    </div>
                </section>
            </section>
        </section>
    </main> 
        <Modal :title="dialogItemTitle" size="small" v-model='dialogItemStatus' v-cloak>
            <i-form :label-width="80" >
                <Form-Item label="分组" prop="title">
					<Select v-model="item.groupId" prop="groupId" style="width: 200px;">
				        <Option v-for="item in categoryList" :value="item.group_id" :key="item.group_id">{{ item.name }}</Option>
				    </Select>
                </Form-Item> 
                <Form-item label="用户名" prop="username">
                    <i-input v-model="item.username"></i-input>
                </Form-item>
                <Form-item label="管理密码" prop="password">
                    <i-input type='password' v-model="item.password"></i-input>
                </Form-item>
                <Form-item label="重复密码" prop="rpassword">
                    <i-input type='password' v-model="item.rpassword"></i-input>
                </Form-item>
            </i-form>
            <div slot="footer" class="dialog-footer">
                <i-button @click="dialogItemStatus = false">取 消</i-button>
                <i-button type="primary" @click="itemPost('item')">确 定</i-button>
            </div>
        </Modal>

         <!--转移-->
        <Modal title="批量转移" v-model="transferStatus"  v-cloak>
            <i-form :label-width="80" >
                <Form-Item label="提示">
                    <span>请选择您需要转移到的目标分类</span>
                </Form-Item>
                <Form-Item label="目标分类">
                      <Cascader :data="transferList" v-model="transferId"></Cascader>
                </Form-Item>
            </i-form>
            <span slot="footer" class="dialog-footer">
                <i-button @click="transferStatus = false">取 消</i-button>
                <i-button type="primary" @click='transferSubmit'>确 定</i-button>
            </span>
        </Modal>
    </div>
</template>

<script>
export default {
    data () {
        return {
         itemList: [], 
            itemListSelectStatus: [],
            searchData: '',
            cid: '',
            categoryList: [],
            sideCategoryList: [],
            item: {
                uid: '',
                groupId: '',
                username: '',
                password: '',
                rpassword: '',
            },
            dialogItemTitle: '添加用户',
            dialogItemStatus: false,
            //转移
            transferStatus: false,
            transferId: [],
            transferList: [],
            pagination: {
                currentPage: 1,
                limit: 10,
                total: this.total,
            },
        }
    },
    watch: {
        itemList: {
            handler(newValue, oldValue) {
                var tempStatus = false;
	        　　　　　for (var i = 0; i < this.itemList.length; i ++) {
	                    if (this.itemList[i].itemListSelectStatus == true) {
	                        tempStatus = true;
	                    }
	                }
	                if (tempStatus) {
	                    this.itemListSelectStatus = true;
	                } else {
	                    this.itemListSelectStatus = false;
	                }
			},deep: true
		},
    },
    mounted() {
	 	this.getItemList();
        this.getCategoryList();
    },
    methods: {
        //转移   
        transferClick() {
            this.transferStatus = true;
            this.transferId = [];
        },
        transferSubmit() {
            var itemIds = [];
            for(var i = 0; i < this.itemList.length; i++) {
                if (this.itemList[i].itemListSelectStatus) {
                    itemIds.push(this.itemList[i].uid);
                }
            }
            itemIds = itemIds.join(',');
            this.$mip.ajax('{$domain}/user/ApiAdmin{$itemApiUrl}/itemTransferAll', {
                cid: this.transferId[this.transferId.length-1],
                uids:itemIds,
            }).then(res => {
                if(res.code == 1) {
                    this.$Message.success('操作成功');
                    this.getItemList();
                    this.transferStatus = false;
                }
            });
        },
        itemAdd() {
            this.item.uid = '';
            this.item.groupId = this.cid ? this.cid : 2;
            this.item.username = '';
            this.item.password = '';
            this.item.rpassword = '';
            this.dialogItemTitle = '添加用户';
            this.dialogItemStatus = true;
        },
        itemEdit(item) {
            this.item.uid = item.uid;
            this.item.groupId = item.group_id;
            this.item.username = item.username;
            this.item.password = '';
            this.item.rpassword = '';
            this.dialogItemTitle = '修改用户';
            this.dialogItemStatus = true;
        },

        itemPost(val) {
            if (this.item.uid) {
                this.$mip.ajax('{$domain}/user/ApiAdminUser/itemEdit', {
                    'uid': this.item.uid,
                    "groupId": this.item.groupId,
                    "username": this.item.username,
                    "password": md5(this.item.password),
                    "rpassword": md5(this.item.rpassword),
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('修改成功');
                        this.getItemList();
                        this.dialogItemStatus = false;
                    }
                });
            } else {
                this.$mip.ajax('{$domain}/user/ApiAdminUser/itemAdd', {
                    "groupId": this.item.groupId,
                    "username": this.item.username,
                    "password": md5(this.item.password),
                    "rpassword": md5(this.item.rpassword),
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('添加成功');
                        this.getItemList();
                        this.dialogItemStatus = false;
                    }
                });
            }
        },

        getItemList() {
            this.$mip.ajax('{$domain}/user/ApiAdminUser/itemList', {
                page: this.pagination.currentPage,
                limit: this.pagination.limit,
                orderBy: 'reg_time',
                keywords: this.searchData,
                cid: this.cid,
            }).then(res => {
				this.itemList = [];
                if(res.code == 1) {
                    var itemList = res.data.itemList;
                    if (itemList) {
                        for (var i = 0; i < itemList.length; i++) {
                            itemList[i].itemListSelectStatus = false; 
                        }
                        this.itemList = itemList;
                    }
                    this.pagination.total = res.data.total;
                }
            });
        },

        getCategoryList: function() {
            this.$mip.ajax('{$domain}/user/ApiAdminUserGroup/itemList', {
            }).then(res => {
                this.loading = false;
                if(res.code == 1) {
                    var categoryList = res.data;
                    this.categoryList = categoryList;
                    var tempCategoryList = [{children: [],group_id:'',active: true,name:'全部'}];
                    for (var i = 0; i < categoryList.length; i++) {
                        categoryList[i].disabled = false;
                        categoryList[i].active = false;
                       	tempCategoryList.push(categoryList[i]);
                    }
                    this.sideCategoryList = tempCategoryList;
                    var transferList = res.data.categoryList;
                    this.transferList = transferList;
                }
            });
        },

        itemsDel() {
            this.$Modal.confirm({
                title: '消息提示',
                content: '<p>确定删除么？删除后不可恢复</p>',
                onOk: () => {
                    var itemIds = [];
                    for(var i = 0; i < this.itemList.length; i++) {
                        if (this.itemList[i].itemListSelectStatus) {
                            itemIds.push(this.itemList[i].uid);
                        }
                    }
                    itemIds = itemIds.join(',');
                    this.$mip.ajax('{$domain}/user/ApiAdminUser/itemsDel', {
                        uids:itemIds,
                    }).then(res => {
                        if(res.code == 1) {
                            this.$Message.success(res.msg);
                        }
                        this.getItemList();
                    });
                },
                onCancel: () => {
                }
            });
        },

        itemDel(item) {
            this.$Modal.confirm({
                title: '消息提示',
                content: '<p>确定删除么？删除后不可恢复</p>',
                onOk: () => {
                    this.$mip.ajax('{$domain}/user/ApiAdminUser/itemDel', {
                        uid: item.uid,
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

        //选择全部
        itemListSelectChange() {
            var itemList = this.itemList;
            for (var i = 0; i < itemList.length; i++) {
                if (this.itemListSelectStatus) {
                    itemList[i].itemListSelectStatus = true;
                } else {
                    itemList[i].itemListSelectStatus = false;
                }
            }
            this.itemList = itemList;
        },
        
        itemPaginationSelect(val) {
            this.pagination.limit = val;
            this.getItemList();
        },
        
        itemPaginationClick(val) {
            this.pagination.currentPage = val;
            this.getItemList();
        },
        
        //搜索
        itemSearchClick: function() {
            this.getItemList();
        },
        
        menuClick(id) {
            for (var i = 0; i < this.sideCategoryList.length; i++) {
                this.sideCategoryList[i].active = false;
                if (this.sideCategoryList[i].children) {
                    for (var j = 0; j < this.sideCategoryList[i].children.length; j++) {
                        this.sideCategoryList[i].children[j].active = false;
                    }
                }
            }
            for (var i = 0; i < this.sideCategoryList.length; i++) {
                if (id == this.sideCategoryList[i].group_id) {
                    this.sideCategoryList[i].active = true;
                }
                if (this.sideCategoryList[i].children) {
                    for (var j = 0; j < this.sideCategoryList[i].children.length; j++) {
                        if (id == this.sideCategoryList[i].children[j].group_id) {
                            this.sideCategoryList[i].children[j].active = true;
                        }
                    }
                }
            }
            this.cid = id;
            this.pagination.currentPage = '1';
            this.getItemList();
        },
    }
}
</script>
