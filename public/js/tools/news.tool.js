$(function() {
    var $body = $("body");
    var gActionNews = "ask"; //default to ask
    var MelisCmsNewsId = 0;

        $body.on("click", '.addNews', function() {
            var newsId  = 0,
                name    = translations.tr_meliscmsnews_list_header_title_new;

                toolNews.tabOpen(name, newsId);
        });

        $body.on("click", '.newsEdit', function() {
            var $this   = $(this),
                newsId  = $this.closest('tr').attr('id'),
                name    = $this.closest('tr').find("td:nth-child(3)").text();

                toolNews.tabOpen(name, newsId);
        });

        $body.on("click", '.newsListRefresh', function() {
            toolNews.refreshTable();
        });

        $body.on("click", ".newsDelete", function() {
            var $this       = $(this),
                newsId      = $this.closest('tr').attr('id'),
                ajaxUrl     = '/melis/MelisCmsNews/MelisCmsNewsList/deleteNews',
                dataString  = [];

                dataString.push({
                    name: 'newsId',
                    value: newsId
                });

                melisCoreTool.pending(".newsDelete");

                melisCoreTool.confirm(
                    translations.tr_meliscmsnews_common_label_yes,
                    translations.tr_meliscmsnews_common_label_no,
                    translations.tr_meliscmsnews_common_label_delete_news,
                    translations.tr_meliscmsnews_common_label_delete_confirm,
                    function () {
                        $.ajax({
                            type: 'POST',
                            url: ajaxUrl,
                            data: dataString,
                            dataType: 'json',
                            encode: true
                        }).done(function (data) {
                            if (data.success) {
                                melisHelper.melisOkNotification(data.textTitle, data.textMessage);
                                toolNews.tabClose(newsId);
                                melisHelper.zoneReload("id_meliscmsnews_list_content_table", "meliscmsnews_list_content_table", {});
                            } else {
                                melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                            }
                            melisCore.flashMessenger();
                        }).fail(function () {
                            alert( translations.tr_meliscore_error_message );
                        });
                    });

                melisCoreTool.done(".newsDelete");
        });

        $body.on("click", ".removeAttachFile", function() {
            var $this       = $(this),
                newsId      = $this.data('newsid'),
                column      = $this.data('column'),
                type        = $this.data('type'),
                ajaxUrl     = '/melis/MelisCmsNews/MelisCmsNews/removeAttachFile',
                dataString  = [];

                dataString.push({
                    name: 'newsId',
                    value: newsId
                });

                dataString.push({
                    name: 'column',
                    value: column
                });
                
                dataString.push({
                    name: 'type',
                    value: type
                });

                melisCoreTool.pending(".removeAttachFile");

                melisCoreTool.confirm(
                    translations.tr_meliscmsnews_common_label_yes,
                    translations.tr_meliscmsnews_common_label_no,
                    melisHelper.melisTranslator("tr_meliscmsnews_delete_" + type + "_title"),
                    melisHelper.melisTranslator("tr_meliscmsnews_delete_" + type + "_confirm_msg"),
                    function() {
                        $.ajax({
                            type: 'POST',
                            url: ajaxUrl,
                            data: dataString,
                            dataType: 'json',
                            encode: true
                        }).done(function (data) {
                            if (data.success) {
                                melisHelper.melisOkNotification(data.textTitle, data.textMessage);
                                melisHelper.zoneReload(newsId + "_id_meliscmsnews_content_tabs_properties_details_left_images", "meliscmsnews_content_tabs_properties_details_left_images", {'newsId': newsId});
                                melisHelper.zoneReload(newsId + "_id_meliscmsnews_content_tabs_properties_details_left_documents", "meliscmsnews_content_tabs_properties_details_left_documents", {'newsId': newsId});
                            } else {
                                melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                            }
                            melisCore.flashMessenger();
                        }).fail(function () {
                            alert( translations.tr_meliscore_error_message );
                        });
                    });

                melisCoreTool.done(".removeAttachFile");
        });

        //automatically set the seo url in Seo tab when entering a new title, applies to adding a news only, not applicable to editing 
        $body.on("keyup", "#cnews_title", function(){
            var newsPage        = $(this).closest('.container-level-a'),
                newsId          = newsPage.data('newsid'),
                newsTitle       = $(this).val();
            //get first the active language in the Text Form
            var textForms   = $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .news-post-text-form');
            var activeDataLangId = $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs').find('li.active a').attr('data-lang-id');
            var origval   = $('#' + newsId + '_id_meliscmsnews_content_tabs_seo_details #cnews_'+activeDataLangId).find('#cnews_seo_url').val(); 
            var cnews_seo_id = $('#' + newsId + '_id_meliscmsnews_content_tabs_seo_details #cnews_'+activeDataLangId).find('input[name="cnews_seo_id"]').val();
            if (cnews_seo_id == '') {
                $('#' + newsId + '_id_meliscmsnews_content_tabs_seo_details #cnews_'+activeDataLangId).find('#cnews_seo_url').val(newsTitle.replace(/\s+/g, '-').replace(/[^a-z0-9]+/gi, '-').replace(/^-+/, '').replace(/-+$/, '').toLowerCase());
            }                        
        });
        $body.on("click", ".saveNewsLetter", function() {
            var $this = $(this);

                if ( $this.attr('disabled') === undefined ) {
                    melisCoreTool.pending(".saveNewsLetter");

                    var ajaxUrl         = '/melis/MelisCmsNews/MelisCmsNews/saveNewsLetter',
                        newsPage        = $this.closest('.container-level-a'),
                        dataString      = newsPage.find('form#newsLetterForm').serializeArray(),
                        newsId          = newsPage.data('newsid'),
                        selectedSlider  = newsPage.find('select[name=cnews_slider_id]').val(),
                        selectedSite    = newsPage.find('select[name=cnews_site_id]').val();

                    //newsSiteTitleSubtitleForm
                    var forms   = $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .news-post-text-form'),
                        ctr     = 0;

                        forms.each(function () {
                            var formContainer = $(this);

                            dataString.push({
                                name: 'cnews_title[' + ctr + ']',
                                value: formContainer.find('#cnews_title').val()
                            });

                            dataString.push({
                                name: 'cnews_subtitle[' + ctr + ']',
                                value: formContainer.find('#cnews_subtitle').val()
                            });

                            dataString.push({
                                name: 'cnews_lang_id' + "[" + ctr + "]",
                                value: formContainer.data("langId")
                            });

                            for (var i = 1; i <= 10; i++) {
                                dataString.push({
                                    name: 'cnews_paragraph' + i + "[" + ctr + "]",
                                    value: formContainer.find("textarea[name='cnews_paragraph"+ i+"'"+"]" ).val()
                                });
                            }
                            var order = "";
                            formContainer.find("textarea.editme").each(function(){   
                                if (order != '') {
                                    order = order + "-" + $(this).attr('name');    
                                } else{
                                    order = $(this).attr('name')
                                }                           
                            });                
                            dataString.push({
                                name: 'cnews_paragraph_order[' + ctr + ']',
                                value: order
                            });
                            ctr++;
                        });

                    var seoForms   = $('#' + newsId + '_id_meliscmsnews_content_tabs_seo_details .news-seo-form');
                        seoForms.each(function(index, val) {                         
                            var form_name = $(this).find('form').attr('name');    
                            var countForm = $('form[name='+form_name+']').length;
                            var formData = new FormData($(this).find('form')[0]);
                            var formValues = formData.entries();
                            for(var pair of formValues){      
                                dataString.push({
                                    name: 'cnews_seo['+index+']['+pair[0]+']',
                                    value: pair[1]
                                });                             
                            }                                  
                        });
                        dataString.push({name: 'formCount', value: forms.length});
                        //end

                        newsPage.find('.make-switch div').each(function () {
                            var $this       = $(this),
                                field       = $this.find('input').attr('name'),
                                status      = $this.hasClass('switch-on'),
                                saveStatus  = (status) ? 1 : 0;

                                dataString.push({name: field, value: saveStatus});
                        });

                        dataString.push({name: 'cnews_slider_id', value: selectedSlider});
                        dataString.push({name: 'cnews_site_id', value: selectedSite});

                        $.ajax({
                            type: 'POST',
                            url: ajaxUrl,
                            data: dataString,
                            dataType: 'json',
                            encode: true
                        }).done(function (data) {
                            if (data.success) {
                                toolNews.tabClose(newsId);
                                melisHelper.melisOkNotification(data.textTitle, data.textMessage);
                                toolNews.tabOpen(data.chunk.cnews_title, data.chunk.cnews_id);
                                toolNews.refreshTable();
                            } else {
                                melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                                melisCoreTool.highlightErrors(data.success, data.errors, data.chunk.cnews_id + "_id_meliscmsnews_page form");

                                if (data.errors.hasOwnProperty('cnews_title')) {
                                    /**
                                     * Exceptional case for highlighting errors:
                                     *  "melisCoreTool.highlightErrors()" function cannot be used in highlighting all title fields
                                     *  because they use the same name ('cnews_title'), & the selector used by the said function
                                     *  is the element's/field's name. Resulting into only one title field being highlighted
                                     */
                                    var allTitleLabels = $body.find("#" + data.chunk.cnews_id + "_id_meliscmsnews_content_tabs_properties_details_right_paragraphs label[for='cnews_title']");
                                    if (allTitleLabels.length) {
                                        allTitleLabels.css("color", "red");
                                    }
                                }
                                var seo_fields = ["cnews_seo_meta_title","cnews_seo_meta_description","cnews_seo_canonical","cnews_seo_url","cnews_seo_url_redirect","cnews_seo_url_301"];
                                var arrayLength = seo_fields.length;
                                for (var i = 0; i < arrayLength; i++) {                                  
                                    if (data['errors'][seo_fields[i]]) {
                                        var label = data['errors'][seo_fields[i]]['label'];    
                                        var lang = label.substring(
                                            label.indexOf("(") + 1, 
                                            label.lastIndexOf(")")
                                        );
                                        var langTabIDWithError = $('#' + newsId + '_id_meliscmsnews_content_tabs_seo_details').find('span:contains('+lang+')').parent('a').attr('href');
                                        $('#' + data.chunk.cnews_id + '_id_meliscmsnews_content_tabs_seo_details .form-control[name='+seo_fields[i]+']').parents(".form-group").children(":first").css("color", "#686868");
                                        $(langTabIDWithError + " .form-control[name='"+seo_fields[i]+"']").parents(".form-group").children(":first").css("color", "red");  
                                    }                                   
                                }                             
                            }
                            melisCore.flashMessenger();
                            melisCoreTool.done(".saveNewsLetter");
                        });
                }
        });

        $body.on("click", '.newsAttachFile', function() {
            melisCoreTool.pending('.newsAttachFile');

            var $this       = $(this),
                newsId      = $this.data('newsid'),
                type        = $this.data('filetype'),
                // initialation of local variable
                zoneId      = 'id_meliscmsnews_modal_documents_form',
                melisKey    = 'meliscmsnews_modal_documents_form',
                modalUrl    = '/melis/MelisCmsNews/MelisCmsNews/renderModal';

                // requesitng to create modal and display after
                melisHelper.createModal(zoneId, melisKey, false, {
                    'newsId': newsId,
                    'type': type,
                    'isNew': 1
                }, modalUrl, function () {
                    melisCoreTool.done('.newsAttachFile');
                });
        });

        $body.on("click", '.newsAttachImage', function() {
            melisCoreTool.pending('.newsAttachImage');

            var $this       = $(this),
                newsId      = $this.data('newsid'),
                type        = $this.data('filetype'),
                // initialation of local variable
                zoneId      = 'id_meliscmsnews_modal_documents_form_image',
                melisKey    = 'meliscmsnews_modal_documents_form_image',
                modalUrl    = '/melis/MelisCmsNews/MelisCmsNews/renderModal';

                // requesitng to create modal and display after
                melisHelper.createModal(zoneId, melisKey, false, {
                    'newsId': newsId,
                    'type': type,
                    'isNew': 1
                }, modalUrl, function () {
                    melisCoreTool.done('.newsAttachImage');
                });
        });

        $body.on("click", '.newsEditImage', function() {
            melisCoreTool.pending('.newsAttachImage');

            var $this       = $(this),
                newsId      = $this.data('newsid'),
                type        = $this.data('filetype'),
                column      = $this.data('column'),
                // initialation of local variable
                zoneId      = 'id_meliscmsnews_modal_documents_form_image',
                melisKey    = 'meliscmsnews_modal_documents_form_image',
                modalUrl    = '/melis/MelisCmsNews/MelisCmsNews/renderModal';

                // requesitng to create modal and display after
                melisHelper.createModal(zoneId, melisKey, false, {
                    'newsId': newsId,
                    'type': type,
                    'isNew': 0,
                    'column': column
                }, modalUrl, function () {
                    melisCoreTool.done('.newsAttachImage');
                });
        });

        $body.on("click", "#newsAttachment", function () {
            var saveBtn = $(this);
                melisCoreTool.pending(saveBtn);

            var ajaxUrl     = '/melis/MelisCmsNews/MelisCmsNews/saveFileForm',
                tmpForm     = $('#newsFileForm').get(0),
                sliderData  = new FormData(tmpForm);

                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: sliderData,
                    dataType: 'json',
                    processData: false,
                    cache: false,
                    contentType: false,
                    encode: true,
                    xhr: function () {
                        var fileXhr = $.ajaxSettings.xhr();
                            if (fileXhr.upload) {
                                fileXhr.upload.addEventListener('progress', toolNews.progress, false);
                            }
                            return fileXhr;
                    }
                }).done(function (data) {
                    if (data.success) {
                        if (data.chunk.type === 'image') {
                            toolNews.imageModal(data.chunk);
                        } else {
                            toolNews.fileModal(data.chunk);
                        }

                        melisHelper.melisOkNotification(data.textTitle, data.textMessage, '#72af46');
                        melisCore.flashMessenger();
                    } else {
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 'closeByButtonOnly');
                    }
                    $("div.progressContent").addClass("hidden");
                    melisCoreTool.done(saveBtn);
                }).fail(function (event, xhr) {
                    alert( translations.tr_meliscore_error_message );
                    melisCoreTool.done(saveBtn);
                });
        });

        $body.on("change", "select[name=cnews_site_id]", function () {
            var tableId = $(this).parents().eq(6).find('table').attr('id');
                
                $("#" + tableId).DataTable().ajax.reload();
        });

        /**
         * Preview tab: News details page selector
         */
        $body.on("change", ".mcnews-page-details", function () {
            /** Get news ID, create the URI for "see-in-new-tab" button & iFrame src. */
            var $this           = $(this),
                pageId          = this.value,
                selectorData    = $this.data();

            if ( typeof selectorData.newsId !== "undefined" && selectorData.newsId > 0 &&
                typeof selectorData.nameSpace.length !== "undefined" && selectorData.nameSpace.length > 0 &&
                typeof pageId !== "undefined" && pageId > 0 ) {

                var uri = "/id/" + pageId + "/preview?melisSite=" + selectorData.nameSpace + "&newsId=" + selectorData.newsId + "&renderMode=previewtab";

                /** Change href attributes for "Display below" & "Display in a new tab" buttons */
                var seeBelow    = $body.find("#" + selectorData.newsId + "-see-below"),
                    seeNewTab   = $body.find("#" + selectorData.newsId + "-see-in-new-tab");

                    seeBelow.attr({"data-uri": uri, "disabled": false});
                    seeNewTab.attr({"href": uri, "disabled": false});
            }
        });

        /**
         * Preview tab: Display below event
         */
        $body.on("click", ".preview-tab-see-below", function () {
            /** Load iFrame area using the URI as src */
            var newsId  = this.id,
                btn     = this.dataset;

                if ( typeof btn.uri !== "undefined" && btn.uri ) {
                    newsId = newsId.split("-")[0];

                    if ( newsId ) {
                        melisHelper.zoneReload(newsId + "_id_meliscmsnews_tabs_preview_iframe", "meliscmsnews_tabs_preview_iframe", {newsId: newsId, newsUri: btn.uri});
                    }
                }
        });

        /**
         * Comments tab
         */
        $body.on("click", ".comments", function() {
            var $this 		= $(this),
                href 		= $this.attr("href"),
                $tabContent = $(href);

                if ( melisCore.screenSize > 768 ) {
                    $tabContent.find(".comments-table-refresh").trigger("click");
                }
        });

        // auto adjust height of iframe on preview
        $(window).on("scroll", function() {
            // get hte news id
            var newsId = activeTabId.split('_')[0];
            // preview container
            var previewNews = $("#" + newsId + "_news-preview-iframe-container");
            // trigger only when the container is visible
            if (previewNews.is(':visible') === true) {
               // find iframe
               var iframe = previewNews.find('iframe');
               // get current iframe hieght
               var iframeHeight = iframe.height();
               // get loader
               var loader = previewNews.find(".overlay-loader");
               // trigger only after loading the iframe
               if (loader.length === 0) {
                   // adjust height if the current height is below to the min-height
                   if (iframeHeight <= 800) {
                       // adjust iframe height
                       iframe.height(iframe.contents().height());
                   }
               }
            }
        });
        $body.on(
            "keyup keydown change",
            "form[name='newsSeoForm'] input[name='cnews_seo_meta_title']",
            { limit: 65 },
            charCounter
        );
        $body.on(
            "keyup keydown change",
            "form[name='newsSeoForm'] textarea[name='cnews_seo_meta_description']",
            { limit: 255 },
            charCounter
        );
        $body.on("click", ".news-details-workflow", function() {
            gActionNews = "ask"; //set back to default action 
            var $this = $(this);
            if ( $this.attr('disabled') == undefined ) {
                var pageData = $this.data();
                    melisCoreTool.pending('.news-details-workflow');
                    MelisCmsNewsId = pageData.wfId;
                    renderNewsWorkFlowModal(pageData, ".news-details-workflow");
            }
        });
        $body.on("click", ".news-workflow", function() {
            gActionNews = "ask"; //set back to default action 
            var $this = $(this);
                if ( $this.attr('disabled') == undefined ) {
                    melisCoreTool.pending('.news-workflow');
                    var news    = $this.closest('tr'),
                        newsId  = news.attr('id');
                        MelisCmsNewsId = newsId;
                        var newsTitle = news.find("td:eq(2)").text();
                        var pageData = {
                            wfDetails: newsTitle + ' (' + newsId + ')',
                            wfId: newsId,
                            wfOpeningJs: "melisHelper.tabOpen('" + newsTitle + "', 'fa fa-rss fa-2x', " + newsId + "'_id_meliscmsnews_page', 'meliscmsnews_page', { newsId: " + newsId + " });",
                            wfType: 'NEWS'
                        };
                        renderNewsWorkFlowModal(pageData, ".news-workflow");                        
                }
        });
        $body.on("click",".workflow-modal-cont .btn-validate-refuse", function() {    
            gActionNews = $(this).hasClass('pw-validate') ? "validate" : "refuse" ;
        });
        $body.on('click', '.btnAddWorkflowCommentNews, .btnCommentModalCloseNews', function (e) {
            var newsId = MelisCmsNewsId,
                form = "workflow-comments-modal form#idmelissbpagecomments",
                action = gActionNews,
                btn = "";
            if ($(this).hasClass('btnAddWorkflowCommentNews')) {                   
                btn = ".btnAddWorkflowCommentNews";                    
            } else {                  
                $('#id_pcom_text').val('');
                btn = ".btnCommentModalCloseNews";
            }    
            var datastring  = $("#" + form).serializeArray();
            datastring.push({
                name: "pcom_news_id",
                value: newsId,
            });
            datastring.push({
                name: "action",
                value: action,
            });
            datastring = $.param(datastring);
            melisCoreTool.pending(".btnAddWorkflowCommentNews");
            $.ajax({
                type    : 'POST',
                url     : '/melis/MelisSmallBusiness/MelisWorkflow/addWorkflowComments',
                data    : datastring,
                dataType: 'json',
                encode  : true
            }).done(function (data) {
                if (data.success) {                        
                    melisHelper.zoneReload(newsId + '_id_meliscmsnews_center_workflow_tabs_comments_timeline', 'meliscmsnews_workflow_comments_timeline', {newsId: newsId});
                    melisCoreTool.clearForm(form);
                    $('#workflowCommentModal').modal('hide');
                    $("#pageWorkflowModal").modal("hide");
                } else {
                    melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 0);
                    melisCoreTool.highlightErrors(data.success, data.errors, form);
                }
                melisCore.flashMessenger();
                melisCoreTool.done(".btnAddWorkflowCommentNews");
            }).fail(function() {
                alert( translations.tr_meliscore_error_message );
            });
        });
        $body.on('click', '.btnAddNewsComment', function(e) {          
            var btn         = $(this),
                newsId      = btn.data("newsid"),               
                form        = newsId + "_id_meliscmsnews_center_page_tabs_comments_add form#idmelissbpagecomments",
                datastring  = $("#"+form).serializeArray();
                datastring.push({
                    name: "pcom_news_id", 
                    value: newsId,
                });
                datastring = $.param(datastring);
                btn.attr('disabled', true);
                $.ajax({
                    type        : 'POST', 
                    url         : '/melis/MelisSmallBusiness/PageComments/addComment',
                    data        : datastring,
                    dataType    : 'json',
                    encode      : true
                }).done(function(data){
                    if (data.success) {
                        melisHelper.zoneReload(newsId + '_id_meliscmsnews_center_workflow_tabs_comments_timeline', 'meliscmsnews_workflow_comments_timeline', {newsId: newsId});
                        melisCoreTool.clearForm(form);
                    } else {
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                        melisCoreTool.highlightErrors(data.success, data.errors, form);
                    }
                    melisCore.flashMessenger();
                }).fail(function() {
                    alert( translations.tr_meliscore_error_message );
                }); 
        });
});
function renderNewsWorkFlowModal(pageData, button) {
    $.ajax({
        url: '/melis/MelisSmallBusiness/MelisWorkflow/render-workflow-modal',
        encode: true
    }).done(function (data) {
        $("#melis-modals-container").append(data);
        $("#melis-modals-container").find(".modal-content").data(pageData);
        var modalID = $(data).find(".modal").attr("id");
        melisHelper.zoneReload('id_melissb_workflow_modal_content', 'melissb_workflow_modal_content', pageData);
        $("#" + modalID).modal({show: true});
        melisCoreTool.done(button);
    }).fail(function (xhr, textStatus, errorThrown) {
        melisCoreTool.done(button);
        alert( translations.tr_meliscore_error_message );
    });
    var commentBtnSearch = setInterval(function () {
        $commentBtn = $("#melis-modals-container").find(".btnAddWorkflowComment");
        if ($commentBtn.length) {
            $commentBtn.removeClass();
            $commentBtn.addClass("btn btn-success pull-right btnAddWorkflowCommentNews");
            if ($("#melis-modals-container").find(".btnAddWorkflowCommentNews").length) {
                clearInterval(commentBtnSearch);
            }
        }
        $noCommentBtn = $("#melis-modals-container").find(".btnCommentModalClose");
        if ($noCommentBtn.length) {
            $noCommentBtn.removeClass();
            $noCommentBtn.addClass("btn btn-success pull-right btnCommentModalCloseNews");
            if ($("#melis-modals-container").find(".btnCommentModalCloseNews").length) {
                clearInterval(commentBtnSearch);
            }
        }
    }, 300);
}
function charCounter(event) {
    var charLength = $(this).val().length;
    var prevLabel = $(this).prev("label");
    var limit = event.data.limit;
    if (prevLabel.find("span").length) {
        if (charLength === 0) {
            prevLabel.removeClass("limit");
            prevLabel.find("span").remove();
        } else {
            prevLabel
                .find("span")
                .html('<i class="fa fa-text-width"></i>(' + charLength + ")");
            if (charLength > limit) {
                prevLabel.addClass("limit");
                prevLabel.find("span").addClass("limit");
            } else {
                prevLabel.removeClass("limit");
                prevLabel.find("span").removeClass("limit");
            }
        }
    } else {
        if (charLength !== 0) {
            prevLabel
                .find(".label-text")
                .append(
                    "<span class='text-counter-indicator'><i class='fa fa-text-width'></i>(" +
                        charLength +
                        ")</span>"
                );
            if (charLength > limit) {
                prevLabel.addClass("limit");
                prevLabel.find("span").addClass("limit");
            }
        }
    }
}

