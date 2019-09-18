$(function() {
    var $body = $("body");

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
                    var forms   = $('#' + newsId + '_id_meliscmsnews_content_tabs_properties_details_right_paragraphs form#newsSiteTitleSubtitleForm'),
                        ctr     = 0,
                        len     = 1;

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
                            }
                            melisCore.flashMessenger();
                            melisCoreTool.done(".saveNewsLetter");
                        }).fail(function () {
                            melisCoreTool.done(".saveNewsLetter");
                            alert( translations.tr_meliscore_error_message );
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
    }
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

window.paginateDataTables = function() {
    // paginate dataTables data
    melisCore.paginateDataTables();
};
/*! ============================================================
 * bootstrapSwitch v1.8 by Larentis Mattia @SpiritualGuru
 * http://www.larentis.eu/
 * 
 * Enhanced for radiobuttons by Stein, Peter @BdMdesigN
 * http://www.bdmdesign.org/
 *
 * Project site:
 * http://www.larentis.eu/switch/
 * ============================================================
 * Licensed under the Apache License, Version 2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 * ============================================================ */

!function ($) {
  "use strict";

  $.fn['bootstrapSwitch'] = function (method) {
    var inputSelector = 'input[type!="hidden"]';
    var methods = {
      init: function () {
        return this.each(function () {
            var $element = $(this)
              , $div
              , $switchLeft
              , $switchRight
              , $label
              , $form = $element.closest('form')
              , myClasses = ""
              , classes = $element.attr('class')
              , color
              , moving
              , onLabel = "ON"
              , offLabel = "OFF"
              , icon = false
              , textLabel = false;

            $.each(['switch-mini', 'switch-small', 'switch-large'], function (i, el) {
              if (classes.indexOf(el) >= 0)
                myClasses = el;
            });

            $element.addClass('has-switch');

            if ($element.data('on') !== undefined)
              color = "switch-" + $element.data('on');

            if ($element.data('on-label') !== undefined)
              onLabel = $element.data('on-label');

            if ($element.data('off-label') !== undefined)
              offLabel = $element.data('off-label');

            if ($element.data('label-icon') !== undefined)
              icon = $element.data('label-icon');

            if ($element.data('text-label') !== undefined)
              textLabel = $element.data('text-label');

            $switchLeft = $('<span>')
              .addClass("switch-left")
              .addClass(myClasses)
              .addClass(color)
              .html(onLabel);

            color = '';
            if ($element.data('off') !== undefined)
              color = "switch-" + $element.data('off');

            $switchRight = $('<span>')
              .addClass("switch-right")
              .addClass(myClasses)
              .addClass(color)
              .html(offLabel);

            $label = $('<label>')
              .html("&nbsp;")
              .addClass(myClasses)
              .attr('for', $element.find(inputSelector).attr('id'));

            if (icon) {
              $label.html('<i class="icon ' + icon + '"></i>');
            }

            if (textLabel) {
              $label.html('' + textLabel + '');
            }

            $div = $element.find(inputSelector).wrap($('<div>')).parent().data('animated', false);

            if ($element.data('animated') !== false)
              $div.addClass('switch-animate').data('animated', true);

            $div
              .append($switchLeft)
              .append($label)
              .append($switchRight);

            $element.find('>div').addClass(
              $element.find(inputSelector).is(':checked') ? 'switch-on' : 'switch-off'
            );

            if ($element.find(inputSelector).is(':disabled'))
              $(this).addClass('deactivate');

            var changeStatus = function ($this) {
              if ($element.parent('label').is('.label-change-switch')) {

              } else {
                $this.siblings('label').trigger('mousedown').trigger('mouseup').trigger('click');
              }
            };

            $element.on('keydown', function (e) {
              if (e.keyCode === 32) {
                e.stopImmediatePropagation();
                e.preventDefault();
                changeStatus($(e.target).find('span:first'));
              }
            });

            $switchLeft.on('click', function (e) {
              changeStatus($(this));
            });

            $switchRight.on('click', function (e) {
              changeStatus($(this));
            });

            $element.find(inputSelector).on('change', function (e, skipOnChange) {
              var $this = $(this)
                , $element = $this.parent()
                , thisState = $this.is(':checked')
                , state = $element.is('.switch-off');

              e.preventDefault();

              $element.css('left', '');

              if (state === thisState) {

                if (thisState)
                  $element.removeClass('switch-off').addClass('switch-on');
                else $element.removeClass('switch-on').addClass('switch-off');

                if ($element.data('animated') !== false)
                  $element.addClass("switch-animate");

                if (typeof skipOnChange === 'boolean' && skipOnChange)
                  return;

                $element.parent().trigger('switch-change', {'el': $this, 'value': thisState})
              }
            });

            $element.find('label').on('mousedown touchstart', function (e) {
              var $this = $(this);
              moving = false;

              e.preventDefault();
              e.stopImmediatePropagation();

              $this.closest('div').removeClass('switch-animate');

              if ($this.closest('.has-switch').is('.deactivate')) {
                $this.unbind('click');
              } else if ($this.closest('.switch-on').parent().is('.radio-no-uncheck')) {
                $this.unbind('click');
              } else {
                $this.on('mousemove touchmove', function (e) {
                  var $element = $(this).closest('.make-switch')
                    , relativeX = (e.pageX || e.originalEvent.targetTouches[0].pageX) - $element.offset().left
                    , percent = (relativeX / $element.width()) * 100
                    , left = 25
                    , right = 75;

                  moving = true;

                  if (percent < left)
                    percent = left;
                  else if (percent > right)
                    percent = right;

                  $element.find('>div').css('left', (percent - right) + "%")
                });

                $this.on('click touchend', function (e) {
                  var $this = $(this)
                    , $target = $(e.target)
                    , $myRadioCheckBox = $target.siblings('input');

                  e.stopImmediatePropagation();
                  e.preventDefault();

                  $this.unbind('mouseleave');

                  if (moving)
                    $myRadioCheckBox.prop('checked', !(parseInt($this.parent().css('left')) < -25));
                  else
                    $myRadioCheckBox.prop("checked", !$myRadioCheckBox.is(":checked"));

                  moving = false;
                  $myRadioCheckBox.trigger('change');
                });

                $this.on('mouseleave', function (e) {
                  var $this = $(this)
                    , $myInputBox = $this.siblings('input');

                  e.preventDefault();
                  e.stopImmediatePropagation();

                  $this.unbind('mouseleave');
                  $this.trigger('mouseup');

                  $myInputBox.prop('checked', !(parseInt($this.parent().css('left')) < -25)).trigger('change');
                });

                $this.on('mouseup', function (e) {
                  e.stopImmediatePropagation();
                  e.preventDefault();

                  $(this).unbind('mousemove');
                });
              }
            });

            if ($form.data('bootstrapSwitch') !== 'injected') {
              $form.bind('reset', function () {
                setTimeout(function () {
                  $form.find('.make-switch').each(function () {
                    var $input = $(this).find(inputSelector);

                    $input.prop('checked', $input.is(':checked')).trigger('change');
                  });
                }, 1);
              });
              $form.data('bootstrapSwitch', 'injected');
            }
          }
        );
      },
      toggleActivation: function () {
        var $this = $(this);

        $this.toggleClass('deactivate');
        $this.find(inputSelector).prop('disabled', $this.is('.deactivate'));
      },
      isActive: function () {
        return !$(this).hasClass('deactivate');
      },
      setActive: function (active) {
        var $this = $(this);

        if (active) {
          $this.removeClass('deactivate');
          $this.find(inputSelector).removeAttr('disabled');
        }
        else {
          $this.addClass('deactivate');
          $this.find(inputSelector).attr('disabled', 'disabled');
        }
      },
      toggleState: function (skipOnChange) {
        var $input = $(this).find(':checkbox');
        $input.prop('checked', !$input.is(':checked')).trigger('change', skipOnChange);
      },
      toggleRadioState: function (skipOnChange) {
        var $radioinput = $(this).find(':radio');
        $radioinput.not(':checked').prop('checked', !$radioinput.is(':checked')).trigger('change', skipOnChange);
      },
      toggleRadioStateAllowUncheck: function (uncheck, skipOnChange) {
        var $radioinput = $(this).find(':radio');
        if (uncheck) {
          $radioinput.not(':checked').trigger('change', skipOnChange);
        }
        else {
          $radioinput.not(':checked').prop('checked', !$radioinput.is(':checked')).trigger('change', skipOnChange);
        }
      },
      setState: function (value, skipOnChange) {
        $(this).find(inputSelector).prop('checked', value).trigger('change', skipOnChange);
      },
      setOnLabel: function (value) {
        var $switchLeft = $(this).find(".switch-left");
        $switchLeft.html(value);
      },
      setOffLabel: function (value) {
        var $switchRight = $(this).find(".switch-right");
        $switchRight.html(value);
      },
      setOnClass: function (value) {
        var $switchLeft = $(this).find(".switch-left");
        var color = '';
        if (value !== undefined) {
          if ($(this).attr('data-on') !== undefined) {
            color = "switch-" + $(this).attr('data-on')
          }
          $switchLeft.removeClass(color);
          color = "switch-" + value;
          $switchLeft.addClass(color);
        }
      },
      setOffClass: function (value) {
        var $switchRight = $(this).find(".switch-right");
        var color = '';
        if (value !== undefined) {
          if ($(this).attr('data-off') !== undefined) {
            color = "switch-" + $(this).attr('data-off')
          }
          $switchRight.removeClass(color);
          color = "switch-" + value;
          $switchRight.addClass(color);
        }
      },
      setAnimated: function (value) {
        var $element = $(this).find(inputSelector).parent();
        if (value === undefined) value = false;
        $element.data('animated', value);
        $element.attr('data-animated', value);

        if ($element.data('animated') !== false) {
          $element.addClass("switch-animate");
        } else {
          $element.removeClass("switch-animate");
        }
      },
      setSizeClass: function (value) {
        var $element = $(this);
        var $switchLeft = $element.find(".switch-left");
        var $switchRight = $element.find(".switch-right");
        var $label = $element.find("label");
        $.each(['switch-mini', 'switch-small', 'switch-large'], function (i, el) {
          if (el !== value) {
            $switchLeft.removeClass(el);
            $switchRight.removeClass(el);
            $label.removeClass(el);
          } else {
            $switchLeft.addClass(el);
            $switchRight.addClass(el);
            $label.addClass(el);
          }
        });
      },
      status: function () {
        return $(this).find(inputSelector).is(':checked');
      },
      destroy: function () {
        var $element = $(this)
          , $div = $element.find('div')
          , $form = $element.closest('form')
          , $inputbox;

        $div.find(':not(input)').remove();

        $inputbox = $div.children();
        $inputbox.unwrap().unwrap();

        $inputbox.unbind('change');

        if ($form) {
          $form.unbind('reset');
          $form.removeData('bootstrapSwitch');
        }

        return $inputbox;
      }
    };

    if (methods[method])
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    else if (typeof method === 'object' || !method)
      return methods.init.apply(this, arguments);
    else
      $.error('Method ' + method + ' does not exist!');
  };
}(jQuery);
