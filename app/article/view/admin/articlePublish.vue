<style>
	.simditor-body {
	    height: 400px;
	    overflow: auto;
	}
	.publish {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 50px;
        padding-left: 150px;
        left: 0;
        z-index: 9999;
        background-color: #fff;
    }
    .ivu-upload-list {
    	margin-top: 0;
    }
</style>
<template>
<div class="publish">
    <header class="mipcms-container-header clearfix">
        <div class="header-group">
            <h4 class="title">
                <Button type="text" size='small' @click='articlePublishBack'><Icon type="ios-arrow-back" size='20' /></Button>
                <span class="text">文章</span>
            </h4>
            <h5 class="sub-title">{{articlePublishUuid? "编辑" : "发布"}}</h5>
        </div>
        <div class="float-right">
            <Button class="mr-3" shape="circle" icon="md-close" @click='articlePublishBack'>关闭</Button>
            <button type="button" class="float-right ivu-btn ivu-btn-primary ivu-btn-circle" @click='publishPost'>
                <span><Icon type="md-checkmark" /> 提交保存</span>
            </button>
        </div>
    </header>
    <main class="mipcms-container-body" style="height: calc(100% - 62px)">
        <section class="">
            <section class="mip-box">
                <section class="mip-box-body">
                     <i-Form :label-width="80" class="mb-3 v-h" :class='vShow'>
                         
                        <Form-Item label="标题" class="ivu-form-item-required">
                            <i-Input style="width: 500px;" v-model="title" placeholder="请输入标题..."></i-Input>
                        </Form-Item>
                       	<Form-Item label="描述">
                            <i-Input type="textarea" style="width: 500px;" v-model="description" placeholder="请输入描述..."></i-Input>
                        </Form-Item>
                        <Form-Item label="关键词">
                            <i-Input type="textarea" style="width: 500px;" v-model="keywords" placeholder="请输入关键词..."></i-Input>
                        </Form-Item>
                        <Form-Item label="URl别名" v-if='siteGlobal.siteInfo.diyUrlStatus'>
                            <i-input v-model="url_name"  style="width: 500px;" placeholder="输入英文字母"><i-Button slot="append" class="tags-btn" @click='getPinyin'>获取拼音</i-Button></i-input>
                        </Form-Item>
                        <Form-Item v-for='item in fieldList' :label="item.name" >
                              <i-Input type='textarea' contentEditable='true' v-model="item.value" style="width: 500px;"></i-Input>
                        </Form-Item>
                        
                        <Form-Item label="封面">
                            <i-Input style="width: 500px;" v-model="imgUrl" placeholder="缩略图可以直接添加">
                                <Upload slot="append"  :show-upload-list='false' :on-success="handleSuccessImg" :format="['jpg','png','gif','jpeg']" name='fileDataFileName'
                                    action="/setting/ApiAdminUpload/defaultImgUpload">
                                    <i-button  icon="ios-cloud-upload-outline">上传缩略图</i-Button>
                                </Upload>
                            </i-Input>
                        </Form-Item>
                         
                        <Form-Item label="分类" prop="title">
                            <Cascader :data="categoryList" transfer v-model="cidArray"  style="width: 300px;"></Cascader>
                        </Form-Item> 
                        <Form-Item label="标签" prop="title">
                            <div class="m-b-sm">
                                <i-Input transfer filterable v-model='tagName' style="width: 300px;"  placeholder="请选择标签,或直接输入回车即可" @on-focus='tagsSearch' @on-blur='timeShowList' @on-click="tagsClick($event)" @on-keyup="enterTags($event)">
                                     <i-Button slot="append" class="tags-btn" @click='btnTags'><Icon type="md-checkmark" /></i-Button>
                                </i-Input>
                                <div class="ivu-select-dropdown ivu-auto-complete" v-show='isShowTagsList' style="width: 300px;">
                                    <ul class="ivu-select-dropdown-list">
                                        <li class="ivu-select-item" v-for="item in tagsList" :value="item" :key="item" @click='tagsClick(item)'>{{item}}</li>
                                    </ul>
                                </div>
                            </div>
                            <Tag class='m-t-xs' closable v-for="item in selectedTagsList" :key="item" :name='item' @on-close="selectedTagsListClose">{{item}}</Tag>
                        </Form-Item> 
                        <Form-Item label="内容" class="ivu-form-item-required">
                            <textarea id="article_editor" class="simditor" style="height: 400px;" placeholder="请输入内容..."></textarea>
                        </Form-Item>
                        
                    </i-Form>
                    <Spin size="large" fix v-if="spinShow"></Spin>
                    <div class="text-center mb-5">
                        <button type="button" class="ivu-btn ivu-btn-primary ivu-btn-circle" @click='publishPost'>
                            <span><Icon type="checkmark-round"></Icon> 提交保存</span>
                        </button>
                    </div>
                </section>
            </section>
        </section>
    </main>
    
