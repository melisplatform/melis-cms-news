function renderNewsWorkFlowModal(e,s){$.ajax({url:"/melis/MelisSmallBusiness/MelisWorkflow/render-workflow-modal",encode:!0}).done((function(t){$("#melis-modals-container").append(t),$("#melis-modals-container").find(".modal-content").data(e);var i=$(t).find(".modal").attr("id");melisHelper.zoneReload("id_melissb_workflow_modal_content","melissb_workflow_modal_content",e);bootstrap.Modal.getOrCreateInstance("#"+i,{show:!0}).show(),melisCoreTool.done(s)})).fail((function(e,t,i){melisCoreTool.done(s),alert(translations.tr_meliscore_error_message)}));var t=setInterval((function(){$commentBtn=$("#melis-modals-container").find(".btnAddWorkflowComment"),$commentBtn.length&&($commentBtn.removeClass(),$commentBtn.addClass("btn btn-success pull-right btnAddWorkflowCommentNews"),$("#melis-modals-container").find(".btnAddWorkflowCommentNews").length&&clearInterval(t)),$noCommentBtn=$("#melis-modals-container").find(".btnCommentModalClose"),$noCommentBtn.length&&($noCommentBtn.removeClass(),$noCommentBtn.addClass("btn btn-success pull-right btnCommentModalCloseNews"),$("#melis-modals-container").find(".btnCommentModalCloseNews").length&&clearInterval(t))}),300)}function charCounter(e){var s=$(this).val().length,t=$(this).prev("label"),i=e.data.limit;t.find("span").length?0===s?(t.removeClass("limit"),t.find("span").remove()):(t.find("span").html('<i class="fa fa-text-width"></i>('+s+")"),s>i?(t.addClass("limit"),t.find("span").addClass("limit")):(t.removeClass("limit"),t.find("span").removeClass("limit"))):0!==s&&(t.find(".label-text").append("<span class='text-counter-indicator'><i class='fa fa-text-width'></i>("+s+")</span>"),s>i&&(t.addClass("limit"),t.find("span").addClass("limit")))}$((function(){var e=$("body"),s="ask",t=0;e.on("click",".addNews",(function(){var e=translations.tr_meliscmsnews_list_header_title_new;toolNews.tabOpen(e,0)})),e.on("click",".newsEdit",(function(){let e=$(this),s=e.closest("tr").attr("id"),t=e.closest("tr").find("td:nth-child(3)").text();toolNews.tabOpen(t,s)})),e.on("click",".newsListRefresh",(function(){toolNews.refreshTable()})),e.on("click",".newsDelete",(function(){var e=$(this).closest("tr").attr("id"),s=[];s.push({name:"newsId",value:e}),melisCoreTool.pending(".newsDelete"),melisCoreTool.confirm(translations.tr_meliscmsnews_common_label_yes,translations.tr_meliscmsnews_common_label_no,translations.tr_meliscmsnews_common_label_delete_news,translations.tr_meliscmsnews_common_label_delete_confirm,(function(){$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNewsList/deleteNews",data:s,dataType:"json",encode:!0}).done((function(s){s.success?(melisHelper.melisOkNotification(s.textTitle,s.textMessage),toolNews.tabClose(e),melisHelper.zoneReload("id_meliscmsnews_list_content_table","meliscmsnews_list_content_table",{})):melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors),melisCore.flashMessenger()})).fail((function(){alert(translations.tr_meliscore_error_message)}))})),melisCoreTool.done(".newsDelete")})),e.on("click",".removeAttachFile",(function(){var e=$(this),s=e.data("newsid"),t=e.data("column"),i=e.data("type"),n=[];n.push({name:"newsId",value:s}),n.push({name:"column",value:t}),n.push({name:"type",value:i}),melisCoreTool.pending(".removeAttachFile"),melisCoreTool.confirm(translations.tr_meliscmsnews_common_label_yes,translations.tr_meliscmsnews_common_label_no,melisHelper.melisTranslator("tr_meliscmsnews_delete_"+i+"_title"),melisHelper.melisTranslator("tr_meliscmsnews_delete_"+i+"_confirm_msg"),(function(){$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/removeAttachFile",data:n,dataType:"json",encode:!0}).done((function(e){e.success?(melisHelper.melisOkNotification(e.textTitle,e.textMessage),melisHelper.zoneReload(s+"_id_meliscmsnews_content_tabs_properties_details_left_images","meliscmsnews_content_tabs_properties_details_left_images",{newsId:s}),melisHelper.zoneReload(s+"_id_meliscmsnews_content_tabs_properties_details_left_documents","meliscmsnews_content_tabs_properties_details_left_documents",{newsId:s})):melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors),melisCore.flashMessenger()})).fail((function(){alert(translations.tr_meliscore_error_message)}))})),melisCoreTool.done(".removeAttachFile")})),e.on("keyup","#cnews_title",(function(){var e=$(this).closest(".container-level-a").data("newsid"),s=$(this).val(),t=($("#"+e+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .news-post-text-form"),$("#"+e+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs").find("li.active a").attr("data-lang-id"));$("#"+e+"_id_meliscmsnews_content_tabs_seo_details #cnews_"+t).find("#cnews_seo_url").val();""==$("#"+e+"_id_meliscmsnews_content_tabs_seo_details #cnews_"+t).find('input[name="cnews_seo_id"]').val()&&$("#"+e+"_id_meliscmsnews_content_tabs_seo_details #cnews_"+t).find("#cnews_seo_url").val(s.replace(/\s+/g,"-").replace(/[^a-z0-9]+/gi,"-").replace(/^-+/,"").replace(/-+$/,"").toLowerCase())})),e.on("click",".saveNewsLetter",(function(){var s=$(this);if(void 0===s.attr("disabled")){melisCoreTool.pending(".saveNewsLetter");var t=s.closest(".container-level-a"),i=t.find("form#newsLetterForm").serializeArray(),n=t.data("newsid"),a=t.find("select[name=cnews_slider_id]").val(),o=t.find("select[name=cnews_site_id]").val(),l=$("#"+n+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .news-post-text-form"),r=0;l.each((function(){var e=$(this);i.push({name:"cnews_title["+r+"]",value:e.find("#cnews_title").val()}),i.push({name:"cnews_subtitle["+r+"]",value:e.find("#cnews_subtitle").val()}),i.push({name:"cnews_lang_id["+r+"]",value:e.data("langId")});for(var s=1;s<=10;s++)i.push({name:"cnews_paragraph"+s+"["+r+"]",value:e.find("textarea[name='cnews_paragraph"+s+"']").val()});var t="";e.find("textarea.editme").each((function(){t=""!=t?t+"-"+$(this).attr("name"):$(this).attr("name")})),i.push({name:"cnews_paragraph_order["+r+"]",value:t}),r++})),$("#"+n+"_id_meliscmsnews_content_tabs_seo_details .news-seo-form").each((function(e,s){var t=$(this).find("form").attr("name"),n=($("form[name="+t+"]").length,new FormData($(this).find("form")[0]).entries());for(var a of n)i.push({name:"cnews_seo["+e+"]["+a[0]+"]",value:a[1]})})),i.push({name:"formCount",value:l.length}),t.find(".make-switch div").each((function(){var e=$(this),s=e.find("input").attr("name"),t=e.hasClass("switch-on")?1:0;i.push({name:s,value:t})})),i.push({name:"cnews_slider_id",value:a}),i.push({name:"cnews_site_id",value:o}),$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/saveNewsLetter",data:i,dataType:"json",encode:!0}).done((function(s){if(s.success)toolNews.tabClose(n),melisHelper.melisOkNotification(s.textTitle,s.textMessage),toolNews.tabOpen(s.chunk.cnews_title,s.chunk.cnews_id),toolNews.refreshTable();else{if(melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors),melisCoreTool.highlightErrors(s.success,s.errors,s.chunk.cnews_id+"_id_meliscmsnews_page form"),s.errors.hasOwnProperty("cnews_title")){var t=e.find("#"+s.chunk.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs label[for='cnews_title']");t.length&&t.css("color","red")}for(var i=["cnews_seo_meta_title","cnews_seo_meta_description","cnews_seo_canonical","cnews_seo_url","cnews_seo_url_redirect","cnews_seo_url_301"],a=i.length,o=0;o<a;o++)if(s.errors[i[o]]){var l=s.errors[i[o]].label,r=l.substring(l.indexOf("(")+1,l.lastIndexOf(")")),d=$("#"+n+"_id_meliscmsnews_content_tabs_seo_details").find("span:contains("+r+")").parent("a").attr("href");$("#"+s.chunk.cnews_id+"_id_meliscmsnews_content_tabs_seo_details .form-control[name="+i[o]+"]").parents(".form-group").children(":first").css("color","#686868"),$(d+" .form-control[name='"+i[o]+"']").parents(".form-group").children(":first").css("color","red")}}melisCore.flashMessenger(),melisCoreTool.done(".saveNewsLetter")}))}})),e.on("click",".newsAttachFile",(function(){melisCoreTool.pending(".newsAttachFile");var e=$(this),s=e.data("newsid"),t=e.data("filetype");melisHelper.createModal("id_meliscmsnews_modal_documents_form","meliscmsnews_modal_documents_form",!1,{newsId:s,type:t,isNew:1},"/melis/MelisCmsNews/MelisCmsNews/renderModal",(function(){melisCoreTool.done(".newsAttachFile")}))})),e.on("click",".newsAttachImage",(function(){melisCoreTool.pending(".newsAttachImage");var e=$(this),s=e.data("newsid"),t=e.data("filetype");melisHelper.createModal("id_meliscmsnews_modal_documents_form_image","meliscmsnews_modal_documents_form_image",!1,{newsId:s,type:t,isNew:1},"/melis/MelisCmsNews/MelisCmsNews/renderModal",(function(){melisCoreTool.done(".newsAttachImage")}))})),e.on("click",".newsEditImage",(function(){melisCoreTool.pending(".newsAttachImage");var e=$(this),s=e.data("newsid"),t=e.data("filetype"),i=e.data("column");melisHelper.createModal("id_meliscmsnews_modal_documents_form_image","meliscmsnews_modal_documents_form_image",!1,{newsId:s,type:t,isNew:0,column:i},"/melis/MelisCmsNews/MelisCmsNews/renderModal",(function(){melisCoreTool.done(".newsAttachImage")}))})),e.on("click","#newsAttachment",(function(){var e=$(this);melisCoreTool.pending(e);var s=$("#newsFileForm").get(0),t=new FormData(s);$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/saveFileForm",data:t,dataType:"json",processData:!1,cache:!1,contentType:!1,encode:!0,xhr:function(){var e=$.ajaxSettings.xhr();return e.upload&&e.upload.addEventListener("progress",toolNews.progress,!1),e}}).done((function(s){s.success?("image"===s.chunk.type?toolNews.imageModal(s.chunk):toolNews.fileModal(s.chunk),melisHelper.melisOkNotification(s.textTitle,s.textMessage,"#72af46"),melisCore.flashMessenger()):melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors,"closeByButtonOnly"),$("div.progressContent").addClass("hidden"),melisCoreTool.done(e)})).fail((function(s,t){alert(translations.tr_meliscore_error_message),melisCoreTool.done(e)}))})),e.on("change","select[name=cnews_site_id]",(function(){var e=$(this).parents().eq(6).find("table").attr("id");$("#"+e).DataTable().ajax.reload()})),e.on("change",".mcnews-page-details",(function(){var s=$(this),t=this.value,i=s.data();if(void 0!==i.newsId&&i.newsId>0&&void 0!==i.nameSpace.length&&i.nameSpace.length>0&&void 0!==t&&t>0){var n="/id/"+t+"/preview?melisSite="+i.nameSpace+"&newsId="+i.newsId+"&renderMode=previewtab",a=e.find("#"+i.newsId+"-see-below"),o=e.find("#"+i.newsId+"-see-in-new-tab");a.attr({"data-uri":n,disabled:!1}),o.attr({href:n,disabled:!1})}})),e.on("click",".preview-tab-see-below",(function(){var e=this.id,s=this.dataset;void 0!==s.uri&&s.uri&&(e=e.split("-")[0])&&melisHelper.zoneReload(e+"_id_meliscmsnews_tabs_preview_iframe","meliscmsnews_tabs_preview_iframe",{newsId:e,newsUri:s.uri})})),e.on("click",".comments",(function(){var e=$(this).attr("href"),s=$(e);melisCore.screenSize>768&&s.find(".comments-table-refresh").trigger("click")})),$(window).on("scroll",(function(){var e=activeTabId.split("_")[0],s=$("#"+e+"_news-preview-iframe-container");if(!0===s.is(":visible")){var t=s.find("iframe"),i=t.height();0===s.find(".overlay-loader").length&&i<=800&&t.height(t.contents().height())}})),e.on("keyup keydown change","form[name='newsSeoForm'] input[name='cnews_seo_meta_title']",{limit:65},charCounter),e.on("keyup keydown change","form[name='newsSeoForm'] textarea[name='cnews_seo_meta_description']",{limit:255},charCounter),e.on("click",".news-details-workflow",(function(){s="ask";var e=$(this);if(null==e.attr("disabled")){var i=e.data();melisCoreTool.pending(".news-details-workflow"),t=i.wfId,renderNewsWorkFlowModal(i,".news-details-workflow")}})),e.on("click",".news-workflow",(function(){s="ask";var e=$(this);if(null==e.attr("disabled")){melisCoreTool.pending(".news-workflow");var i=e.closest("tr"),n=i.attr("id");t=n;var a=i.find("td:eq(2)").text();renderNewsWorkFlowModal({wfDetails:a+" ("+n+")",wfId:n,wfOpeningJs:"melisHelper.tabOpen('"+a+"', 'fa fa-rss fa-2x',  '"+n+"_id_meliscmsnews_page', 'meliscmsnews_page', { newsId: "+n+" });",wfType:"NEWS"},".news-workflow")}})),e.on("click",".workflow-modal-cont .btn-validate-refuse",(function(){s=$(this).hasClass("pw-validate")?"validate":"refuse"})),e.on("click",".btnAddWorkflowCommentNews, .btnCommentModalCloseNews",(function(e){var i=t,n="workflow-comments-modal form#idmelissbpagecomments",a=s;$(this).hasClass("btnAddWorkflowCommentNews")||$("#id_pcom_text").val("");var o=$("#"+n).serializeArray();o.push({name:"pcom_news_id",value:i}),o.push({name:"action",value:a}),o=$.param(o),melisCoreTool.pending(".btnAddWorkflowCommentNews"),$.ajax({type:"POST",url:"/melis/MelisSmallBusiness/MelisWorkflow/addWorkflowComments",data:o,dataType:"json",encode:!0}).done((function(e){e.success?(melisHelper.zoneReload(i+"_id_meliscmsnews_center_workflow_tabs_comments_timeline","meliscmsnews_workflow_comments_timeline",{newsId:i}),melisCoreTool.clearForm(n),$("#workflowCommentModal").modal("hide"),$("#pageWorkflowModal").modal("hide")):(melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors,0),melisCoreTool.highlightErrors(e.success,e.errors,n)),melisCore.flashMessenger(),melisCoreTool.done(".btnAddWorkflowCommentNews")})).fail((function(){alert(translations.tr_meliscore_error_message)}))})),e.on("click",".btnAddNewsComment",(function(e){var s=$(this),t=s.data("newsid"),i=t+"_id_meliscmsnews_center_page_tabs_comments_add form#idmelissbpagecomments",n=$("#"+i).serializeArray();n.push({name:"pcom_news_id",value:t}),n=$.param(n),s.attr("disabled",!0),$.ajax({type:"POST",url:"/melis/MelisSmallBusiness/PageComments/addComment",data:n,dataType:"json",encode:!0}).done((function(e){e.success?(melisHelper.zoneReload(t+"_id_meliscmsnews_center_workflow_tabs_comments_timeline","meliscmsnews_workflow_comments_timeline",{newsId:t}),melisCoreTool.clearForm(i)):(melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors),melisCoreTool.highlightErrors(e.success,e.errors,i)),melisCore.flashMessenger()})).fail((function(){alert(translations.tr_meliscore_error_message)}))}))}));var toolNews={refreshTable:function(){melisHelper.zoneReload("id_meliscmsnews_list_content_table","meliscmsnews_list_content_table",{})},tabOpen:function(e,s){melisHelper.tabOpen(e,"fa fa-list-alt",s+"_id_meliscmsnews_page","meliscmsnews_page",{newsId:s},"id_meliscmsnews_left_menu")},tabClose:function(e){melisHelper.tabClose(e+"_id_meliscmsnews_page")},imageModal:function(e){$("#id_meliscmsnews_modal_documents_form_image_container").modal("hide"),melisHelper.zoneReload(e.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_images","meliscmsnews_content_tabs_properties_details_left_images",{newsId:e.cnews_id})},fileModal:function(e){$("#id_meliscmsnews_modal_documents_form_image_container").modal("hide"),$("#id_meliscmsnews_modal_documents_form_container").modal("hide"),melisHelper.zoneReload(e.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_documents","meliscmsnews_content_tabs_properties_details_left_documents",{newsId:e.cnews_id})},trimLength:function(e){return(e=$.trim(e)).length>15?(e=e.substring(0,12)).substring(0,e.lastIndexOf(" "))+"...":e},progress:function(e){var s=$("div.progressContent");s.removeClass("hidden");var t=$("div.progressContent > div.progress > div.progress-bar");t.attr("aria-valuenow",0),t.css("width","0%");var i=$("div.progressContent > div.progress > span.status");if(i.html(""),e.lengthComputable){var n=e.total,a=100*e.loaded/n;t.attr("aria-valuenow",a),t.css("width",a+"%"),a>100?s.addClass("hidden"):i.html(Math.round(a)+"%")}}};window.newsImagePreview=function(e,s){if(s.files&&s.files[0]){var t=new FileReader;t.onload=function(s){$(e).attr("src",s.target.result)},t.readAsDataURL(s.files[0])}},window.initNewsList=function(e){var s=$("#newsSiteSelect");s.length&&(e.cnews_site_id=s.val())},function(e){"use strict";e.fn.bootstrapSwitch=function(s){var t='input[type!="hidden"]',i={init:function(){return this.each((function(){var s,i,n,a,o,l,r=e(this),d=r.closest("form"),c="",m=r.attr("class"),_="ON",f="OFF",w=!1,h=!1;e.each(["switch-mini","switch-small","switch-large"],(function(e,s){m.indexOf(s)>=0&&(c=s)})),r.addClass("has-switch"),void 0!==r.data("on")&&(o="switch-"+r.data("on")),void 0!==r.data("on-label")&&(_=r.data("on-label")),void 0!==r.data("off-label")&&(f=r.data("off-label")),void 0!==r.data("label-icon")&&(w=r.data("label-icon")),void 0!==r.data("text-label")&&(h=r.data("text-label")),i=e("<span>").addClass("switch-left").addClass(c).addClass(o).html(_),o="",void 0!==r.data("off")&&(o="switch-"+r.data("off")),n=e("<span>").addClass("switch-right").addClass(c).addClass(o).html(f),a=e("<label>").html("&nbsp;").addClass(c).attr("for",r.find(t).attr("id")),w&&a.html('<i class="icon '+w+'"></i>'),h&&a.html(""+h),s=r.find(t).wrap(e("<div>")).parent().data("animated",!1),!1!==r.data("animated")&&s.addClass("switch-animate").data("animated",!0),s.append(i).append(a).append(n),r.find(">div").addClass(r.find(t).is(":checked")?"switch-on":"switch-off"),r.find(t).is(":disabled")&&e(this).addClass("deactivate");var p=function(e){r.parent("label").is(".label-change-switch")||e.siblings("label").trigger("mousedown").trigger("mouseup").trigger("click")};r.on("keydown",(function(s){32===s.keyCode&&(s.stopImmediatePropagation(),s.preventDefault(),p(e(s.target).find("span:first")))})),i.on("click",(function(s){p(e(this))})),n.on("click",(function(s){p(e(this))})),r.find(t).on("change",(function(s,t){var i=e(this),n=i.parent(),a=i.is(":checked"),o=n.is(".switch-off");if(s.preventDefault(),n.css("left",""),o===a){if(a?n.removeClass("switch-off").addClass("switch-on"):n.removeClass("switch-on").addClass("switch-off"),!1!==n.data("animated")&&n.addClass("switch-animate"),"boolean"==typeof t&&t)return;n.parent().trigger("switch-change",{el:i,value:a})}})),r.find("label").on("mousedown touchstart",(function(s){var t=e(this);l=!1,s.preventDefault(),s.stopImmediatePropagation(),t.closest("div").removeClass("switch-animate"),t.closest(".has-switch").is(".deactivate")||t.closest(".switch-on").parent().is(".radio-no-uncheck")?t.off("click"):(t.on("mousemove touchmove",(function(s){var t=e(this).closest(".make-switch"),i=((s.pageX||s.originalEvent.targetTouches[0].pageX)-t.offset().left)/t.width()*100;l=!0,i<25?i=25:i>75&&(i=75),t.find(">div").css("left",i-75+"%")})),t.on("click touchend",(function(s){var t=e(this),i=e(s.target).siblings("input");s.stopImmediatePropagation(),s.preventDefault(),t.off("mouseleave"),l?i.prop("checked",!(parseInt(t.parent().css("left"))<-25)):i.prop("checked",!i.is(":checked")),l=!1,i.trigger("change")})),t.on("mouseleave",(function(s){var t=e(this),i=t.siblings("input");s.preventDefault(),s.stopImmediatePropagation(),t.off("mouseleave"),t.trigger("mouseup"),i.prop("checked",!(parseInt(t.parent().css("left"))<-25)).trigger("change")})),t.on("mouseup",(function(s){s.stopImmediatePropagation(),s.preventDefault(),e(this).off("mousemove")})))})),"injected"!==d.data("bootstrapSwitch")&&(d.on("reset",(function(){setTimeout((function(){d.find(".make-switch").each((function(){var s=e(this).find(t);s.prop("checked",s.is(":checked")).trigger("change")}))}),1)})),d.data("bootstrapSwitch","injected"))}))},toggleActivation:function(){var s=e(this);s.toggleClass("deactivate"),s.find(t).prop("disabled",s.is(".deactivate"))},isActive:function(){return!e(this).hasClass("deactivate")},setActive:function(s){var i=e(this);s?(i.removeClass("deactivate"),i.find(t).prop("disabled",!1)):(i.addClass("deactivate"),i.find(t).attr("disabled","disabled"))},toggleState:function(s){var t=e(this).find(":checkbox");t.prop("checked",!t.is(":checked")).trigger("change",s)},toggleRadioState:function(s){var t=e(this).find(":radio");t.not(":checked").prop("checked",!t.is(":checked")).trigger("change",s)},toggleRadioStateAllowUncheck:function(s,t){var i=e(this).find(":radio");s?i.not(":checked").trigger("change",t):i.not(":checked").prop("checked",!i.is(":checked")).trigger("change",t)},setState:function(s,i){e(this).find(t).prop("checked",s).trigger("change",i)},setOnLabel:function(s){e(this).find(".switch-left").html(s)},setOffLabel:function(s){e(this).find(".switch-right").html(s)},setOnClass:function(s){var t=e(this).find(".switch-left"),i="";void 0!==s&&(void 0!==e(this).attr("data-on")&&(i="switch-"+e(this).attr("data-on")),t.removeClass(i),i="switch-"+s,t.addClass(i))},setOffClass:function(s){var t=e(this).find(".switch-right"),i="";void 0!==s&&(void 0!==e(this).attr("data-off")&&(i="switch-"+e(this).attr("data-off")),t.removeClass(i),i="switch-"+s,t.addClass(i))},setAnimated:function(s){var i=e(this).find(t).parent();void 0===s&&(s=!1),i.data("animated",s),i.attr("data-animated",s),!1!==i.data("animated")?i.addClass("switch-animate"):i.removeClass("switch-animate")},setSizeClass:function(s){var t=e(this),i=t.find(".switch-left"),n=t.find(".switch-right"),a=t.find("label");e.each(["switch-mini","switch-small","switch-large"],(function(e,t){t!==s?(i.removeClass(t),n.removeClass(t),a.removeClass(t)):(i.addClass(t),n.addClass(t),a.addClass(t))}))},status:function(){return e(this).find(t).is(":checked")},destroy:function(){var s,t=e(this),i=t.find("div"),n=t.closest("form");return i.find(":not(input)").remove(),(s=i.children()).unwrap().unwrap(),s.off("change"),n&&(n.off("reset"),n.removeData("bootstrapSwitch")),s}};return i[s]?i[s].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof s&&s?void e.error("Method "+s+" does not exist!"):i.init.apply(this,arguments)}}(jQuery);
