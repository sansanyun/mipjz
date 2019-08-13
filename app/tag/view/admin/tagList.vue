<style>
	
</style>
<template>
<div>
    <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">标签</h4> <h5 class="sub-title">全部标签</h5>
        </div>
        {if condition='$addInfo["tagimport"]'}
        <div class="pull-left ml-3">
            <Upload :on-success="handleSuccessExcel" :format="['xls','csv']" name='fileDataFileName'
                action="{$domain}/tag/ApiAdminTag/import">
                <i-button type="ghost" class='ivu-btn-circle'>Excel批量导入</i-Button>
            </Upload>
        </div>
        {/if}
        <div class="float-right">
             <button type="button" class="ivu-btn ivu-btn-primary ivu-btn-circle" @click='itemAdd'>
                <span><Icon type="plus-round"></Icon> 添加标签</span>
            </button>
        </div>
    </header>
    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
        <aside class="side-box mip-box" style="height: calc(100% - 50px - 92px);bottom: 15px;">
            <section class="side-box-body">
                <section class="side-list">
                    <ul>
                        <li class="nav-item" :class='{"active" : item.active}' v-for='item in categoryList'>
                            <a @click="menuClick(item.id)">{{item.name}}</a>
                            <ul class="list-unstyled children" v-if='item.children'>
                                <li v-for='sub in item.children' :class='{"active" : sub.active}'><a @click="menuClick(sub.id)">{{sub.name}}</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="pt-3 p-3">
                        <a @click='addCategory' class="ivu-btn ivu-btn-dashed ivu-btn-long"><i class="ion-ios-plus-empty"></i> <span>添加分类</span></a>
                    </div>
                </section>
            </section>
        </aside>
        <section class="content-box">
        <section class="mip-box">
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
                            {if condition='$addInfo["descriptionBatch"]'}
                         	<button class="ivu-btn ivu-btn-text" type="text" :disabled='!itemListSelectStatus' @click="descriptionBatchClick"><i class="ivu-icon ivu-icon-paper-airplane"></i> 描述生成</button>
                            {/if}
                        </div>
                    </div>
                    <div class="diy-table-item diy-table-item-header">
                        <ul class="list-unstyled row">
                           
                            <li class="col-md-1">
                                <Checkbox v-model="itemListSelectStatus"  :disabled='!itemList.length' @on-change='itemListSelectChange'>全选</Checkbox>
                            </li>
                            <li class="col-md-2">
                                <span>分类</span>
                            </li>
                            <li class="col-md-2">
                                <span>名称</span>
                            </li>
                            <li class="col-md-2">
                                <span>别名</span>
                            </li>
                            <li class="col-md-1">
                                <span>关联</span>
                            </li>
                            <li class="col-md-2">
                                <Dropdown trigger="click" @on-click='changeClick'>
							        <a href="javascript:void(0)">描述
							            <Icon type="arrow-down-b"></Icon>
							        </a>
							        <DropdownMenu slot="list">
							            <DropdownItem name='descriptionOk'>有</DropdownItem>
							            <DropdownItem name='descriptionNo'>无</DropdownItem>
							        </DropdownMenu>
							    </Dropdown>
                            </li>
                            <li class="col-md-2">
                                <span>操作</span>
                            </li>
                        </ul>
                    </div>
                    <div v-if='itemList.length' class="diy-table-body">
                        <div class="diy-table-item" v-for='item in itemList' >
                             <ul class="list-unstyled row">
                                    
                                <li class="col-md-1">
                                    <Checkbox v-model="item.itemListSelectStatus"></Checkbox>
                                </li>
                                <li class="col-md-2"> <span v-if='item.categoryInfo'>{{item.categoryInfo.name}}</span><span v-else>无分类</span></li>
                                <li class="col-md-2">
                                    <span>{{item.name}}</span>
                                </li>
                                <li class="col-md-2">
                                    <span>{{item.url_name}}</span>
                                </li>
                                <li class="col-md-1">
                                    <span>{{item.relevance_num}}</span>
                                </li>
                                <li class="col-md-2 over-h-e">
                                    <span :title="item.description">{{item.description}}</span>
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
            <i-form :model="item" :rules="itemRules" ref="item" :label-width="80" >
                <Form-Item label="分类" prop="title">
                    <i-Select v-model="item.cid" transfer style="width:200px">
                        <i-Option v-for="sub in item.categoryList" :value="sub.id" :key="item.id">{{ sub.name }}</i-Option>
                    </i-Select>
                </Form-Item> 
                <Form-Item label="标签名称" prop="name">
                    <i-input v-model="item.name" placeholder="例：网站优化"></i-input>
                </Form-Item>
                <Form-Item label="标签别名" prop="url_name">
                    <i-input v-model="item.url_name" placeholder="例：seo" ><i-Button slot="append" class="tags-btn" @click='getPinyin'>获取拼音</i-Button></i-input>
                </Form-Item>
                <Form-Item label="关键词">
                    <i-input type="textarea" v-model="item.keywords"></i-input>
                </Form-Item>
                <Form-Item label="标签描述">
                    <i-input type="textarea" v-model="item.description"></i-input>
                </Form-Item>
            </i-form>
            <div slot="footer" class="dialog-footer">
                <i-button @click="dialogItemStatus = false">取 消</i-button>
                <i-button type="primary" @click="itemPost('item')">确 定</i-button>
            </div>
        </Modal>
                
         <!--转移-->
        <Modal title="批量转移" v-model="transferStatus"  v-cloak>
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
        
        <Modal title='批量操作' v-model="descriptionModal" width="360" v-cloak>
	        <i-form :label-width="80">
	            <Form-Item label="进度">
	                <i-Progress :percent="scale"></i-Progress>
	            </Form-Item>
	        </i-form>
	
	        <span slot="footer" class="dialog-footer">
	            <i-button @click="descriptionModal = false">取 消</i-button>
	            <i-button type="primary" @click="descriptionBatchAction(0)">点击开始</i-button>
	        </span>
	    </Modal>
