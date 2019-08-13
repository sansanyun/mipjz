<style>
	.simditor-body {
	    height: 400px;
	    overflow: auto;
	}
</style>
<template>
<div>
	<i-form>
        <Form-Item>
            <textarea id="article_editor" required="true" class="simditor" style="height: 400px;" placeholder="请输入内容..."></textarea>
        </Form-Item>
    </i-Form>
    <div class="text-center mb-5">
        <button type="button" class="ivu-btn ivu-btn-primary ivu-btn-circle" @click='categoryPost'>
            <span><Icon type="checkmark-round"></Icon> 提交保存</span>
        </button>
    </div>
</div>
</template>

<script>
    export default {
    props: {
        articlePublishCid: String,
    },
	data () {
  		return {
  			itemInfo : '',
  			editor: '',
        	toolbar : ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', 'color', '|', 'ol', 'ul', 'blockquote', 'code', '|', 'link', 'image', 'hr', '|', 'indent', 'outdent', 'alignment', 'html'],
       }
     },
    watch: {
    },
    mounted() {
        this.editorInit();
      	this.getItemInfo();
    },
    methods: {
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
                        siteId: this.$route.params.dataId,
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
        getItemInfo() {
            this.$mip.ajax('{$domain}/article/ApiAdminArticleCategory/categoryInfo', {
                cid: this.articlePublishCid,
            }).then(res => {
                if(res.code == 1) {
                    var itemInfo = res.data.itemInfo;
                    this.itemInfo = itemInfo;
                	this.editor.setValue(itemInfo.content);
                }
            });
        },
        
        categoryPost() {
            this.$mip.ajax('{$domain}/article/ApiAdminArticleCategory/categoryContentEdit', {
                id: this.itemInfo.id,
    			content: this.editor.getValue(),
            }).then(res => {
                if(res.code == 1) {
                    this.$Message.success(res.msg);
                }
            });
        },
        
    }
}
</script>
