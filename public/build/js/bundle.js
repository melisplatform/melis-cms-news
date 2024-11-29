function renderNewsWorkFlowModal(e,s){$.ajax({url:"/melis/MelisSmallBusiness/MelisWorkflow/render-workflow-modal",encode:!0}).done((function(t){$("#melis-modals-container").append(t),$("#melis-modals-container").find(".modal-content").data(e);var a=$(t).find(".modal").attr("id");melisHelper.zoneReload("id_melissb_workflow_modal_content","melissb_workflow_modal_content",e),melisCoreTool.showModal(a),melisCoreTool.done(s)})).fail((function(e,t,a){melisCoreTool.done(s),alert(translations.tr_meliscore_error_message)}));var t=setInterval((function(){$commentBtn=$("#melis-modals-container").find(".btnAddWorkflowComment"),$commentBtn.length&&($commentBtn.removeClass(),$commentBtn.addClass("btn btn-success pull-right btnAddWorkflowCommentNews"),$("#melis-modals-container").find(".btnAddWorkflowCommentNews").length&&clearInterval(t)),$noCommentBtn=$("#melis-modals-container").find(".btnCommentModalClose"),$noCommentBtn.length&&($noCommentBtn.removeClass(),$noCommentBtn.addClass("btn btn-success pull-right btnCommentModalCloseNews"),$("#melis-modals-container").find(".btnCommentModalCloseNews").length&&clearInterval(t))}),300)}function charCounter(e){var s=$(this).val().length,t=$(this).prev("label"),a=e.data.limit;t.find("span").length?0===s?(t.removeClass("limit"),t.find("span").remove()):(t.find("span").html('<i class="fa fa-text-width"></i>('+s+")"),s>a?(t.addClass("limit"),t.find("span").addClass("limit")):(t.removeClass("limit"),t.find("span").removeClass("limit"))):0!==s&&(t.find(".label-text").append("<span class='text-counter-indicator'><i class='fa fa-text-width'></i>("+s+")</span>"),s>a&&(t.addClass("limit"),t.find("span").addClass("limit")))}$((function(){var e=$("body"),s="ask",t=0;e.on("click",".addNews",(function(){var e=translations.tr_meliscmsnews_list_header_title_new;toolNews.tabOpen(e,0)})),e.on("click",".newsEdit",(function(){let e=$(this),s=e.closest("tr").attr("id"),t=e.closest("tr").find("td:nth-child(3)").text();toolNews.tabOpen(t,s)})),e.on("click",".newsListRefresh",(function(){toolNews.refreshTable()})),e.on("click",".newsDelete",(function(){var e=$(this).closest("tr").attr("id"),s=[];s.push({name:"newsId",value:e}),melisCoreTool.pending(".newsDelete"),melisCoreTool.confirm(translations.tr_meliscmsnews_common_label_yes,translations.tr_meliscmsnews_common_label_no,translations.tr_meliscmsnews_common_label_delete_news,translations.tr_meliscmsnews_common_label_delete_confirm,(function(){$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNewsList/deleteNews",data:s,dataType:"json",encode:!0}).done((function(s){s.success?(melisHelper.melisOkNotification(s.textTitle,s.textMessage),toolNews.tabClose(e),melisHelper.zoneReload("id_meliscmsnews_list_content_table","meliscmsnews_list_content_table",{})):melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors),melisCore.flashMessenger()})).fail((function(){alert(translations.tr_meliscore_error_message)}))})),melisCoreTool.done(".newsDelete")})),e.on("click",".removeAttachFile",(function(){var e=$(this),s=e.data("newsid"),t=e.data("column"),a=e.data("type"),n=[];n.push({name:"newsId",value:s}),n.push({name:"column",value:t}),n.push({name:"type",value:a}),melisCoreTool.pending(".removeAttachFile"),melisCoreTool.confirm(translations.tr_meliscmsnews_common_label_yes,translations.tr_meliscmsnews_common_label_no,melisHelper.melisTranslator("tr_meliscmsnews_delete_"+a+"_title"),melisHelper.melisTranslator("tr_meliscmsnews_delete_"+a+"_confirm_msg"),(function(){$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/removeAttachFile",data:n,dataType:"json",encode:!0}).done((function(e){e.success?(melisHelper.melisOkNotification(e.textTitle,e.textMessage),melisHelper.zoneReload(s+"_id_meliscmsnews_content_tabs_properties_details_left_images","meliscmsnews_content_tabs_properties_details_left_images",{newsId:s}),melisHelper.zoneReload(s+"_id_meliscmsnews_content_tabs_properties_details_left_documents","meliscmsnews_content_tabs_properties_details_left_documents",{newsId:s})):melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors),melisCore.flashMessenger()})).fail((function(){alert(translations.tr_meliscore_error_message)}))})),melisCoreTool.done(".removeAttachFile")})),e.on("keyup","#cnews_title",(function(){var e=$(this).closest(".container-level-a").data("newsid"),s=$(this).val(),t=($("#"+e+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .news-post-text-form"),$("#"+e+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs").find("li.active a").attr("data-lang-id"));$("#"+e+"_id_meliscmsnews_content_tabs_seo_details #cnews_"+t).find("#cnews_seo_url").val();""==$("#"+e+"_id_meliscmsnews_content_tabs_seo_details #cnews_"+t).find('input[name="cnews_seo_id"]').val()&&$("#"+e+"_id_meliscmsnews_content_tabs_seo_details #cnews_"+t).find("#cnews_seo_url").val(s.replace(/\s+/g,"-").replace(/[^a-z0-9]+/gi,"-").replace(/^-+/,"").replace(/-+$/,"").toLowerCase())})),e.on("click",".saveNewsLetter",(function(){var s=$(this);if(void 0===s.attr("disabled")){melisCoreTool.pending(".saveNewsLetter");var t=s.closest(".container-level-a"),a=t.find("form#newsLetterForm").serializeArray(),n=t.data("newsid"),i=t.find("select[name=cnews_slider_id]").val(),o=t.find("select[name=cnews_site_id]").val(),l=t.find(`select#${t.data("newsid")}_tags_select`).val(),r=$("#"+n+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .news-post-text-form"),c=0;r.each((function(){var e=$(this);a.push({name:"cnews_title["+c+"]",value:e.find("#cnews_title").val()}),a.push({name:"cnews_subtitle["+c+"]",value:e.find("#cnews_subtitle").val()}),a.push({name:"cnews_lang_id["+c+"]",value:e.data("langId")});for(var s=1;s<=10;s++)a.push({name:"cnews_paragraph"+s+"["+c+"]",value:e.find("textarea[name='cnews_paragraph"+s+"']").val()});var t="";e.find("textarea.editme").each((function(){t=""!=t?t+"-"+$(this).attr("name"):$(this).attr("name")})),a.push({name:"cnews_paragraph_order["+c+"]",value:t}),$("#"+n+"_news_category_area span.news-cat-values").each((function(e){a.push({name:`cnews_categories[${e}][cnc_cat2_id]`,value:$(this).data("cnc-cat2-id")}),a.push({name:`cnews_categories[${e}][cnc_id]`,value:$(this).data("cnc-id")??""}),a.push({name:`cnews_categories[${e}][cnc_order]`,value:e+1})})),a.push({name:"cnews_selected_tags",value:l}),$("#"+n+"_deleted_news_category_area span").each((function(e){$(this).data("cnc-id")&&a.push({name:`cnews_removed_categories[${e}][cnc_id]`,value:$(this).data("cnc-id")})})),c++})),$("#"+n+"_id_meliscmsnews_content_tabs_seo_details .news-seo-form").each((function(e,s){var t=$(this).find("form").attr("name"),n=($("form[name="+t+"]").length,new FormData($(this).find("form")[0]).entries());for(var i of n)a.push({name:"cnews_seo["+e+"]["+i[0]+"]",value:i[1]})})),a.push({name:"formCount",value:r.length}),t.find(".make-switch div").each((function(){var e=$(this),s=e.find("input").attr("name"),t=e.hasClass("switch-on")?1:0;a.push({name:s,value:t})})),a.push({name:"cnews_slider_id",value:i}),a.push({name:"cnews_site_id",value:o}),$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/saveNewsLetter",data:a,dataType:"json",encode:!0}).done((function(s){if(s.success)toolNews.tabClose(n),melisHelper.melisOkNotification(s.textTitle,s.textMessage),toolNews.tabOpen(s.chunk.cnews_title,s.chunk.cnews_id),toolNews.refreshTable();else{if(melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors),melisCoreTool.highlightErrors(s.success,s.errors,s.chunk.cnews_id+"_id_meliscmsnews_page form"),s.errors.hasOwnProperty("cnews_title")){var t=e.find("#"+s.chunk.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs label[for='cnews_title']");t.length&&t.css("color","red")}for(var a=["cnews_seo_meta_title","cnews_seo_meta_description","cnews_seo_canonical","cnews_seo_url","cnews_seo_url_redirect","cnews_seo_url_301"],i=a.length,o=0;o<i;o++)if(s.errors[a[o]]){var l=s.errors[a[o]].label,r=l.substring(l.indexOf("(")+1,l.lastIndexOf(")")),c=$("#"+n+"_id_meliscmsnews_content_tabs_seo_details").find("span:contains("+r+")").parent("a").attr("href");$("#"+s.chunk.cnews_id+"_id_meliscmsnews_content_tabs_seo_details .form-control[name="+a[o]+"]").parents(".form-group").children(":first").css("color","#686868"),$(c+" .form-control[name='"+a[o]+"']").parents(".form-group").children(":first").css("color","red")}}melisCore.flashMessenger(),melisCoreTool.done(".saveNewsLetter")}))}})),e.on("click",".newsAttachFile",(function(){melisCoreTool.pending(".newsAttachFile");var e=$(this),s=e.data("newsid"),t=e.data("filetype");melisHelper.createModal("id_meliscmsnews_modal_documents_form","meliscmsnews_modal_documents_form",!1,{newsId:s,type:t,isNew:1},"/melis/MelisCmsNews/MelisCmsNews/renderModal",(function(){melisCoreTool.done(".newsAttachFile")}))})),e.on("click",".newsAttachImage",(function(){melisCoreTool.pending(".newsAttachImage");var e=$(this),s=e.data("newsid"),t=e.data("filetype");melisHelper.createModal("id_meliscmsnews_modal_documents_form_image","meliscmsnews_modal_documents_form_image",!1,{newsId:s,type:t,isNew:1},"/melis/MelisCmsNews/MelisCmsNews/renderModal",(function(){melisCoreTool.done(".newsAttachImage")}))})),e.on("click",".newsEditImage",(function(){melisCoreTool.pending(".newsAttachImage");var e=$(this),s=e.data("newsid"),t=e.data("filetype"),a=e.data("column");melisHelper.createModal("id_meliscmsnews_modal_documents_form_image","meliscmsnews_modal_documents_form_image",!1,{newsId:s,type:t,isNew:0,column:a},"/melis/MelisCmsNews/MelisCmsNews/renderModal",(function(){melisCoreTool.done(".newsAttachImage")}))})),e.on("click","#newsAttachment",(function(){var e=$(this);melisCoreTool.pending(e);var s=$("#newsFileForm").get(0),t=new FormData(s);$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/saveFileForm",data:t,dataType:"json",processData:!1,cache:!1,contentType:!1,encode:!0,xhr:function(){var e=$.ajaxSettings.xhr();return e.upload&&e.upload.addEventListener("progress",toolNews.progress,!1),e}}).done((function(s){s.success?("image"===s.chunk.type?toolNews.imageModal(s.chunk):toolNews.fileModal(s.chunk),melisHelper.melisOkNotification(s.textTitle,s.textMessage,"#72af46"),melisCore.flashMessenger()):melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors,"closeByButtonOnly"),$("div.progressContent").addClass("hidden"),melisCoreTool.done(e)})).fail((function(s,t){alert(translations.tr_meliscore_error_message),melisCoreTool.done(e)}))})),e.on("change","select[name=cnews_site_id]",(function(){var e=$(this).parents().eq(6).find("table").attr("id");$("#"+e).DataTable().ajax.reload()})),e.on("change",".mcnews-page-details",(function(){var s=$(this),t=this.value,a=s.data();if(void 0!==a.newsId&&a.newsId>0&&void 0!==a.nameSpace.length&&a.nameSpace.length>0&&void 0!==t&&t>0){var n="/id/"+t+"/preview?melisSite="+a.nameSpace+"&newsId="+a.newsId+"&renderMode=previewtab",i=e.find("#"+a.newsId+"-see-below"),o=e.find("#"+a.newsId+"-see-in-new-tab");i.attr({"data-uri":n,disabled:!1}),o.attr({href:n,disabled:!1})}})),e.on("click",".preview-tab-see-below",(function(){var e=this.id,s=this.dataset;void 0!==s.uri&&s.uri&&(e=e.split("-")[0])&&melisHelper.zoneReload(e+"_id_meliscmsnews_tabs_preview_iframe","meliscmsnews_tabs_preview_iframe",{newsId:e,newsUri:s.uri})})),e.on("click",".comments",(function(){var e=$(this).attr("href"),s=$(e);melisCore.screenSize>768&&s.find(".comments-table-refresh").trigger("click")})),$(window).on("scroll",(function(){var e=activeTabId.split("_")[0],s=$("#"+e+"_news-preview-iframe-container");if(!0===s.is(":visible")){var t=s.find("iframe"),a=t.height();0===s.find(".overlay-loader").length&&a<=800&&t.height(t.contents().height())}})),e.on("keyup keydown change","form[name='newsSeoForm'] input[name='cnews_seo_meta_title']",{limit:65},charCounter),e.on("keyup keydown change","form[name='newsSeoForm'] textarea[name='cnews_seo_meta_description']",{limit:255},charCounter),e.on("click",".news-details-workflow",(function(){s="ask";var e=$(this);if(null==e.attr("disabled")){var a=e.data();melisCoreTool.pending(".news-details-workflow"),t=a.wfId,renderNewsWorkFlowModal(a,".news-details-workflow")}})),e.on("click",".news-workflow",(function(){s="ask";var e=$(this);if(null==e.attr("disabled")){melisCoreTool.pending(".news-workflow");var a=e.closest("tr"),n=a.attr("id");t=n;var i=a.find("td:eq(2)").text();renderNewsWorkFlowModal({wfDetails:i+" ("+n+")",wfId:n,wfOpeningJs:"melisHelper.tabOpen('"+i+"', 'fa fa-rss fa-2x',  '"+n+"_id_meliscmsnews_page', 'meliscmsnews_page', { newsId: "+n+" });",wfType:"NEWS"},".news-workflow")}})),e.on("click",".workflow-modal-cont .btn-validate-refuse",(function(){s=$(this).hasClass("pw-validate")?"validate":"refuse"})),e.on("click",".btnAddWorkflowCommentNews, .btnCommentModalCloseNews",(function(e){var a=t,n="workflow-comments-modal form#idmelissbpagecomments",i=s;$(this).hasClass("btnAddWorkflowCommentNews")||$("#id_pcom_text").val("");var o=$("#"+n).serializeArray();o.push({name:"pcom_news_id",value:a}),o.push({name:"action",value:i}),o=$.param(o),melisCoreTool.pending(".btnAddWorkflowCommentNews"),$.ajax({type:"POST",url:"/melis/MelisSmallBusiness/MelisWorkflow/addWorkflowComments",data:o,dataType:"json",encode:!0}).done((function(e){e.success?(melisHelper.zoneReload(a+"_id_meliscmsnews_center_workflow_tabs_comments_timeline","meliscmsnews_workflow_comments_timeline",{newsId:a}),melisCoreTool.clearForm(n),melisCoreTool.hideModal("workflowCommentModal"),melisCoreTool.hideModal("pageWorkflowModal")):(melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors,0),melisCoreTool.highlightErrors(e.success,e.errors,n)),melisCore.flashMessenger(),melisCoreTool.done(".btnAddWorkflowCommentNews")})).fail((function(){alert(translations.tr_meliscore_error_message)}))})),e.on("click",".btnAddNewsComment",(function(e){var s=$(this),t=s.data("newsid"),a=t+"_id_meliscmsnews_center_page_tabs_comments_add form#idmelissbpagecomments",n=$("#"+a).serializeArray();n.push({name:"pcom_news_id",value:t}),n=$.param(n),s.prop("disabled",!0),$.ajax({type:"POST",url:"/melis/MelisSmallBusiness/PageComments/addComment",data:n,dataType:"json",encode:!0}).done((function(e){e.success?(melisHelper.zoneReload(t+"_id_meliscmsnews_center_workflow_tabs_comments_timeline","meliscmsnews_workflow_comments_timeline",{newsId:t}),melisCoreTool.clearForm(a)):(melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors),melisCoreTool.highlightErrors(e.success,e.errors,a)),melisCore.flashMessenger()})).fail((function(){alert(translations.tr_meliscore_error_message)}))})),e.on("click",".newsCategoryList",(function(e){const s=$(this);s.attr("disabled",!0);const t=s.data("newsid");melisHelper.createModal("id_meliscmsnews_content_tabs_properties_details_left_categories_modal","meliscmsnews_content_tabs_properties_details_left_categories_modal",!1,{newsId:t},"/melis/MelisCmsNews/MelisCmsNews/renderNewsCategoryModal",(function(){s.attr("disabled",!1)}))})),e.on("keyup","#newsCategorySearch",(function(e){var s=$(this),t=s.data("newsid"),a=s.val();$("#"+t+"_newsCategoryList").jstree("search",a)})),e.on("click","#clearNewsCatSearchInputBtn",(function(e){var s=$(this).data("newsid"),t=$("#"+s+"_newsCategoryList");categoryOpeningItemFlag=!1,$("#newsCategorySearch").val(""),t.jstree("search","")})),e.on("click","#expandNewsCatTreeViewBtn",(function(e){var s=$(this).data("newsid");$("#"+s+"_newsCategoryList").jstree("open_all")})),e.on("click","#collapseNewsCatTreeViewBtn",(function(e){var s=$(this).data("newsid");newsCatTree=$("#"+s+"_newsCategoryList"),newsCatTree.jstree("close_all")})),e.on("click","#refreshNewsCatTreeView",(function(e){var s=$(this).data("newsid"),t=$("#"+s+"_newsCategoryList");t.jstree(!0).refresh("forget_state",!0),t.jstree("search",""),$("#newsCategorySearch").val("")})),e.on("click",".newsDelCat",(function(){var e=$(this).parent().parent();$("#"+activeTabId.split("_")[0]+"_deleted_news_category_area").append('<span data-cnc-id="'+e.data("cnc-id")+'" data-cnc-cat2-id="'+e.data("cnc-cat2-id")+'"></span>'),e.fadeOut("slow").remove(),0===$(".newsDelCat").length&&$("p#"+activeTabId.split("_")[0]+"_no_categories").show()})),e.on("click",".news-category-tree-view-lang li a",(function(){var e=$(this),s=e.text(),t=e.data("locale"),a=$(".newsCategoryLangDropdown").data("newsid");$(".newsCategoryLangDropdown span.filter-key").text(s),initNewsCategoryList(a,t)})),e.on("click",".addNewsCategory",(function(){var e=$(this).data("newsid"),s=new Array;$.each($("#"+e+"_newsCategoryList").jstree().get_checked(!0),(function(){s.push(parseInt(this.original.cat2_id))}));var t=new Array,a=new Array;$("#"+e+"_news_category_area span[data-cnc-cat2-id]").each((function(){var e=$(this),n=parseInt(e.data("cnc-cat2-id"));-1!==s.indexOf(n)?a.push(n):(t.push(n),$("span.news-cat-values[data-cnc-cat2-id='"+n+"']").remove())})),$.each($("#"+e+"_newsCategoryList").jstree().get_checked(!0),(function(){var s=parseInt(this.original.cat2_id);if(-1===t.indexOf(s)&&-1===a.indexOf(s)){var n=this.text;$.get("/melis/MelisCmsNews/MelisCmsNews/getNewsCategoryLastOrderNum",{catId:s,newsId:e},(function(t){$("#"+e+"_news_category_area").append('<span class="news-cat-values" data-cnc-id="'+t.id+'" data-cnc-cat2-id="'+s+'" data-cnc-order="'+t.order+'"><span class="category-value">'+n+'<i class="newsDelCat fa fa-times"></i></span></span>')}))}})),s.length?$("p#"+e+"_no_categories").hide():$("p#"+e+"_no_categories").show(),$("#id_meliscmsnews_content_tabs_properties_details_left_categories_modal_container").modal("hide")}))}));var toolNews={refreshTable:function(){melisHelper.zoneReload("id_meliscmsnews_list_content_table","meliscmsnews_list_content_table",{})},tabOpen:function(e,s){melisHelper.tabOpen(e,"fa fa-list-alt",s+"_id_meliscmsnews_page","meliscmsnews_page",{newsId:s},"id_meliscmsnews_left_menu")},tabClose:function(e){melisHelper.tabClose(e+"_id_meliscmsnews_page")},imageModal:function(e){melisCoreTool.hideModal("id_meliscmsnews_modal_documents_form_image_container"),melisHelper.zoneReload(e.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_images","meliscmsnews_content_tabs_properties_details_left_images",{newsId:e.cnews_id})},fileModal:function(e){melisCoreTool.hideModal("id_meliscmsnews_modal_documents_form_image_container"),melisCoreTool.hideModal("id_meliscmsnews_modal_documents_form_container"),melisHelper.zoneReload(e.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_documents","meliscmsnews_content_tabs_properties_details_left_documents",{newsId:e.cnews_id})},trimLength:function(e){return(e=e.trim()).length>15?(e=e.substring(0,12)).substring(0,e.lastIndexOf(" "))+"...":e},progress:function(e){var s=$("div.progressContent");s.removeClass("hidden");var t=$("div.progressContent > div.progress > div.progress-bar");t.attr("aria-valuenow",0),t.css("width","0%");var a=$("div.progressContent > div.progress > span.status");if(a.html(""),e.lengthComputable){var n=e.total,i=100*e.loaded/n;t.attr("aria-valuenow",i),t.css("width",i+"%"),i>100?s.addClass("hidden"):a.html(Math.round(i)+"%")}}};window.newsImagePreview=function(e,s){if(s.files&&s.files[0]){var t=new FileReader;t.onload=function(s){$(e).attr("src",s.target.result)},t.readAsDataURL(s.files[0])}},window.initNewsList=function(e){var s=$("#newsSiteSelect");s.length&&(e.cnews_site_id=s.val())},window.initNewsCategoryList=function(e,s){if(void 0===s&&(s=melisLangId),$("#"+e+"_newsCategoryList").length){var t=$("#"+e+"_newsCategoryList");t.data("langlocale",s),t.jstree("destroy");var a=new Array;if(a.push({name:"newsId",value:e}),a.push({name:"langlocale",value:t.data("langlocale")}),e){const e=$("#"+activeTabId).find("select[name=cnews_site_id]").val();a.push({name:"siteId",value:e})}var n=new Array;$("div#"+e+"_news_category_area > span.news-cat-values").each((function(){n.push($(this).data("cnc-cat2-id"))})),a=$.param(a),t.on("loading.jstree",(function(e,s){toolNews.pendingZoneStart("newsCategorySearchZone")})).on("loaded.jstree",(function(e,s){toolNews.pendingZoneDone("newsCategorySearchZone")})).on("ready.jstree",(function(){n.forEach((function(e){t.jstree("check_node",e)}))})).jstree({types:{default:{icon:"fa fa-circle text-success"},selected:{select_node:!1}},core:{check_callback:!0,animation:500,themes:{name:"default",responsive:!1},dblclick_toggle:!1,data:{cache:!0,url:"/melis/MelisCmsNews/MelisCmsNews/getCategoryTreeView?"+a}},checkbox:{three_state:!1,whole_node:!1,tie_selection:!1},plugins:["search","changed","types","checkbox"]})}},function(e){"use strict";e.fn.bootstrapSwitch=function(s){var t='input[type!="hidden"]',a={init:function(){return this.each((function(){var s,a,n,i,o,l,r=e(this),c=r.closest("form"),d="",m=r.attr("class"),_="ON",w="OFF",f=!1,h=!1;e.each(["switch-mini","switch-small","switch-large"],(function(e,s){m.indexOf(s)>=0&&(d=s)})),r.addClass("has-switch"),void 0!==r.data("on")&&(o="switch-"+r.data("on")),void 0!==r.data("on-label")&&(_=r.data("on-label")),void 0!==r.data("off-label")&&(w=r.data("off-label")),void 0!==r.data("label-icon")&&(f=r.data("label-icon")),void 0!==r.data("text-label")&&(h=r.data("text-label")),a=e("<span>").addClass("switch-left").addClass(d).addClass(o).html(_),o="",void 0!==r.data("off")&&(o="switch-"+r.data("off")),n=e("<span>").addClass("switch-right").addClass(d).addClass(o).html(w),i=e("<label>").html("&nbsp;").addClass(d).attr("for",r.find(t).attr("id")),f&&i.html('<i class="icon '+f+'"></i>'),h&&i.html(""+h),s=r.find(t).wrap(e("<div>")).parent().data("animated",!1),!1!==r.data("animated")&&s.addClass("switch-animate").data("animated",!0),s.append(a).append(i).append(n),r.find(">div").addClass(r.find(t).is(":checked")?"switch-on":"switch-off"),r.find(t).is(":disabled")&&e(this).addClass("deactivate");var p=function(e){r.parent("label").is(".label-change-switch")||e.siblings("label").trigger("mousedown").trigger("mouseup").trigger("click")};r.on("keydown",(function(s){32===s.keyCode&&(s.stopImmediatePropagation(),s.preventDefault(),p(e(s.target).find("span:first")))})),a.on("click",(function(s){p(e(this))})),n.on("click",(function(s){p(e(this))})),r.find(t).on("change",(function(s,t){var a=e(this),n=a.parent(),i=a.is(":checked"),o=n.is(".switch-off");if(s.preventDefault(),n.css("left",""),o===i){if(i?n.removeClass("switch-off").addClass("switch-on"):n.removeClass("switch-on").addClass("switch-off"),!1!==n.data("animated")&&n.addClass("switch-animate"),"boolean"==typeof t&&t)return;n.parent().trigger("switch-change",{el:a,value:i})}})),r.find("label").on("mousedown touchstart",(function(s){var t=e(this);l=!1,s.preventDefault(),s.stopImmediatePropagation(),t.closest("div").removeClass("switch-animate"),t.closest(".has-switch").is(".deactivate")||t.closest(".switch-on").parent().is(".radio-no-uncheck")?t.off("click"):(t.on("mousemove touchmove",(function(s){var t=e(this).closest(".make-switch"),a=((s.pageX||s.originalEvent.targetTouches[0].pageX)-t.offset().left)/t.width()*100;l=!0,a<25?a=25:a>75&&(a=75),t.find(">div").css("left",a-75+"%")})),t.on("click touchend",(function(s){var t=e(this),a=e(s.target).siblings("input");s.stopImmediatePropagation(),s.preventDefault(),t.off("mouseleave"),l?a.prop("checked",!(parseInt(t.parent().css("left"))<-25)):a.prop("checked",!a.is(":checked")),l=!1,a.trigger("change")})),t.on("mouseleave",(function(s){var t=e(this),a=t.siblings("input");s.preventDefault(),s.stopImmediatePropagation(),t.off("mouseleave"),t.trigger("mouseup"),a.prop("checked",!(parseInt(t.parent().css("left"))<-25)).trigger("change")})),t.on("mouseup",(function(s){s.stopImmediatePropagation(),s.preventDefault(),e(this).off("mousemove")})))})),"injected"!==c.data("bootstrapSwitch")&&(c.on("reset",(function(){setTimeout((function(){c.find(".make-switch").each((function(){var s=e(this).find(t);s.prop("checked",s.is(":checked")).trigger("change")}))}),1)})),c.data("bootstrapSwitch","injected"))}))},toggleActivation:function(){var s=e(this);s.toggleClass("deactivate"),s.find(t).prop("disabled",s.is(".deactivate"))},isActive:function(){return!e(this).hasClass("deactivate")},setActive:function(s){var a=e(this);s?(a.removeClass("deactivate"),a.find(t).prop("disabled",!1)):(a.addClass("deactivate"),a.find(t).attr("disabled","disabled"))},toggleState:function(s){var t=e(this).find(":checkbox");t.prop("checked",!t.is(":checked")).trigger("change",s)},toggleRadioState:function(s){var t=e(this).find(":radio");t.not(":checked").prop("checked",!t.is(":checked")).trigger("change",s)},toggleRadioStateAllowUncheck:function(s,t){var a=e(this).find(":radio");s?a.not(":checked").trigger("change",t):a.not(":checked").prop("checked",!a.is(":checked")).trigger("change",t)},setState:function(s,a){e(this).find(t).prop("checked",s).trigger("change",a)},setOnLabel:function(s){e(this).find(".switch-left").html(s)},setOffLabel:function(s){e(this).find(".switch-right").html(s)},setOnClass:function(s){var t=e(this).find(".switch-left"),a="";void 0!==s&&(void 0!==e(this).attr("data-on")&&(a="switch-"+e(this).attr("data-on")),t.removeClass(a),a="switch-"+s,t.addClass(a))},setOffClass:function(s){var t=e(this).find(".switch-right"),a="";void 0!==s&&(void 0!==e(this).attr("data-off")&&(a="switch-"+e(this).attr("data-off")),t.removeClass(a),a="switch-"+s,t.addClass(a))},setAnimated:function(s){var a=e(this).find(t).parent();void 0===s&&(s=!1),a.data("animated",s),a.attr("data-animated",s),!1!==a.data("animated")?a.addClass("switch-animate"):a.removeClass("switch-animate")},setSizeClass:function(s){var t=e(this),a=t.find(".switch-left"),n=t.find(".switch-right"),i=t.find("label");e.each(["switch-mini","switch-small","switch-large"],(function(e,t){t!==s?(a.removeClass(t),n.removeClass(t),i.removeClass(t)):(a.addClass(t),n.addClass(t),i.addClass(t))}))},status:function(){return e(this).find(t).is(":checked")},destroy:function(){var s,t=e(this),a=t.find("div"),n=t.closest("form");return a.find(":not(input)").remove(),(s=a.children()).unwrap().unwrap(),s.off("change"),n&&(n.off("reset"),n.removeData("bootstrapSwitch")),s}};return a[s]?a[s].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof s&&s?void e.error("Method "+s+" does not exist!"):a.init.apply(this,arguments)}}(jQuery);