</div>
</template>

<script>
    export default {
    props: {
        articlePublishUuid: String,
        articlePublishCid: String,
    },
     data () {
       return {
            vShow: '',
            spinShow: true,
            articlePublishUuid: '',
            uuid: '',
            title: '',
            keywords: '',
            description: '',
            link_tags: '',
            categoryList: [],
            cid: '',
            cidArray: [],
            is_recommend: '',
            content: '',
            url_name: '',
            
            fieldList: [],
            
            ruleValidate: {
                title: [
                    { required: true, message: '请输入标题', trigger: 'blur' }
                ],
            },
            
            tagsList: [],
            tagsListDefault: [],
            tagName: '',
            selectedTagsList: [], //已选中的标签列表
            isShowTagsList: false, //标签下拉状态
            toolbar : ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', 'color', '|', 'ol', 'ul', 'blockquote', 'code', '|', 'link', 'image', 'hr', '|', 'indent', 'outdent', 'alignment', 'html'],
            uploadUrl: '',
            
            downUrl:'',
            imgUrl: '',
            stockNum: 1,
            
       }
     },
    watch: {
    },
    mounted() {
        if (this.articlePublishCid) {
            this.cid = this.articlePublishCid;
        }
        this.getTags();
        this.editorInit();
        this.getFieldList();
        this.getCategoryList();
    },
    methods: {
        handleSuccessImg(res) {
           this.imgUrl = res.data;
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
            document.getElementById('article_editor').innerHTML = '';
            this.editor = new Simditor({
                textarea: document.getElementById('article_editor'),
                toolbar: _this.toolbar,
                upload: {
                    url: this.uploadUrl,
                    params: {
                        type: 'article',
                        siteId: '',
                    },
                    fileKey: 'fileDataFileName',
                    connectionCount: 3,
                    leaveConfirm: '正在上传文件'
                },
                pasteImage: true,
                autosave: 'editor-content'
            });
            this.vShow = 'v-s';
            this.spinShow = false;
        },
        getPinyin: function() {
            if (!this.title) {
                this.$Message.error('请输入名称');
                return false;
            }
            this.$mip.ajax('{$domain}/setting/ApiAdminTool/getPinyin', {
                words: this.title,
                type: '',
            }).then(res => {
                if(res.code == 1) {
                    this.url_name = res.data;
                }
            });
        },
        getFieldList: function() {
                var _this = this;
                this.$mip.ajax('{$domain}/article/ApiAdminArticleDiy/itemFieldList', {
                }).then(function (res) {
                    if (res.code == 1) {
                        var fieldList = res.data;
                        var tempFieldList = [];
                        if (fieldList) {
                            for (var i = 0; i < fieldList.length; i++) {
                                tempFieldList.push({value:'',key:fieldList[i].value, name: fieldList[i].name});
                            }
                        }
                        _this.fieldList = tempFieldList;
                    }
                });
            },
        getCategoryList: function() {
            var _this = this;
            this.$mip.ajax('{$domain}/article/ApiAdminArticleCategory/categoryList', {
                
            }).then(res => {
                if(res.code == 1) {
                    var categoryList = res.data.categoryList;
                    if (this.cid) {
                        var tempCidArray = [];
                        for (var i = 0; i < categoryList.length; i++) {
                            if (this.cid == categoryList[i].id) {
                                tempCidArray.push(categoryList[i].id);
                            } else {
                                if (categoryList[i].children && categoryList[i].children.length) {
                                    for (var g = 0; g < categoryList[i].children.length; g++) {
                                        if (this.cid == categoryList[i].children[g].id) {
                                            tempCidArray.push(categoryList[i].id);
                                            tempCidArray.push(categoryList[i].children[g].id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    this.cidArray = tempCidArray;
                    this.categoryList = categoryList;
                    if (this.articlePublishUuid) {
                        this.getItemInfo();
                    }
                }
            });
        },
        //编辑时
        getItemInfo() {
            this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemFind', {
                uuid: this.articlePublishUuid,
            }).then(res => {
                if(res.code == 1) {
                    var itemInfo = res.data;
                    this.keywords = itemInfo.keywords;
                    this.description = itemInfo.description;
                    this.link_tags = itemInfo.link_tags;
                    this.uuid = itemInfo.uuid;
                    this.title = itemInfo.title;
                    this.url_name = itemInfo.url_name;
                    this.cid = itemInfo.cid;
                    this.imgUrl = itemInfo.img_url;
                    this.downUrl = itemInfo.down_url;
                    this.stockNum = itemInfo.stock_num;
                    for(var i = 0; i < this.fieldList.length; i++) {
                        this.fieldList[i].value = itemInfo[this.fieldList[i].key];
                    }
                    this.is_recommend = itemInfo.is_recommend == 0 ? false : true;
                    this.tags = [];
                    this.publish_time = itemInfo.publish_time ? new Date(parseInt(itemInfo.publish_time) * 1000) : '';
                    this.editor.setValue(itemInfo.content);
                 	this.getItemTags(itemInfo.uuid);
                    
                }
            });
        },
        getItemTags: function(itemId) {
            this.$mip.ajax('{$domain}/tag/ApiAdminItemTag/itemTagsSelectByItem', {
                'itemType': 'article',
                'itemId': itemId,
            }).then(res => {
                if(res.code == 1) {
                    var tagsList = res.data.itemList;
                    if(tagsList.length > 0) {
                        for(var i = 0; i < tagsList.length; i++) {
                            tagsList[i].name = tagsList[i].tags.name;
                            this.selectedTagsList.push(tagsList[i].name);
                        }
                    }
                }
            });
        },
        //开始发布
        publishPost: function(param) {
            this.content = this.editor.getValue();
            var timestamp = Date.parse(this.publish_time);
            timestamp = timestamp / 1000;
            if(this.articlePublishUuid) {
                this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemEdit', {
                    uuid: this.uuid,
                    title: this.title,
                    keywords: this.keywords,
                    description: this.description,
                    link_tags:this.link_tags,
                    url_name: this.url_name,
                    content: this.content,
                    cid: this.cidArray[this.cidArray.length - 1],
                    is_recommend: this.is_recommend ? '1' : '0',
                    tags: this.selectedTagsList.join(','),
                    publish_time: timestamp ? timestamp : '',
                    fieldList: JSON.stringify(this.fieldList),
                    img_url : this.imgUrl,
                    
                }).then(res => {
                    if(res.code == 1) {
                        this.$Message.success(res.msg);
                        this.editor.setValue('');
                        this.articlePublishBack();
                    }
                });
            } else {
                var tempField = this.fieldList;
                this.$mip.ajax('{$domain}/article/ApiAdminArticle/itemAdd', {
                    title: this.title,
                    keywords: this.keywords,
                    description: this.description,
                    link_tags:this.link_tags,
                    url_name: this.url_name,
                    content: this.content,
                    cid: this.cidArray[this.cidArray.length - 1],
                    is_recommend: this.is_recommend ? '1' : '0',
                    tags: this.selectedTagsList.join(','),
                    publish_time: timestamp ? timestamp : '',
                    fieldList: JSON.stringify(tempField),
                    img_url : this.imgUrl,
                }).then(res => {
                    if(res.code == 1) {
                        if(res.data) {
                            var resMIp = JSON.parse(res.data);
                            this.$Message.success('推送成功：' + resMIp.success_mip + '条，当天剩余条数：' + resMIp.remain_mip);
                        }
                        this.editor.setValue('');
                        this.$Message.success(res.msg);
                        this.articlePublishBack();
                    }
                });
            }

        },
        //关闭当前发布弹出层
        articlePublishBack() {
            if(this.editor.getValue()) {
                this.$emit('articlePublishValue', 'close');
            } else {
                this.$emit('articlePublishValue', 'closePublish');
            }
        },
    
        getTags() {
            this.$mip.ajax('{$domain}/tag/ApiAdminTag/itemList', {
                limit: 10,
                orderBy: 'relevance_num',
            }).then(res => {
                if(res.code == 1) {
                    var tagsList = res.data.itemList;
                    this.tagsListDefault = [];
                    if (tagsList.length) {
                        for (var i = 0; i < tagsList.length; i++) {
                            this.tagsListDefault.push(tagsList[i].name);
                        }
                    }
                }
            });
        },
        //标签tags
        tagsListFiltr(list) {
            //去除原来的tags中含有的已经确认的tags
            if (this.selectedTagsList.length) {
                var tempList = [];
                for (var i = 0; i < list.length; i++) {
                       tempList.push(list[i]);
                }
                for (var i = 0; i < this.selectedTagsList.length; i++) {
                    for (var j = 0; j < tempList.length; j++) {
                           if (this.selectedTagsList[i] == tempList[j]) {
                               tempList.splice(j,1);
                           }
                    }
                }
            } else {
                tempList = list;
            }
            if (tempList.length) {
                this.isShowTagsList = true; //开启下拉
            } else {
                this.isShowTagsList = false; //搜索不到 取消下拉
            }
            return tempList;
        },
        //tags搜索
        tagsSearch(val) {
            if (this.tagName) {
                var tempTagsList = this.tagsListDefault;
                var tempTagsListTwo = [];
                for (var i = 0; i < tempTagsList.length; i++) {
                       if (tempTagsList[i].indexOf(this.tagName) != -1) {
                           tempTagsListTwo.push(tempTagsList[i]);
                       }
                }
                if (tempTagsListTwo.length) {
                    this.tagsList = this.tagsListFiltr(tempTagsListTwo);
                } else {
                    this.isShowTagsList = false; //搜索不到 取消下拉
                }
            } else {
                this.tagsList = this.tagsListFiltr(this.tagsListDefault); //空的时候 默认
            }
        },
        //tags按钮确认
        btnTags: function() {
            if (this.tagName) {
                //支持,号空格分割
                var tempSubTagName = this.tagName.split(',');
                if (tempSubTagName) {
                    for (var j = 0; j < tempSubTagName.length; j++) {
                        if (tempSubTagName[j]) {
                            if (this.selectedTagsList.indexOf(tempSubTagName[j]) == -1) {
                                this.selectedTagsList.push(tempSubTagName[j]);
                            }
                        }
                    }     
                                
                }
                this.tagName = ''; //将input值为空
                this.isShowTagsList = false; //取消下拉
            }
        },
        //tags回车
        enterTags: function(ev) {
            if(ev.keyCode == 13) {
                if (this.tagName) {
                	var tempSubTagName = this.tagName.split(',');
                    if (tempSubTagName) {
                        for (var j = 0; j < tempSubTagName.length; j++) {
                            if (tempSubTagName[j]) {
                                if (this.selectedTagsList.indexOf(tempSubTagName[j]) == -1) {
                                    this.selectedTagsList.push(tempSubTagName[j]);
                                }
                            }
                        }     
                                    
                    }
                    this.tagName = ''; //将input值为空
                    this.isShowTagsList = false; //取消下拉
                }
            } else {
                this.tagsSearch();
            }
        },
        timeShowList() {
            var _this = this;
            setTimeout(function(){
                _this.isShowTagsList = false;
            },200)
            
        },
        //下拉tags点击
        tagsClick(val) {
            console.log(val);
            if (this.selectedTagsList.indexOf(val) == -1) {
                this.selectedTagsList.push(val);
            }
            this.tagName = ''; //将input值为空
            this.isShowTagsList = false; //取消下拉
        },
        //取消已经选择tags
        selectedTagsListClose(event, name) {
            const index = this.selectedTagsList.indexOf(name);
            this.selectedTagsList.splice(index, 1);
        },
            
    }
}
</script>
