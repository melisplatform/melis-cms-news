$(document).ready(function () {
    var body = $("body");

    body.on("click", '.addNews', function () {
        var newsId = 0;
        var name = translations.tr_meliscmsnews_list_header_title_new;
        toolNews.tabOpen(name, newsId);
    });

    body.on("click", '.newsEdit', function () {
        var newsId = $(this).closest('tr').attr('id');
        var name = $(this).closest('tr').find("td:nth-child(3)").text();
        toolNews.tabOpen(name, newsId);
    });

    body.on("click", '.newsListRefresh', function () {
        toolNews.refreshTable();
    });

    body.on("click", ".newsDelete", function () {
        var newsId = $(this).closest('tr').attr('id');
        var ajaxUrl = '/melis/MelisCmsNews/MelisCmsNewsList/deleteNews';
        var dataString = [];
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
                }).success(function (data) {
                    if (data.success) {
                        melisHelper.melisOkNotification(data.textTitle, data.textMessage);
                        toolNews.tabClose(newsId);
                        melisHelper.zoneReload("id_meliscmsnews_list_content_table", "meliscmsnews_list_content_table", {});
                    } else {
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                    }
                    melisCore.flashMessenger();
                }).error(function () {
                    console.log('failed');
                });
            });

        melisCoreTool.done(".newsDelete");
    });

    body.on("click", ".removeAttachFile", function () {
        var newsId = $(this).data('newsid');
        var column = $(this).data('column');
        var type = $(this).data('type');
        var ajaxUrl = '/melis/MelisCmsNews/MelisCmsNews/removeAttachFile';
        var dataString = [];
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
            function () {
                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: dataString,
                    dataType: 'json',
                    encode: true
                }).success(function (data) {
                    if (data.success) {
                        melisHelper.melisOkNotification(data.textTitle, data.textMessage);
                        melisHelper.zoneReload(newsId + "_id_meliscmsnews_content_tabs_properties_details_left_images", "meliscmsnews_content_tabs_properties_details_left_images", {'newsId': newsId});
                        melisHelper.zoneReload(newsId + "_id_meliscmsnews_content_tabs_properties_details_left_documents", "meliscmsnews_content_tabs_properties_details_left_documents", {'newsId': newsId});
                    } else {
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                    }
                    melisCore.flashMessenger();
                }).error(function () {
                    console.log('failed');
                });
            });

        melisCoreTool.done(".removeAttachFile");
    });

    body.on("click", ".saveNewsLetter", function () {
        if ($(this).attr('disabled') === undefined) {
            melisCoreTool.pending(".saveNewsLetter");
            var ajaxUrl = '/melis/MelisCmsNews/MelisCmsNews/saveNewsLetter';
            var newsPage = $(this).closest('.container-level-a');
            var dataString = newsPage.find('form#newsLetterForm').serializeArray();
            var newsId = newsPage.data('newsid');
            var selectedSlider = newsPage.find('select[name=cnews_slider_id]').val();
            var selectedSite = newsPage.find('select[name=cnews_site_id]').val();

            //newsSiteTitleSubtitleForm
            var forms = $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs form#newsSiteTitleSubtitleForm');
            var ctr = 0;
            var len = 1;
            forms.each(function () {
                dataString.push({
                    name: 'cnews_title[' + ctr + ']',
                    value: $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs #cnews_' + len + ' ' + '#cnews_title').val()
                });
                dataString.push({
                    name: 'cnews_subtitle[' + ctr + ']',
                    value: $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs #cnews_' + len + ' ' + '#cnews_subtitle').val()
                });
                dataString.push({
                    name: 'cnews_lang_id' + "[" + ctr + "]",
                    value: $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs .product-text-tab #news_cms_lang_' + len).attr("data-lang-id")
                });

                for (var i = 1; i <= 4; i++) {
                    dataString.push({
                        name: 'cnews_paragraph' + i + "[" + ctr + "]",
                        value: $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs #cnews_' + len + ' ' + '#cnews_paragraph' + i).val()
                    });
                }
                ctr++;
                len++;
            });

            dataString.push({name: 'formCount', value: forms.length});
            //end

            newsPage.find('.make-switch div').each(function () {
                var field = $(this).find('input').attr('name');
                var status = $(this).hasClass('switch-on');
                var saveStatus = (status) ? 1 : 0;
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
            }).success(function (data) {
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
                        var allTitleLabels = body.find("#" + data.chunk.cnews_id + "_id_meliscmsnews_content_tabs_properties_details_right_paragraphs label[for='cnews_title']");
                        if (allTitleLabels.length) {
                            allTitleLabels.css("color", "red");
                        }
                    }
                }
                melisCore.flashMessenger();
                melisCoreTool.done(".saveNewsLetter");
            }).error(function () {
                melisCoreTool.done(".saveNewsLetter");
                console.log('failed');
            });
        }
    });

    body.on("click", '.newsAttachFile', function () {
        melisCoreTool.pending('.newsAttachFile');
        var newsId = $(this).data('newsid');
        var type = $(this).data('filetype');
        // initialation of local variable
        var zoneId = 'id_meliscmsnews_modal_documents_form';
        var melisKey = 'meliscmsnews_modal_documents_form';
        var modalUrl = '/melis/MelisCmsNews/MelisCmsNews/renderModal';
        // requesitng to create modal and display after
        melisHelper.createModal(zoneId, melisKey, false, {
            'newsId': newsId,
            'type': type,
            'isNew': 1
        }, modalUrl, function () {
            melisCoreTool.done('.newsAttachFile');
        });
    });

    body.on("click", '.newsAttachImage', function () {
        melisCoreTool.pending('.newsAttachImage');
        var newsId = $(this).data('newsid');
        var type = $(this).data('filetype');
        // initialation of local variable
        var zoneId = 'id_meliscmsnews_modal_documents_form_image';
        var melisKey = 'meliscmsnews_modal_documents_form_image';
        var modalUrl = '/melis/MelisCmsNews/MelisCmsNews/renderModal';
        // requesitng to create modal and display after
        melisHelper.createModal(zoneId, melisKey, false, {
            'newsId': newsId,
            'type': type,
            'isNew': 1
        }, modalUrl, function () {
            melisCoreTool.done('.newsAttachImage');
        });
    });

    body.on("click", '.newsEditImage', function () {
        melisCoreTool.pending('.newsAttachImage');
        var newsId = $(this).data('newsid');
        var type = $(this).data('filetype');
        var column = $(this).data('column');
        // initialation of local variable
        var zoneId = 'id_meliscmsnews_modal_documents_form_image';
        var melisKey = 'meliscmsnews_modal_documents_form_image';
        var modalUrl = '/melis/MelisCmsNews/MelisCmsNews/renderModal';
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

    body.on("click", "#newsAttachment", function () {
        var saveBtn = $(this);
        melisCoreTool.pending(saveBtn);

        var ajaxUrl = '/melis/MelisCmsNews/MelisCmsNews/saveFileForm';
        var tmpForm = $('#newsFileForm').get(0);
        var sliderData = new FormData(tmpForm);

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
        }).success(function (data) {
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
        }).error(function (event, xhr) {
            console.log('failed', JSON.parse(xhr.responseText));
            melisCoreTool.done(saveBtn);
        });
    });

    body.on("change", "select[name=cnews_site_id]", function () {
        var tableId = $(this).parents().eq(6).find('table').attr('id');
        $("#" + tableId).DataTable().ajax.reload();
    });

    /**
     * Preview tab: News details page selector
     */
    body.on("change", ".mcnews-page-details", function () {
        /** Get news ID, create the URI for "see-in-new-tab" button & iFrame src. */
        var pageId = this.value;
        var selectorData = $(this).data();

        if (typeof selectorData.newsId !== "undefined" && selectorData.newsId > 0 &&
            typeof selectorData.nameSpace.length !== "undefined" && selectorData.nameSpace.length > 0 &&
            typeof pageId !== "undefined" && pageId > 0) {
            var uri = "/id/" + pageId + "/preview?melisSite=" + selectorData.nameSpace + "&newsId=" + selectorData.newsId + "&renderMode=previewtab";

            /** Change href attributes for "Display below" & "Display in a new tab" buttons */
            var seeBelow = body.find("#" + selectorData.newsId + "-see-below");
            var seeNewTab = body.find("#" + selectorData.newsId + "-see-in-new-tab");
            seeBelow.attr({"data-uri": uri, "disabled": false});
            seeNewTab.attr({"href": uri, "disabled": false});
        }
    });

    /**
     * Preview tab: Display below event
     */
    body.on("click", ".preview-tab-see-below", function () {
        /** Load iFrame area using the URI as src */
        var newsId = this.id;
        var btn = this.dataset;

        if (typeof btn.uri !== "undefined" && btn.uri) {
            newsId = newsId.split("-")[0];
            if (newsId) {
                melisHelper.zoneReload(newsId + "_id_meliscmsnews_tabs_preview_iframe", "meliscmsnews_tabs_preview_iframe", {newsId: newsId, newsUri: btn.uri});
            }
        }
    });
});
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
        var maxLength = 15;
        var ellipsis = "...";
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
        if (e.lengthComputable) {
            var max = e.total;
            var current = e.loaded;
            var percentage = (current * 100) / max;
            progressBar.attr("aria-valuenow", percentage);
            progressBar.css("width", percentage + "%");

            if (percentage > 100) {
                progressContent.addClass("hidden");
            }
            else {
                status.html(Math.round(percentage) + "%");
            }
        }
    }

};
window.newsImagePreview = function (id, fileInput) {
    if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(id).attr('src', e.target.result);
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
};

window.initNewsList = function (data) {
    var siteSelect = $("#newsSiteSelect");
    if (siteSelect.length) {
        data.cnews_site_id = siteSelect.val();
    }
};
