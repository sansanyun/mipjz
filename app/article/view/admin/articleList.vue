<style>
	
</style>
<template>
<div>
    <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">文章</h4> <h5 class="sub-title">全部文章</h5>
        </div>
        <Button type="primary" class="float-right" shape="circle" icon='md-add' @click="articlePublishClick"> 添加文章</Button>
    </header>
    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
        <aside class="side-box mip-box" style="height: calc(100% - 50px - 92px);bottom: 15px;">
            <section class="side-box-body">
                <section class="side-list">
                    <ul>
                        <li class="nav-item" :class='{"active" : item.active}' v-for='item in categoryList'>
                            <a @click="menuClick(item.id,item)">{{item.name}}</a>
                            <ul class="list-unstyled children" v-if='item.children'>
                                <li v-for='sub in item.children' :class='{"active" : sub.active}'><a @click="menuClick(sub.id,sub)">{{sub.name}}</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="pt-3 p-3">
                        <a @click='addCategory' class="ivu-btn ivu-btn-dashed ivu-btn-long"><Icon type="md-add" /> <span>添加分类</span></a>
                    </div>
                </section>
            </section>
        </aside>
        <section class="content-box">
            <section class="mip-box">
                <section class="mip-box-body" v-if='categoryInfo && categoryInfo["is_page"] == 0'>
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
                                <li class="col-md-1">
                                    <Checkbox v-model="itemListSelectStatus"  :disabled='!itemList.length' @on-change='itemListSelectChange'>全选</Checkbox>
                                </li>
                                <li class="col-md-1"><span>分类</span></li>
                                <li class="col-md-3"><span>标题</span></li>
                                <li class="col-md-2"><span>时间</span></li>
                                <li class="col-md-1"><span>浏览</span></li>
                                <li class="col-md-2"><span>操作</span></li>
                            </ul>
                        </div>
                        <div v-if='itemList.length' class="diy-table-body">
                            <div class="diy-table-item" v-for='item in itemList' >
                                 <ul class="list-unstyled row">
                                    <li class="col-md-1"><Checkbox v-model="item.itemListSelectStatus"></Checkbox></li>
                                    <li class="col-md-1"> <span v-if='item.categoryInfo'><a :href="item.categoryInfo.url" target="_blank">{{item.categoryInfo.name}}</a></span><span v-else>无分类</span></li>
                                    <li class="col-md-3 over-h-e"><span><a :href="item.url" target="_blank">{{item.title}}</a></span></li>
                                    <li class="col-md-2"><span>{{item.publish_time | time}}</span></li>
                                    <li class="col-md-1"><span>{{item.views}}</span></li>
                                    <li class="col-md-2">
                                        <span>
                                            <a @click="itemDel(item)" >删除</a>   
                                            <a v-if='!item.is_recommend' @click="itemRecomment(item)" class="ml-1">推荐</a>
                                            <a v-else @click="itemRecomment(item)" class="ml-1">取消</a>
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
                    <!--分页-->
                    <div class="text-right clearfix mt-3">
                        <div class="diy-table-item float-left" style="margin-left: 10px;">
                            <Checkbox v-model="itemListSelectStatus"  :disabled='!itemList.length' @on-change='itemListSelectChange'>全选</Checkbox>
                            <button class="ivu-btn ivu-btn-text" type="text" :disabled='!itemListSelectStatus' @click="transferClick"><Icon type="arrow-swap"></Icon> 批量转移</button>
                        </div>
                        <Page :total="pagination.total"  @on-page-size-change='itemPaginationSelect' :page-size-opts='[10,100,500,1000,5000]' show-total show-sizer placement='top' @on-change='itemPaginationClick'></Page>
                    </div>
                    <Spin fix v-if="loading">
                        <svg viewBox="25 25 50 50" class="loding-circular">
                            <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                        </svg>
                        <p class="text-center">数据加载中...</p>
                    </Spin>
                </section>
                <!---->
				<articlePage class="mip-box-body" v-if='!articlePublishStatus && categoryInfo && categoryInfo["is_page"] == 1' :articlePublishCid='articlePublishCid'></articlePage>
            </section>
        </section>
    </main>
    
    <articlePublish v-if='articlePublishStatus' v-on:articlePublishValue='articlePublishValue' :articlePublishUuid='articlePublishUuid' :articlePublishCid='articlePublishCid'></articlePublish>
    
    <Modal title="批量转移" v-model="transferStatus">
        <i-form :label-width="80"> 
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
            loading: false,
            itemListSelectStatus: false,
            searchData: '',
            cid: '',
            categoryList: [],
            itemList: [], 
            pullData: [],
            articlePublishStatus: false,
            articlePublishUuid: '',
            articlePublishCid: '',
            pullBtnInfo: '点击更新最新数据',
            status: '',
            statusText: '全部',
            //转移
            transferStatus: false,
            transferId: [],
            transferList: [],
            //
            categoryInfo: {
            	is_page: '0',
            	content: '',
            },
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
            },
            deep: true,
		},
        articlePublishValue(val) {
            this.articlePublishValue(val);
        },
	},
	mounted() {
        this.getItemList();
        this.getCategoryList();
    },
    methods: {
        addCategory() {
            this.$router.push({
                name: 'articleCategory'
            });
        },
        editorInit() {
	        var _this = this;
	        setTimeout(function() {
	            _this.createEditor();
	        }, 100);
	    },
	    
	    //创建编辑器
	    createEditor() {
	        var _this = this;
	        this.uploadUrl = '{$domain}/setting/ApiAdminUpload/defaultImgUpload';
//	        document.getElementById('article_editor').innerHTML = '';
	        if (this.editor) {
	        	this.editor.destroy();
	        }
	        this.editor = new Simditor({
	            textarea: document.getElementById('article_editor'),
	            toolbar: _this.toolbar,
	            upload: {
	                url: this.uploadUrl,
	                params: {
	                    type: 'article',
	                },
	                fileKey: 'fileDataFileName',
	                connectionCount: 3,
	                leaveConfirm: '正在上传文件'
	            },
	            pasteImage: true,
	            autosave: 'editor-content'
	        });
	       	this.editor.setValue('');
	    },
		
        //转移
        transferClick() {
            this.transferStatus = true;
            this.transferId = [];
        },
        transferSubmit() {
            var itemIds = [];
            for(var i = 0; i < this.itemList.length; i++) {
                if (this.itemList[i].itemListSelectStatus) {
                    itemIds.push(this.itemList[i].id);
                }
            }
            itemIds = itemIds.join(',');
            this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemTransferAll', {
                cid: this.transferId[this.transferId.length-1],
                ids:itemIds,
            }).then(res => {
                if(res.code == 1) {
                    this.$Message.success('操作成功');
                    this.getItemList();
                    this.transferStatus = false;
                }
            });
        },
        itemEdit(item) {
            this.articlePublishUuid = item.uuid;
            this.articlePublishCid = item.cid;
            this.articlePublishStatus = true;
        },
        getCategoryList: function() {
            this.loading = true;
            this.$mip.ajax('{$domain}/article/ApiAdminArticleCategory/categoryList', {
            }).then(res => {
                this.loading = false;
                if(res.code == 1) {
                    var categoryList = res.data.categoryList;
                    var tempCategoryList = [{children: [],id:'',pid: 0,active: true,name:'全部',is_page: 0}];
                    for (var i = 0; i < categoryList.length; i++) {
                        categoryList[i].disabled = false;
                        categoryList[i].active = false;
                        if (categoryList[i].children) {
                            for (var j = 0; j < categoryList[i].children.length; j++) {
                                categoryList[i].children[j].active = false;
                            }
                        }
                        if (categoryList[i].pid == 0) {
                            tempCategoryList.push(categoryList[i]);
                        }
                    }
                    this.categoryList = tempCategoryList;

                    var transferList = res.data.categoryList;
                    this.transferList = transferList;
                     
                }
            });
        },
   
        
        itemDel(val) {
           this.$Modal.confirm({
                title: '消息提示',
                content: '<p>确定删除么？删除后不可恢复</p>',
                onOk: () => {
                    this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemDel', {
                        uuid: val.uuid,
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
        itemsDel() {
            this.$Modal.confirm({
                title: '消息提示',
                content: '<p>确定删除么？删除后不可恢复</p>',
                onOk: () => {
                    var itemIds = [];
                    for(var i = 0; i < this.itemList.length; i++) {
                        if (this.itemList[i].itemListSelectStatus) {
                            itemIds.push(this.itemList[i].uuid);
                        }
                    }
                    itemIds = itemIds.join(',');
                    this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemsDel', {
                        uuids: itemIds,
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
        itemRecomment(row) {
            this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemRecomment', {
                id: row.id,
            }).then(res => {
                if(res.code == 1) {
                   	this.$Message.success(res.msg);
                }
                this.getItemList();
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
        pushStautsClick(val) {
            this.status = val;
            if (val === '') {
                this.statusText = '全部';
            }
            if (parseInt(val) == 0) {
                this.statusText = '未推送';
            }
            if (parseInt(val) == 1) {
                this.statusText = '已推送';
            }
            this.getItemList();
        },
        getItemList() {
            this.loading = true;
            this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemList', {
                status: this.status,
                page: this.pagination.currentPage,
                limit: this.pagination.limit,
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
                this.loading = false;
            });
        },
        //监听关闭
        articlePublishValue(val) {
            if (val == 'close') {
                this.articlePublishStatus = false;
            }
            if (val == 'closePublish') {
                this.articlePublishStatus = false;
                this.getItemList();
            }
            this.articlePublishUuid = '';
            this.articlePublishCid = '';
        },
        articlePublishClick() {
            this.articlePublishUuid = '';
            this.articlePublishCid = this.cid;
            this.articlePublishStatus = true;
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
        menuClick(id,categoryInfo) {
            for (var i = 0; i < this.categoryList.length; i++) {
                this.categoryList[i].active = false;
                if (this.categoryList[i].children) {
                    for (var j = 0; j < this.categoryList[i].children.length; j++) {
                        this.categoryList[i].children[j].active = false;
                    }
                }
            }
            for (var i = 0; i < this.categoryList.length; i++) {
                if (id == this.categoryList[i].id) {
                    this.categoryList[i].active = true;
                }
                if (this.categoryList[i].children) {
                    for (var j = 0; j < this.categoryList[i].children.length; j++) {
                        if (id == this.categoryList[i].children[j].id) {
                            this.categoryList[i].children[j].active = true;
                        }
                    }
                }
            }
        
        	if (this.cid == categoryInfo.id && this.categoryInfo.is_page) {
        		return false;
        	}
            
            this.cid = id;
            this.articlePublishCid = categoryInfo.id;
            this.categoryInfo = categoryInfo;
            if (this.categoryInfo.is_page) {
            	this.categoryStatus = true;
				this.editorInit();
            }
            
            this.searchData = '';
            this.getItemList();
        },
    }
}
</script>