var toolNews = {
    refreshTable: function () {
        melisHelper.zoneReload("id_meliscmsnews_list_content_table", "meliscmsnews_list_content_table", {});
    },

    tabOpen: function (name, id) {
        melisHelper.tabOpen(name, 'fa fa-list-alt', id + '_id_meliscmsnews_page', 'meliscmsnews_page', {newsId: id}, 'id_meliscmsnews_left_menu');
    },

    tabClose: function (id) {
        melisHelper.tabClose(id + '_id_meliscmsnews_page');
    },

    imageModal: function (data) {
        $("#id_meliscmsnews_modal_documents_form_image_container").modal("hide");
        melisHelper.zoneReload(data.cnews_id + "_id_meliscmsnews_content_tabs_properties_details_left_images", "meliscmsnews_content_tabs_properties_details_left_images", {'newsId': data.cnews_id});
    },

    fileModal: function (data) {
        $("#id_meliscmsnews_modal_documents_form_image_container").modal("hide");
        $("#id_meliscmsnews_modal_documents_form_container").modal("hide");
        melisHelper.zoneReload(data.cnews_id + "_id_meliscmsnews_content_tabs_properties_details_left_documents", "meliscmsnews_content_tabs_properties_details_left_documents", {'newsId': data.cnews_id});
    },

    trimLength: function (text) {
        var maxLength   = 15,
            ellipsis    = "...";

            text = $.trim(text);

            if (text.length > maxLength) {
                text = text.substring(0, maxLength - ellipsis.length);

                return text.substring(0, text.lastIndexOf(" ")) + ellipsis;
            }
            else {
                return text;
            }
    },

    progress: function progress(e) {
        var progressContent = $("div.progressContent");
            progressContent.removeClass("hidden");

        var progressBar = $("div.progressContent > div.progress > div.progress-bar");
            progressBar.attr("aria-valuenow", 0);
            progressBar.css("width", '0%');

        var status = $("div.progressContent > div.progress > span.status");
            status.html("");

            if ( e.lengthComputable ) {
                var max         = e.total,
                    current     = e.loaded,
                    percentage  = (current * 100) / max;

                    progressBar.attr("aria-valuenow", percentage);
                    progressBar.css("width", percentage + "%");

                    if ( percentage > 100 ) {
                        progressContent.addClass("hidden");
                    }
                    else {
                        status.html(Math.round(percentage) + "%");
                    }
            }
    },
};

window.newsImagePreview = function (id, fileInput) {
    if ( fileInput.files && fileInput.files[0] ) {
        var reader = new FileReader();

            reader.onload = function (e) {
                $(id).attr('src', e.target.result);
            };
            reader.readAsDataURL(fileInput.files[0]);
    }
};

window.initNewsList = function (data) {
    var siteSelect = $("#newsSiteSelect");

        if ( siteSelect.length ) {
            data.cnews_site_id = siteSelect.val();
        }
};