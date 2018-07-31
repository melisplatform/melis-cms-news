$(document).ready(function(){var e=$("body");e.on("click",".addNews",function(){var e=translations.tr_meliscmsnews_list_header_title_new;toolNews.tabOpen(e,0)}),e.on("click",".newsEdit",function(){var e=$(this).closest("tr").attr("id"),s=$(this).closest("tr").find("td:nth-child(3)").text();toolNews.tabOpen(s,e)}),e.on("click",".newsListRefresh",function(){toolNews.refreshTable()}),e.on("click",".newsDelete",function(){var e=$(this).closest("tr").attr("id"),s=[];s.push({name:"newsId",value:e}),melisCoreTool.pending(".newsDelete"),melisCoreTool.confirm(translations.tr_meliscmsnews_common_label_yes,translations.tr_meliscmsnews_common_label_no,translations.tr_meliscmsnews_common_label_delete_news,translations.tr_meliscmsnews_common_label_delete_confirm,function(){$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNewsList/deleteNews",data:s,dataType:"json",encode:!0}).success(function(s){s.success?(melisHelper.melisOkNotification(s.textTitle,s.textMessage),toolNews.tabClose(e),melisHelper.zoneReload("id_meliscmsnews_list_content_table","meliscmsnews_list_content_table",{})):melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors),melisCore.flashMessenger()}).error(function(){console.log("failed")})}),melisCoreTool.done(".newsDelete")}),e.on("click",".removeAttachFile",function(){var e=$(this).data("newsid"),s=$(this).data("column"),t=$(this).data("type"),i=[];i.push({name:"newsId",value:e}),i.push({name:"column",value:s}),i.push({name:"type",value:t}),melisCoreTool.pending(".removeAttachFile"),melisCoreTool.confirm(translations.tr_meliscmsnews_common_label_yes,translations.tr_meliscmsnews_common_label_no,melisHelper.melisTranslator("tr_meliscmsnews_delete_"+t+"_title"),melisHelper.melisTranslator("tr_meliscmsnews_delete_"+t+"_confirm_msg"),function(){$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/removeAttachFile",data:i,dataType:"json",encode:!0}).success(function(s){s.success?(melisHelper.melisOkNotification(s.textTitle,s.textMessage),melisHelper.zoneReload(e+"_id_meliscmsnews_content_tabs_properties_details_left_images","meliscmsnews_content_tabs_properties_details_left_images",{newsId:e}),melisHelper.zoneReload(e+"_id_meliscmsnews_content_tabs_properties_details_left_documents","meliscmsnews_content_tabs_properties_details_left_documents",{newsId:e})):melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors),melisCore.flashMessenger()}).error(function(){console.log("failed")})}),melisCoreTool.done(".removeAttachFile")}),e.on("click",".saveNewsLetter",function(){if(void 0==$(this).attr("disabled")){melisCoreTool.pending(".saveNewsLetter");var e=$(this).closest(".container-level-a"),s=e.find("form#newsLetterForm").serializeArray(),t=e.data("newsid"),i=e.find("select[name=cnews_slider_id]").val(),a=e.find("select[name=cnews_site_id]").val(),n=$("#"+t+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs form#newsSiteTitleSubtitleForm");ctr=0,len=1,n.each(function(){s.push({name:"cnews_title["+ctr+"]",value:$("#"+t+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs #cnews_"+len+" #cnews_title").val()}),s.push({name:"cnews_subtitle["+ctr+"]",value:$("#"+t+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs #cnews_"+len+" #cnews_subtitle").val()}),s.push({name:"cnews_lang_id["+ctr+"]",value:$("#"+t+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .product-text-tab #news_cms_lang_"+len).attr("data-lang-id")});for(var e=1;e<=4;e++)s.push({name:"cnews_paragraph"+e+"["+ctr+"]",value:$("#"+t+"_id_meliscmsnews_content_tabs_properties_details_right_paragraphs #cnews_"+len+" #cnews_paragraph"+e).val()});ctr++,len++}),s.push({name:"formCount",value:n.length}),e.find(".make-switch div").each(function(){var e=$(this).find("input").attr("name"),t=$(this).hasClass("switch-on"),i=t?1:0;s.push({name:e,value:i})}),s.push({name:"cnews_slider_id",value:i}),s.push({name:"cnews_site_id",value:a}),$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/saveNewsLetter",data:s,dataType:"json",encode:!0}).success(function(e){e.success?(toolNews.tabClose(t),melisHelper.melisOkNotification(e.textTitle,e.textMessage),toolNews.tabOpen(e.chunk.cnews_title,e.chunk.cnews_id),toolNews.refreshTable()):(melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors),melisCoreTool.highlightErrors(e.success,e.errors,activeTabId+" form"),$(".newsPublishDate").prev("label").css("color","#686868"),$(".newsUnpublishDate").prev("label").css("color","#686868"),$.each(e.errors,function(e,s){"cnews_publish_date"==e&&$(".newsPublishDate").prev("label").css("color","red"),"cnews_unpublish_date"==e&&$(".newsUnpublishDate").prev("label").css("color","red")})),melisCore.flashMessenger(),melisCoreTool.done(".saveNewsLetter")}).error(function(){melisCoreTool.done(".saveNewsLetter"),console.log("failed")})}}),e.on("click",".newsAttachFile",function(){melisCoreTool.pending(".newsAttachFile");var e=$(this).data("newsid"),s=$(this).data("filetype");zoneId="id_meliscmsnews_modal_documents_form",melisKey="meliscmsnews_modal_documents_form",modalUrl="/melis/MelisCmsNews/MelisCmsNews/renderModal",melisHelper.createModal(zoneId,melisKey,!1,{newsId:e,type:s,isNew:1},modalUrl,function(){melisCoreTool.done(".newsAttachFile")})}),e.on("click",".newsAttachImage",function(){melisCoreTool.pending(".newsAttachImage");var e=$(this).data("newsid"),s=$(this).data("filetype");zoneId="id_meliscmsnews_modal_documents_form_image",melisKey="meliscmsnews_modal_documents_form_image",modalUrl="/melis/MelisCmsNews/MelisCmsNews/renderModal",melisHelper.createModal(zoneId,melisKey,!1,{newsId:e,type:s,isNew:1},modalUrl,function(){melisCoreTool.done(".newsAttachImage")})}),e.on("click",".newsEditImage",function(){melisCoreTool.pending(".newsAttachImage");var e=$(this).data("newsid"),s=$(this).data("filetype"),t=$(this).data("column");zoneId="id_meliscmsnews_modal_documents_form_image",melisKey="meliscmsnews_modal_documents_form_image",modalUrl="/melis/MelisCmsNews/MelisCmsNews/renderModal",melisHelper.createModal(zoneId,melisKey,!1,{newsId:e,type:s,isNew:0,column:t},modalUrl,function(){melisCoreTool.done(".newsAttachImage")})}),e.on("click","#newsAttachment",function(){var e=$(this);melisCoreTool.pending(e);var s=($("form#newsFileForm input[name=cnews_id]").val(),$("#newsFileForm").get(0)),t=new FormData(s);$.ajax({type:"POST",url:"/melis/MelisCmsNews/MelisCmsNews/saveFileForm",data:t,dataType:"json",processData:!1,cache:!1,contentType:!1,encode:!0,xhr:function(){var e=$.ajaxSettings.xhr();return e.upload&&e.upload.addEventListener("progress",toolNews.progress,!1),e}}).success(function(s){s.success?("image"==s.chunk.type?toolNews.imageModal(s.chunk):toolNews.fileModal(s.chunk),melisHelper.melisOkNotification(s.textTitle,s.textMessage,"#72af46"),melisCore.flashMessenger()):melisHelper.melisKoNotification(s.textTitle,s.textMessage,s.errors,"closeByButtonOnly"),$("div.progressContent").addClass("hidden"),melisCoreTool.done(e)}).error(function(s,t,i,a){console.log("failed",JSON.parse(t.responseText)),melisCoreTool.done(e)})}),e.on("change","select[name=cnews_site_id]",function(){var e=$(this).parents().eq(6).find("table").attr("id");$("#"+e).DataTable().ajax.reload()})});var toolNews={refreshTable:function(){melisHelper.zoneReload("id_meliscmsnews_list_content_table","meliscmsnews_list_content_table",{})},tabOpen:function(e,s){melisHelper.tabOpen(e,"fa fa-list-alt",s+"_id_meliscmsnews_page","meliscmsnews_page",{newsId:s},"id_meliscmsnews_left_menu")},tabClose:function(e){melisHelper.tabClose(e+"_id_meliscmsnews_page")},imageModal:function(e){$("#id_meliscmsnews_modal_documents_form_image_container").modal("hide"),melisHelper.zoneReload(e.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_images","meliscmsnews_content_tabs_properties_details_left_images",{newsId:e.cnews_id})},fileModal:function(e){$("#id_meliscmsnews_modal_documents_form_image_container").modal("hide"),$("#id_meliscmsnews_modal_documents_form_container").modal("hide"),melisHelper.zoneReload(e.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_documents","meliscmsnews_content_tabs_properties_details_left_documents",{newsId:e.cnews_id})},trimLength:function(e){return e=$.trim(e),e.length>15?(e=e.substring(0,15-"...".length),e.substring(0,e.lastIndexOf(" "))+"..."):e},progress:function(e){if($("div.progressContent").removeClass("hidden"),$("div.progressContent > div.progress > div.progress-bar").attr("aria-valuenow",0),$("div.progressContent > div.progress > div.progress-bar").css("width","0%"),$("div.progressContent > div.progress > span.status").html(""),e.lengthComputable){var s=e.total,t=e.loaded,i=100*t/s;$("div.progressContent > div.progress > div.progress-bar").attr("aria-valuenow",i),$("div.progressContent > div.progress > div.progress-bar").css("width",i+"%"),i>100?$("div.progressContent").addClass("hidden"):$("div.progressContent > div.progress > span.status").html(Math.round(i)+"%")}}};window.newsImagePreview=function(e,s){if(s.files&&s.files[0]){var t=new FileReader;t.onload=function(s){$(e).attr("src",s.target.result)},t.readAsDataURL(s.files[0])}},window.initNewsList=function(e,s){$("#newsSiteSelect").length&&(e.cnews_site_id=$("#newsSiteSelect").val())},function(e){"use strict";e.fn.bootstrapSwitch=function(s){var t='input[type!="hidden"]',i={init:function(){return this.each(function(){var s,i,a,n,o,l,r=e(this),c=r.closest("form"),d="",m=r.attr("class"),h="ON",_="OFF",p=!1,f=!1;e.each(["switch-mini","switch-small","switch-large"],function(e,s){m.indexOf(s)>=0&&(d=s)}),r.addClass("has-switch"),void 0!==r.data("on")&&(o="switch-"+r.data("on")),void 0!==r.data("on-label")&&(h=r.data("on-label")),void 0!==r.data("off-label")&&(_=r.data("off-label")),void 0!==r.data("label-icon")&&(p=r.data("label-icon")),void 0!==r.data("text-label")&&(f=r.data("text-label")),i=e("<span>").addClass("switch-left").addClass(d).addClass(o).html(h),o="",void 0!==r.data("off")&&(o="switch-"+r.data("off")),a=e("<span>").addClass("switch-right").addClass(d).addClass(o).html(_),n=e("<label>").html("&nbsp;").addClass(d).attr("for",r.find(t).attr("id")),p&&n.html('<i class="icon '+p+'"></i>'),f&&n.html(""+f),s=r.find(t).wrap(e("<div>")).parent().data("animated",!1),!1!==r.data("animated")&&s.addClass("switch-animate").data("animated",!0),s.append(i).append(n).append(a),r.find(">div").addClass(r.find(t).is(":checked")?"switch-on":"switch-off"),r.find(t).is(":disabled")&&e(this).addClass("deactivate");var u=function(e){r.parent("label").is(".label-change-switch")||e.siblings("label").trigger("mousedown").trigger("mouseup").trigger("click")};r.on("keydown",function(s){32===s.keyCode&&(s.stopImmediatePropagation(),s.preventDefault(),u(e(s.target).find("span:first")))}),i.on("click",function(s){u(e(this))}),a.on("click",function(s){u(e(this))}),r.find(t).on("change",function(s,t){var i=e(this),a=i.parent(),n=i.is(":checked"),o=a.is(".switch-off");if(s.preventDefault(),a.css("left",""),o===n){if(n?a.removeClass("switch-off").addClass("switch-on"):a.removeClass("switch-on").addClass("switch-off"),!1!==a.data("animated")&&a.addClass("switch-animate"),"boolean"==typeof t&&t)return;a.parent().trigger("switch-change",{el:i,value:n})}}),r.find("label").on("mousedown touchstart",function(s){var t=e(this);l=!1,s.preventDefault(),s.stopImmediatePropagation(),t.closest("div").removeClass("switch-animate"),t.closest(".has-switch").is(".deactivate")?t.unbind("click"):t.closest(".switch-on").parent().is(".radio-no-uncheck")?t.unbind("click"):(t.on("mousemove touchmove",function(s){var t=e(this).closest(".make-switch"),i=(s.pageX||s.originalEvent.targetTouches[0].pageX)-t.offset().left,a=i/t.width()*100;l=!0,a<25?a=25:a>75&&(a=75),t.find(">div").css("left",a-75+"%")}),t.on("click touchend",function(s){var t=e(this),i=e(s.target),a=i.siblings("input");s.stopImmediatePropagation(),s.preventDefault(),t.unbind("mouseleave"),l?a.prop("checked",!(parseInt(t.parent().css("left"))<-25)):a.prop("checked",!a.is(":checked")),l=!1,a.trigger("change")}),t.on("mouseleave",function(s){var t=e(this),i=t.siblings("input");s.preventDefault(),s.stopImmediatePropagation(),t.unbind("mouseleave"),t.trigger("mouseup"),i.prop("checked",!(parseInt(t.parent().css("left"))<-25)).trigger("change")}),t.on("mouseup",function(s){s.stopImmediatePropagation(),s.preventDefault(),e(this).unbind("mousemove")}))}),"injected"!==c.data("bootstrapSwitch")&&(c.bind("reset",function(){setTimeout(function(){c.find(".make-switch").each(function(){var s=e(this).find(t);s.prop("checked",s.is(":checked")).trigger("change")})},1)}),c.data("bootstrapSwitch","injected"))})},toggleActivation:function(){var s=e(this);s.toggleClass("deactivate"),s.find(t).prop("disabled",s.is(".deactivate"))},isActive:function(){return!e(this).hasClass("deactivate")},setActive:function(s){var i=e(this);s?(i.removeClass("deactivate"),i.find(t).removeAttr("disabled")):(i.addClass("deactivate"),i.find(t).attr("disabled","disabled"))},toggleState:function(s){var t=e(this).find(":checkbox");t.prop("checked",!t.is(":checked")).trigger("change",s)},toggleRadioState:function(s){var t=e(this).find(":radio");t.not(":checked").prop("checked",!t.is(":checked")).trigger("change",s)},toggleRadioStateAllowUncheck:function(s,t){var i=e(this).find(":radio");s?i.not(":checked").trigger("change",t):i.not(":checked").prop("checked",!i.is(":checked")).trigger("change",t)},setState:function(s,i){e(this).find(t).prop("checked",s).trigger("change",i)},setOnLabel:function(s){e(this).find(".switch-left").html(s)},setOffLabel:function(s){e(this).find(".switch-right").html(s)},setOnClass:function(s){var t=e(this).find(".switch-left"),i="";void 0!==s&&(void 0!==e(this).attr("data-on")&&(i="switch-"+e(this).attr("data-on")),t.removeClass(i),i="switch-"+s,t.addClass(i))},setOffClass:function(s){var t=e(this).find(".switch-right"),i="";void 0!==s&&(void 0!==e(this).attr("data-off")&&(i="switch-"+e(this).attr("data-off")),t.removeClass(i),i="switch-"+s,t.addClass(i))},setAnimated:function(s){var i=e(this).find(t).parent();void 0===s&&(s=!1),i.data("animated",s),i.attr("data-animated",s),!1!==i.data("animated")?i.addClass("switch-animate"):i.removeClass("switch-animate")},setSizeClass:function(s){var t=e(this),i=t.find(".switch-left"),a=t.find(".switch-right"),n=t.find("label");e.each(["switch-mini","switch-small","switch-large"],function(e,t){t!==s?(i.removeClass(t),a.removeClass(t),n.removeClass(t)):(i.addClass(t),a.addClass(t),n.addClass(t))})},status:function(){return e(this).find(t).is(":checked")},destroy:function(){var s,t=e(this),i=t.find("div"),a=t.closest("form");return i.find(":not(input)").remove(),s=i.children(),s.unwrap().unwrap(),s.unbind("change"),a&&(a.unbind("reset"),a.removeData("bootstrapSwitch")),s}};return i[s]?i[s].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof s&&s?void e.error("Method "+s+" does not exist!"):i.init.apply(this,arguments)}}(jQuery);