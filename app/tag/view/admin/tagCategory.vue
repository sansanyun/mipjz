<style>
	
</style>
<template>
    <div>
        <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">标签</h4> <h5 class="sub-title">分类管理 （注：显示、隐藏功能，需自行在模板中调用）</h5>
        </div>
        <Button type="primary" class="float-right" shape="circle" icon='md-add' @click="newAdd"> 添加分类</Button>
    </header>
    <main class="mipcms-container-body" style="height: calc(100% - 50px)">
        <section class="mip-box">
        <section class="mip-box-body">
            <section class="diy-table-list" v-cloak>
                <div class="diy-table-item diy-table-item-header">
                    <ul class="list-unstyled row">
                        <li class="col-md-1"></li>
                        <li class="col-md-1">ID</li>
                        <li class="col-md-1">名称</li>
                        <li class="col-md-2">URL别名</li>
                        <li class="col-md-1 pl-4">排序</li>
                        <li class="col-md-1">模板</li>
                        <li class="col-md-1">关键词</li>
                        <li class="col-md-1">描述</li>
                        <li class="col-md-1"></li>
                        <li class="col-md-2">操作</li>
                    </ul>
                </div>
                <div v-if='categoryList' class="diy-table-body">
                    <div class="diy-table-item" v-for='(item,index) in categoryList' :class='{"is-open":item.isOpen}'>
                         <ul class="list-unstyled row">
                            <li class="col-md-1">
                                <i-Button type="text" size="small" v-if='item.children.length' @click='openChildren(item)'>
                                    <Icon type="ios-arrow-right" v-if='!item.isOpen'></Icon>
                                    <Icon type="ios-arrow-down" v-if='item.isOpen'></Icon>
                                </i-Button>
                            </li>
                            <li class="col-md-1">{{item.id}}</li>
                            <li class="col-md-1"><a :href="item.url" target="_blank">{{item.name}}</a></li>
                            <li class="col-md-2">{{item.url_name}}</li>
                            <li class="col-md-1">
                                <i-Button type="text" size="small" class='ivu-btn-text-up-down' @click='moveUp(item)' :disabled='index==0'><Icon type="md-arrow-up" /></i-Button>
                                <i-Button type="text" size="small" class='ivu-btn-text-up-down' @click='moveDown(item)' :disabled='categoryList.length==index+1'><Icon type="md-arrow-down" /></i-Button>
                            </li>
                            <li class="col-md-1">{{item.template}}</li>
                            <li class="col-md-1"><span v-if='item.keywords'><Icon type="ios-checkmark-empty"></Icon></span></li>
                            <li class="col-md-1"><span v-if='item.description'><Icon type="ios-checkmark-empty"></Icon></span></li>
                            <li class="col-md-1"></li>
                            <li class="col-md-2">
                                <Button size="small" type="text"  :disabled="item.children.length != 0" @click="categoryDel(index, item)">删除</Button>
                            	<Button size="small" v-if='item.status != 2' type="text" @click="switchStatus(index, item)">隐藏</Button>
                            	<Button size="small" v-else type="text" @click="switchStatus(index, item)">显示</Button>
                            	<Button size="small" type="primary" @click="categoryEditDialog(index, item)">编辑</Button>
                            </li>
                            
                        </ul>
                        <div class="children-list" v-if='item.children.length != 0' v-for='(sub,subIndex) in item.children'>
                                 <ul class="list-unstyled row">
                                    <li class="col-md-1"></li>
                                    <li class="col-md-1">{{sub.id}}</li>
                                    <li class="col-md-1"><a :href="sub.url" target="_blank">{{sub.name}}</a></li>
                                    <li class="col-md-2">{{sub.url_name}}</li>
                                    <li class="col-md-1">
                                        <button type="text" size="small" class='ivu-btn-text-up-down ivu-btn ivu-btn-text ivu-btn-small' @click='moveUp(item,sub)' :disabled='subIndex==0'><Icon type="md-arrow-round-up" /></button>
                                        <button type="text" size="small" class='ivu-btn-text-up-down ivu-btn ivu-btn-text ivu-btn-small' @click='moveDown(item,sub)' :disabled='item.children.length==subIndex+1'><Icon type="md-arrow-dropup" /></button>
                                    </li>
                                    <li class="col-md-1">{{item.template}}</li>
                                    <li class="col-md-1"><span v-if='item.keywords'><Icon type="ios-checkmark-empty"></Icon></span></li>
                                    <li class="col-md-1"><span v-if='item.description'><Icon type="ios-checkmark-empty"></Icon></span></li>
                                    <li class="col-md-1"></li>
                                    <li class="col-md-2">
                                        <Button size="small" type="text" @click="categoryDel(subIndex, sub)">删除</Button>
                                        <Button size="small" v-if='sub.status != 2' type="text" @click="switchStatus(subIndex, sub)">隐藏</Button>
                                        <Button size="small" v-else type="text" @click="switchStatus(subIndex, sub)">显示</Button>
                                        <Button size="small" type="primary" @click="categoryEditDialog(subIndex, sub)">修改</Button>
                                    </li>
                                </ul>
                        </div>
                     </div>
                </div>
                <div class="no-block" v-else>
                    <Icon type="ios-filing-outline"></Icon>
                    <p>暂无数据</p>
                </div>
            </section>
        </section>
    </section>
    </main>
    <Modal v-model="category.dialogCategory" size="large" v-cloak :mask-closable="false">
        <i-form :model="category" :rules="category.categoryRules" ref="category" :label-width="80">
            <Tabs :animated='false' v-model="itemTabsValue">
                <Tab-Pane label="基础信息" name='default'>
                   
                    <Form-Item label="分类名称" prop="name">
                        <i-input style="width:300px" v-model="category.name" placeholder="例：网站优化"></i-input>
                    </Form-Item>
                    <Form-Item label="URL别名" prop="url_name">
                        <i-input style="width:300px" placeholder="例：seo" v-model="category.url_name"><i-Button slot="append" class="tags-btn" @click='getPinyin'>获取拼音</i-Button></i-input>
                    </Form-Item>
                </Tab-Pane>
                
                <Tab-Pane label="SEO选项" name='seo'>
                    <form-item label="自定义标题">
                        <i-input placeholder="例：自定义分类标题" v-model="category.seo_title"></i-input>
                    </Form-Item>
                    <Form-Item label="关键词">
                        <i-input type="textarea" v-model="category.keywords" placeholder="例：SEO,SEO优化,搜索引擎优化,网站排名"></i-input>
                    </Form-Item>
                    <Form-Item label="描述">
                        <i-input type="textarea" v-model="category.description" placeholder="请输入description描述信息"></i-input>
                    </Form-Item>
                </Tab-Pane>
                <Tab-Pane label="模板页选择" name='template'>
                    <Form-Item label="分类模板">
                        <i-Select v-model="category.template" transfer style="width:300px">
                            <i-Option v-for="item in templateList" :value="item" :key="item">{{ item }}</i-Option>
                        </i-Select>
                    </Form-Item>
                    <Form-Item label="详情模板">
                        <i-Select v-model="category.detail_template" transfer style="width:300px">
                            <i-Option v-for="item in templateList" :value="item" :key="item">{{ item }}</i-Option>
                        </i-Select>
                    </Form-Item>
                </Tab-Pane>
            </Tabs>
            </i-form>
            <div slot="footer" class="dialog-footer">
                <i-button @click="category.dialogCategory = false">取 消</i-button>
                <i-button type="primary" @click="categoryPost('category',category)">确 定</i-button>
            </div>
        </Modal>
    </div>