</div>
</template>

<script>
    export default {
     data () {
       return {
            itemName: '标签',
            itemListSelectStatus: false,
            cid: '',
            itemList: [], 
            searchData: '',
            
            categoryList: [],
            
            item: {
                cid: 0,
                categoryList: [],
                id: '',
                name: '',
                url_name: '',
                keywords: '',
                description: '',
                item_type: 'article',
            },
            dialogItemTitle: '添加标签',
            dialogItemStatus: false,
            
            itemRules: {
                name: [{
                    required: true,
                    message: '请输入名称',
                    trigger: 'blur'
                }],
            },
            
            //转移
            transferStatus: false,
            transferId: [],
            transferList: [],
            //描述生成
            descriptionModal: false,
            scale: 0,
            countList: [],
            type: '',
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
				deep: true
			},
        },
        mounted() {
            this.getCategoryList();
            this.getItemList();
        },
        methods: {
        	changeClick(val) {
        		this.type = val;
        		this.getItemList();
        	},
        	descriptionBatchClick() {
	         	this.scale = 0;
	    		this.countList = [];
        		this.descriptionModal = true;
        		var itemIds = [];
	            for(var i = 0; i < this.itemList.length; i++) {
	                if (this.itemList[i].itemListSelectStatus) {
	                    itemIds.push(this.itemList[i].id);
	                }
	            }
	    		this.countList = itemIds;
        	},
        	descriptionBatchAction(index) {
        		if ('{$addInfo["tagdescription"]}' == '') {
	          		this.$Message.error('您尚未安装收录查询插件，若已安装，请刷新当前页面');
	          		return false;
        		}
                var scale = parseInt(index / (parseInt(this.countList.length)) * 100);
            	if (scale < 100) {
	                this.scale = scale;
	            } else {
	                this.scale = 100;
	            }
	            if (parseInt(index) + 1 > this.countList.length) {
	          		this.$Message.success('操作完毕');
	    			this.getItemList();
	                return false;
	            }
        		this.$mip.ajax('{$domain}/tagdescription/ApiUserTagDescription/tagdescription', {
	            	uuid: this.countList[index],
	            }).then(res => {
	                if(res.code == 1) {
	                	this.descriptionBatchAction(index+1);
	                }
	            });
        	},
            handleSuccessExcel(res) {
            	console.log(res);
				if (res.code == 1) {
					this.$Message.success('操作成功');
				}
            },
            addCategory() {
                this.$router.push({
                    name: 'tagCategory', params: { mod:'tag'}
                });
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
                this.$mip.ajax('{$domain}/tag/ApiAdminTag/itemTransferAll', {
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
             
            getPinyin: function() {
                if (!this.item.name) {
                    this.$Message.error('请输入名称');
                    return false;
                }
                this.$mip.ajax('{$domain}/setting/ApiAdminTool/getPinyin', {
                    words: this.item.name,
                }).then(res => {
                    if(res.code == 1) {
                        this.item.url_name = res.data;
                    }
                });
            },
            itemAdd() {
                this.item.id = '';
                this.item.cid = this.cid;
                this.dialogItemTitle = '添加标签';
                this.item.name = '';
                this.item.url_name = '';
                this.item.keywords = '';
                this.item.description = '';
                this.dialogItemStatus = true;
            },
            itemsDel() {
                var _this = this;
                this.$Modal.confirm({
                    title: '消息提示',
                    content: '<p>确定删除么？删除后不可恢复</p>',
                    onOk: () => {
                        var itemIds = [];
                        for(var i = 0; i < this.itemList.length; i++) {
                            if (this.itemList[i].itemListSelectStatus) {
                                itemIds.push(this.itemList[i].id);
                            }
                        }
                        itemIds = itemIds.join(',');
                        this.$mip.ajax('{$domain}/tag/ApiAdminTag/itemsDel', {
                            ids: itemIds,
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
                        this.$mip.ajax('{$domain}/tag/ApiAdminTag/itemDel', {
                            id: item.id,
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
                this.item.id = item.id;
                this.item.cid = item.cid;
                this.dialogItemTitle = '修改标签';
                this.item.url_name = item.url_name;
                this.item.name = item.name;
                this.item.keywords = item.keywords;
                this.item.description = item.description;
                this.dialogItemStatus = true;
            },
            itemPost(val) {
                this.$refs[val].validate((valid) => {
                    if(valid) {
                        if (this.item.url_name) {
                            var parent=/^[a-zA-Z0-9_-]+$/;
                            if (!parent.test(this.item.url_name)) {
                                this.$Message.error('别名输入值不支持');
                                return false;
                            }
                        }
                        if (this.item.id) {
                            this.$mip.ajax('{$domain}/tag/ApiAdminTag/itemEdit', {
                                id: this.item.id,
                                name: this.item.name,
                                url_name: this.item.url_name,
                                description: this.item.description,
                                keywords: this.item.keywords,
                                item_type: this.item.item_type,
                                cid: this.item.cid,
                            }).then(res => {
                                if(res.code == 1) {
                                    this.$Message.success('修改成功');
                                    this.getItemList();
                                    this.dialogItemStatus = false;
                                }
                            });
                        } else {
                            this.$mip.ajax('{$domain}/tag/ApiAdminTag/itemAdd', {
                                name: this.item.name,
                                url_name: this.item.url_name,
                                keywords: this.item.keywords,
                                description: this.item.description,
                                item_type: this.item.item_type,
                                cid: this.item.cid,
                            }).then(res => {
                                if(res.code == 1) {
                                    this.$Message.success('添加成功');
                                    this.getItemList();
                                    this.dialogItemStatus = false;
                                }
                            });
                        }
                        
                    }
                });
            },
            getItemList() {
                this.loading = true;
                this.$mip.ajax('{$domain}/tag/ApiAdminTag/itemList', {
                    page: this.pagination.currentPage,
                    limit: this.pagination.limit,
                    type: this.type,
                    orderBy: 'add_time',
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
            getCategoryList: function() {
                this.loading = true;
                this.$mip.ajax('{$domain}/tag/ApiAdminTagCategory/categoryList', {
                }).then(res => {
                    this.loading = false;
                    if(res.code == 1) {
                        var categoryList = res.data.categoryList;
                        if (categoryList) {
                            var tempCategoryList = [{children: [],id:'',pid: 0,active: true,name:'全部'}];
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
                            
                            var categoryList = res.data.categoryList;
                            var tempCategoryList = [{children: [],id:0,pid: 0,name:'顶层分类'}];
                            for (var i = 0; i < categoryList.length; i++) {
                                categoryList[i].disabled = false;
                                if (categoryList[i].pid == 0) {
                                    tempCategoryList.push(categoryList[i]);
                                }
                            }
                            this.item.categoryList = tempCategoryList;
                        }
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
                this.cid = id;
                this.getItemList();
            },
        }
    }
</script>
