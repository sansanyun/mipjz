<style>
	
</style>
<template>
<div>
    <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">功能</h4> <h5 class="sub-title">导航链接</h5>
        </div>
        <Button type="primary" class="float-right" shape="circle" icon='md-add' @click="itemAdd"> 添加内容</Button>
    </header>
    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
        <section class="mip-box">
            <section class="mip-box-body">
                <!--内容列表-->
                <section class="diy-table-list" v-cloak>
                    <div class="diy-table-item diy-table-item-header">
                        <ul class="list-unstyled row">
                            <li class="col-md-1"><span>ID</span></li>
                            <li class="col-md-3"><span>名称</span></li>
                            <li class="col-md-3"><span>链接</span></li>
                            <li class="col-md-1"><span>排序</span></li>
                            <li class="col-md-2"><span>类型</span></li>
                            <li class="col-md-2"><span>操作</span></li>
                        </ul>
                    </div>
                    <div v-if='itemList.length' class="diy-table-body">
                        <div class="diy-table-item" v-for='(item,index) in itemList'>
                        	 <ul class="list-unstyled row">
                                <li class="col-md-1"><span>{{item.id}}</span></li>
                                <li class="col-md-3"><span>{{item.name}}</span></li>
                                <li class="col-md-3"><span>{{item.url}}</span></li>
                                <li class="col-md-1">
	                                <i-Button type="text" size="small" class='ivu-btn-text-up-down' @click='moveUp(item)' :disabled='index==0'><Icon type="md-arrow-up" /></i-Button>
	                                <i-Button type="text" size="small" class='ivu-btn-text-up-down' @click='moveDown(item)' :disabled='itemList.length==index+1'><Icon type="md-arrow-down" /></i-Button>
	                            </li>
                                <li class="col-md-2"><span>{{item.type}}</span></li>
                                <li class="col-md-2">
                                    <span>
                                        <a @click="itemDel(item)" >删除</a>   
                                        <a @click="itemEdit(item)" class="ml-1">编辑</a>   
                                    </span>
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
    <Modal title="添加/修改" size="small" v-model='navModalStatus' v-cloak>
        <Form>
	        <FormItem label="链接名称">
	    		<Input size="large" v-model="itemLink.name"/>
	        </FormItem>
	        <FormItem label="选择链接类型">
	        	<Select v-model="itemLink.type" transfer>
			        <Option v-for="link in linkTypeList" :value="link.name" :key="link.name">{{ link.title }}</Option>
			    </Select>
	        </FormItem>
	        <FormItem label="页面" v-if='itemLink.type == "page"'>
	        	<Select v-model="itemLink.url" transfer>
			        <Option v-for="page in sitePageList" :value="page.url" :key="page.url">{{ page.title }}</Option>
			    </Select>
	        </FormItem>
	        <FormItem label="链接网址" v-if='itemLink.type == "url"'>
	    		<Input size="large" v-model="itemLink.url"/>
	        </FormItem>
	        <FormItem label="新窗口打开">
			    <i-switch v-model="itemLink.target" true-value='1' false-value='0'>
			        <span slot="open">是</span>
			        <span slot="close">否</span>
			    </i-switch>
	        </FormItem>
	    </Form>
        <div slot="footer" class="dialog-footer">
            <i-button @click="navModalStatus = false">取 消</i-button>
            <i-button type="primary" @click="itemPost()">确 定</i-button>
        </div>
    </Modal>
</div>
</template>

<script>
export default {
	data () {
		return {
            itemListSelectStatus: false,
            itemList: [],
            navModalStatus: false,
        	linkTypeList: [{name: 'url',title:'URL地址'},{name:'page',title:'单页面'}],
            itemLink: {
            	
            },
            sitePageList: '',
		}
	},
	watch: {
	},
	mounted() {
        this.getItemList();
        this.getPageList();
    },
    methods: {
    	itemAdd() {
    		this.navModalStatus = true;
    		this.itemLink = {
    			name: '',
    			type: 'url',
    			url: '',
    			target: '0',
    		};
    	},
    	itemPost() {
    		if (this.itemLink.id) {
	            this.$mip.ajax('{$domain}/widget/ApiAdminWidgetNav/itemEdit', {
	            	itemLink: this.itemLink,
	            }).then(res => {
	                if(res.code == 1) {
		            	this.getItemList();
		            	this.navModalStatus = false;
		              	this.$Message.success('操作成功');
	                }
	            });
    		} else {
	            this.$mip.ajax('{$domain}/widget/ApiAdminWidgetNav/itemAdd', {
	            	itemLink: this.itemLink,
	            }).then(res => {
	                if(res.code == 1) {
		            	this.getItemList();
		            	this.navModalStatus = false;
		              	this.$Message.success('操作成功');
	                }
	            });
    		}
    	},
    	itemEdit(item) {
    		this.navModalStatus = true;
    		item.target = item.target + '';
    		this.itemLink = item;
    	},
    	moveUp(item,subItem) {
            var itemList = this.itemList;
            for (var i = 0; i < itemList.length; i++) {
                   itemList[i].sort = i;
            }
            for(var i = 0; i < itemList.length; i++) {
                if (item.id == itemList[i].id) {
                    itemList[i-1].sort = i;
                    itemList[i].sort = i - 1;
                }
            }
            this.itemList = itemList;
            this.itemSortSave();
        },
        moveDown(item,subItem) {
            var itemList = this.itemList;
            for (var i = 0; i < itemList.length; i++) {
                   itemList[i].sort = i;
            }
            for(var i = 0; i < itemList.length; i++) {
                if (item.id == itemList[i].id) {
                    itemList[i+1].sort = i;
                    itemList[i].sort = i + 1;
                }
            }
            this.itemList = itemList;
            this.itemSortSave();
        },
        itemSortSave() {
            this.$mip.ajax('{$domain}/widget/ApiAdminWidgetNav/itemSortSave', {
            	itemList: this.itemList,
            }).then(res => {
                if(res.code == 1) {
	            	this.getItemList();
	              	this.$Message.success('操作成功');
                }
            });
        },
        itemDel(val) {
           this.$Modal.confirm({
                title: '消息提示',
                content: '<p>确定删除么？删除后不可恢复</p>',
                onOk: () => {
                    this.$mip.ajax('{$domain}/widget/ApiAdminWidgetNav/itemDel', {
                        id: val.id,
                    }).then(res => {
                        if(res.code == 1) {
                            this.$Message.success('操作成功');
                            this.getItemList();
                        }
                    });
                },
                onCancel: () => {
                }
            });
        },
        getItemList() {
            this.$mip.ajax('{$domain}/widget/ApiAdminWidgetNav/itemList', {
            }).then(res => {
                this.itemList = [];
                if(res.code == 1) {
                    var itemList = res.data;
                   	this.itemList = itemList;
                }
            });
        },
        getPageList() {
    		this.$mip.ajax('{$domain}/widget/ApiAdminWidgetPages/itemList', {
            }).then(res => {
                if(res.code == 1) {
                    var sitePageList = res.data.itemList;
                    this.sitePageList = sitePageList;
                }
            });
    	},
    }
}
</script>