</template>

<script>
    export default {
     data () {
       return {
         loading: false,
            category: {
                categoryList: [],
                dialogCategory: false,
                dialogCategoryTitle: '',
                isChild: false,
                id: '',
                pid: 0,
                name: '',
                url_name: '',
                seo_title: '',
                description: '',
                keywords: '',
                template: 'tag.html',
                detail_template: 'tagDetail.html',
                category_url: '/tag/<url_name>/',
                category_page_url: '<category_url>index_<page>.html',
                detail_url: '/tag/<id>.html',
                categoryRules: {
                    name: [{
                        required: true,
                        message: '请输入名称',
                        trigger: 'blur'
                    }],
                    url_name: [{
                        required: true,
                        message: '请输入别名',
                        trigger: 'blur'
                    }],
                },
                categoryStatus: false,
                categoryListEdit: [],
            },
            categoryList: [],
            tempCategoryList: [],
            templateList: [],
            itemTabsValue: 'default',
            
        	content: '',
       }
     },
        watch: {
        },
        mounted() {
      		this.getTemplateList();
        },
        methods: {
            moveUp(item,subItem) {
                var categoryList = this.categoryList;
                for (var i = 0; i < categoryList.length; i++) {
                       categoryList[i].sort = i;
                       if (categoryList[i].children.length) {
                           for (var j = 0; j < categoryList[i].children.length; j++) {
                               categoryList[i].children[j].sort = j;
                           }
                       }
                }
                if (!subItem) {
                    for(var i = 0; i < categoryList.length; i++) {
                        if (item.id == categoryList[i].id) {
                            categoryList[i-1].sort = i;
                            categoryList[i].sort = i - 1;
                        }
                    }
                } else {
                    for(var i = 0; i < categoryList.length; i++) {
                        if (item.id == categoryList[i].id) {
                             for (var j = 0; j < categoryList[i].children.length; j++) {
                                   if (subItem.id == categoryList[i].children[j].id) {
                                    categoryList[i].children[j-1].sort = j;
                                    categoryList[i].children[j].sort = j - 1;
                                }
                            }
                        }
                    }
                    
                }
                this.categoryList = categoryList;
                this.tempCategoryList = categoryList;
                this.categorySortSave();
            },
            moveDown(item,subItem) {
                var categoryList = this.categoryList;
                for (var i = 0; i < categoryList.length; i++) {
                       categoryList[i].sort = i;
                       if (categoryList[i].children.length) {
                           for (var j = 0; j < categoryList[i].children.length; j++) {
                               categoryList[i].children[j].sort = j;
                           }
                       }
                }
                if (!subItem) {
                    for(var i = 0; i < categoryList.length; i++) {
                        if (item.id == categoryList[i].id) {
                            categoryList[i+1].sort = i;
                            categoryList[i].sort = i + 1;
                        }
                    }
                } else {
                    for(var i = 0; i < categoryList.length; i++) {
                        if (item.id == categoryList[i].id) {
                             for (var j = 0; j < categoryList[i].children.length; j++) {
                                   if (subItem.id == categoryList[i].children[j].id) {
                                    categoryList[i].children[j+1].sort = j;
                                    categoryList[i].children[j].sort = j + 1;
                                }
                            }
                        }
                    }
                }
                this.categoryList = categoryList;
                this.tempCategoryList = categoryList;
                this.categorySortSave();
            },
            openChildren(item) {
                var categoryList = this.categoryList;
                for (var i = 0; i < categoryList.length; i++) {
                    if (item.id == categoryList[i].id) {
                        categoryList[i].isOpen = !categoryList[i].isOpen;
                    }
                }
                this.categoryList = categoryList;
            },
            getTemplateList() {
            	if (this.zhanqunInfo && this.zhanqunInfo.type == 'client') {
                    var templateListUrl  = this.zhanqunInfo.web_url + '/setting/ApiUserTemplate/templateTagList';
	                this.$mip.ajax(templateListUrl, {
	                    key: this.zhanqunInfo.link_key
	                }).then(res => {
	                    if(res.code == 1) {
	                       this.templateList = res.data;
	            			this.getCategoryList();
	                    }
	                });
                } else {
                    var templateListUrl  = '{$domain}/tag/ApiAdminTagCategory/getTemplate';
	                this.$mip.ajax(templateListUrl, {
	                }).then(res => {
	                    if(res.code == 1) {
	                       this.templateList = res.data;
	            			this.getCategoryList();
	                    }
	                });
                }
            },
            getPinyin: function() {
                if (!this.category.name) {
                    this.$Message.error('请输入名称');
                    return false;
                }
                this.$mip.ajax('{$domain}/setting/ApiAdminTool/getPinyin', {
                    words: this.category.name,
                }).then(res => {
                    if(res.code == 1) {
                        this.category.url_name = res.data;
                    }
                });
            },
            newAdd: function() {
                this.category.categoryStatus = false;
                this.category.isChild = false;
                this.itemTabsValue = 'default';
                this.category.pid = 0,
                this.category.name = '';
                this.category.url_name = '';
                this.category.seo_title = '';
                this.category.template = 'tag.html';
                this.category.detail_template = 'tagDetail.html';
                this.category.category_url = '/tag/<url_name>/';
                this.category.category_page_url = '<category_url>index_<page>.html';
                this.category.detail_url = '/tag/<id>.html';
                this.category.description = '';
                this.category.keywords = '';
                this.category.id = '';
                this.category.dialogCategory = true;
                this.category.dialogCategoryTitle = '添加分类';
            },
            categorySortSave() {
                var itemList = this.categoryList;
                this.$mip.ajax('{$domain}/tag/ApiAdminTagCategory/categorySortSave', {
                    itemList: itemList,
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('操作成功');
                        this.getCategoryList();
                    }
                });
            },
            categoryPost(val, param) {
            	if (!this.category.url_name) {
                    this.$Message.error('请输入基础信息中的必填项');
                    return false;
            	}
            	if (!this.category.name) {
                    this.$Message.error('请输入基础信息中的必填项');
                    return false;
            	}
                this.$refs[val].validate((valid) => {
                    if(valid) {
                        var parent=/^[a-zA-Z0-9_-]+$/;
                        if (!parent.test(this.category.url_name)) {
                            this.$Message.error('别名输入值不支持');
                            return false;
                        }
                        if(this.category.categoryStatus == false) {
                            this.$mip.ajax('{$domain}/tag/ApiAdminTagCategory/categoryAdd', {
                                pid: this.category.pid,
                                name: this.category.name,
                                url_name: this.category.url_name,
                                seo_title: this.category.seo_title,
                                template: this.category.template,
                                detail_template: this.category.detail_template,
                                category_url: this.category.category_url,
                                category_page_url: this.category.category_page_url,
                                detail_url: this.category.detail_url,
                                description: this.category.description,
                                keywords: this.category.keywords,
                            }).then(res => {
                                if(res.code == 1) {
                                    this.category.dialogCategory = false;
                                    this.$Message.success(res.msg);
                                    this.getCategoryList();
                                }
                            });
                        } else {
                            this.$mip.ajax('{$domain}/tag/ApiAdminTagCategory/categoryEdit', {
                                id: param.id,
                                pid: this.category.pid,
                                name: this.category.name,
                                url_name: this.category.url_name,
                                seo_title: this.category.seo_title,
                                template: this.category.template,
                                detail_template: this.category.detail_template,
                                category_url: this.category.category_url,
                                category_page_url: this.category.category_page_url,
                                detail_url: this.category.detail_url,
                                description: this.category.description,
                                keywords: this.category.keywords,
                            }).then(res => {
                                if(res.code == 1) {
                                    this.category.dialogCategory = false;
                                    this.$Message.success(res.msg);
                                    this.getCategoryList();
                                }
                            });
                        }
                    }
                });
            },
            switchStatus(index, val) {
            	this.$mip.ajax('{$domain}/tag/ApiAdminTagCategory/switchStatus', {
                    id: val.id,
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success('操作成功');
                        this.getCategoryList();
                    }
                });
            },
            categoryDel(index, val) {
               this.$Modal.confirm({
                    title: '消息提示',
                    content: '<p style="color:#ff9900;">删除项：'+val.name+'</p><p>确定删除么？删除后不可恢复</p>',
                    onOk: () => {
                        this.$mip.ajax('{$domain}/tag/ApiAdminTagCategory/categoryDel', {
                            id: val.id,
                        }).then(res => {
                            if(res.code == 1) {
                                this.$Message.success(res.msg);
                                this.getCategoryList();
                            }
                        });
                    },
                    onCancel: () => {
                    }
                });
            },
            getCategoryList: function() {
                this.$mip.ajax('{$domain}/tag/ApiAdminTagCategory/categoryList', {
                }).then(res => {
                    if(res.code == 1) {
                        var categoryList = res.data.categoryList;
                        for (var i = 0; i < categoryList.length; i++) {
                            categoryList[i].isOpen = false;
                        }
                        for (var i = 0; i < categoryList.length; i++) {
                            if (this.tempCategoryList.length) {
                                for (var j = 0; j < this.tempCategoryList.length; j++) {
                                       if (this.tempCategoryList[j].id == categoryList[i].id) {
                                        categoryList[i].isOpen = this.tempCategoryList[j].isOpen;
                                       }
                                }
                            }
                        }
                        this.categoryList = categoryList;
                        
                        var tempCategoryList = [{id:0,pid: 0,name:'顶层分类'}];
                        for (var i = 0; i < categoryList.length; i++) {
                            categoryList[i].disabled = false;
                            if (categoryList[i].pid == 0) {
                                tempCategoryList.push(categoryList[i]);
                            }
                        }
                        this.category.categoryList = tempCategoryList;
                        
                       
                        
                    }
                });
            },
            categoryEditDialog(index, row) {
                this.itemTabsValue = 'default';
                this.category.dialogCategory = true;
                this.category.categoryStatus = true;
                this.category.name = row.name;
                this.category.pid = row.pid;
                this.category.url_name = row.url_name;
                this.category.seo_title = row.seo_title;
                this.category.template = row.template;
                this.category.detail_template = row.detail_template;
                this.category.category_url = row.category_url;
                this.category.category_page_url = row.category_page_url;
                this.category.detail_url = row.detail_url;
                this.category.description = row.description;
                this.category.keywords = row.keywords;
                this.category.id = row.id;
                this.category.isChild = false;
                this.category.dialogCategoryTitle = '编辑分类';
                if(row.pid == 0) {
                    for(var i = 0; i < this.category.categoryList.length; i++) {
                        if(row.id == this.category.categoryList[i].id) {
                            this.category.categoryList[i].disabled = true;
                            if(row.children.length > 0) {
                                this.category.isChild = true;
                            }
                        } else {
                            this.category.categoryList[i].disabled = false;
                        }
                    }
                }
            },
     }
    }
</script>
