/*reference: Melis Small Business/js/workflow.js*/
$(function() {
    var $body = $("body");

         /*Start News Workflow Dashboard*/

        // -=[ DASHBOARD VALIDATE & REFUSE ]=- workflow in dashboard to show validate confirmation
        $body.on("click",".meliscmsnews-dashboard-workflow .d-workflow-action-cont  span:not('.wd-see')", function() {

            // check the class of the button that is clicked if its validate or refuse and give it the url action
            var $this   = $(this),
                action  = ( $this.hasClass('wd-validate') ? "validate" : "refuse");
            
            // data of the button. needed in server
            var datastring =  $this.parents('.wd-cont').data();

                datastring['wfe-id'] = $this.data("wfe-id");
                datastring['wfe_wf_id'] = $this.data("wfe-wf-id");

                console.log('wfe-id: ' + $this.data("wfe-id"));
                console.log('wfe_wf_id: ' + $this.data("wfe-wf-id"));
            
            // get reloadable parent
            var zoneId      = $this.parents("div[data-meliskey]").attr('id'),
                meliskey    = $this.parents("div[data-meliskey]").attr('data-meliskey');
            
                // set global values
                gZoneId                 = zoneId;
                gMelisKey               = meliskey;
                gDataString             = datastring;
                gAction                 = action;
                pluginId                = $this.parents(".grid-stack-item").data("gsId");
                currentWorkflowNewsId   = datastring.wfId;

                melisCoreTool.confirm(
                        translations.tr_meliscore_common_yes, 
                        translations.tr_meliscore_common_no, 
                        translations['tr_meliscmsnews_workflow_'+ action +'_notification_heading'], 
                        translations['tr_meliscmsnews_workflow_'+ action +'_notification_message'],  
                        function() {                                                       
                            $.ajax({                                
                                url         : '/melis/MelisCmsNews/MelisCmsNewsWorkflow/saveWorkflowActions?wfaction='+action,
                                data        : datastring,
                                encode      : true
                            }).done(function(data){
                                if(data.success === 1){
                                    $("div[data-gs-id='"+pluginId+"'] .dashboard-plugin-refresh").trigger("click");

                                    // update flash messenger values
                                    melisCore.flashMessenger();
                                    
                                    // call melisOkNotification 
                                    melisHelper.melisOkNotification( data.textTitle, data.textMessage );
                                
                                    // show comment modal
                                    wfCommentZoneId = 'id_meliscmsnews_dashboard_workflow_comment_modal_content';
                                    wfCommentMelisKey = 'meliscmsnews_dashboard_workflow_comment_modal_content';
                                    modalUrl = '/melis/MelisCmsNews/MelisCmsNewsWorkflow/workflowCommentModal';
                                    // requesting to create modal and display after
                                    melisHelper.createModal(wfCommentZoneId, wfCommentMelisKey, false, {cnews_com_news_id: currentWorkflowNewsId, action : gAction, pluginId : pluginId}, modalUrl);
                                } else {
                                    // error modal
                                    melisHelper.melisKoNotification( data.textTitle, data.textMessage, data.errors );
                                }
                                
                            }).fail(function(xhr, textStatus, errorThrown){
                                alert("ERROR !! Status = "+ textStatus + "\n Error = "+ errorThrown + "\n xhr = "+ xhr.statusText);
                            });
                });
        });
        
        // show - hide the demand informations
        $body.on("click",".dashboard-workflow-tabs-news .dashboard-widget-workflow ul.list li:not('i.fa')", function(e) {
            var $this = $(this);

                if ( e.target.nodeName !== "I" ) {
                    $this.find('.wd-cont-content').slideToggle();
                    $this.find('span.fa-arrow-down').toggleClass('move-arrow');
                }
        });

        $body.on('click', '.btnAddWorkflowCommentFromDashboardNews, .btnNoWorkflowCommentFromDashboardNews', function(e) {
            var $this       = $(this),
                pluginId    = $this.data("pluginId"),
                form        = "news-db-wf-comment-modal-"+pluginId,
                btn         = "";

                if ($(this).hasClass('btnAddWorkflowCommentFromDashboardNews')) {                   
                    btn = ".btnAddWorkflowCommentFromDashboardNews";
                    
                } else {                  
                    //clear comment value 
                    $('#id_cnews_com_text').val('');
                    btn = ".btnNoWorkflowCommentFromDashboardNews";
                } 

                var datastring  = $("#"+form).serializeArray();            
                datastring = $.param(datastring);

                melisCoreTool.pending(btn);
            
                $.ajax({
                    type        : 'POST', 
                    url         : '/melis/MelisCmsNews/MelisCmsNewsWorkflow/addWorkflowComments',
                    data        : datastring,
                    dataType    : 'json',
                    encode      : true
                }).done(function(data) {
                    if ( data.success ) {
                        // Closing Comment modal
                        $("#id_meliscmsnews_dashboard_workflow_comment_modal_content_container").modal("hide");      
                    }
                    else {
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 0);
                        melisCoreTool.highlightErrors(data.success, data.errors, form);
                    }
                    
                    melisCore.flashMessenger();
                    melisCoreTool.done(btn);
                }).fail(function() {
                    alert( translations.tr_meliscore_error_message );
                }); 
        });

        /*End News Workflow Dashboard*/

        /**
         * click 'Workflow' button in News page
         */
        $body.on("click", ".news-details-workflow", function() {
            var $this = $(this);
            if ( $this.attr('disabled') == undefined ) {
                var pageData = $this.data();
                    melisCoreTool.pending('.news-details-workflow');
         
                    $.ajax({
                        url: '/melis/MelisCmsNews/MelisCmsNewsWorkflow/render-workflow-modal',
                        encode: true
                    }).done(function (data) {
                        // append the modal to the modal-container
                        $("#melis-modals-container").append(data);

                        // add pageData attributes to the modal so we can use it
                        $("#melis-modals-container").find(".modal-content").data(pageData);

                        // get modal ID so we can trigger it
                        var modalID = $(data).find(".modal").attr("id");

                        // reload the zone so the content is fetched from the server
                        melisHelper.zoneReload('id_meliscmsnews_workflow_modal_content', 'meliscmsnews_workflow_modal_content', pageData);

                        // trigger modal
                        $("#" + modalID).modal({show: true});
                        melisCoreTool.done('.news-details-workflow');
                    }).fail(function (xhr, textStatus, errorThrown) {
                        melisCoreTool.done('.news-details-workflow');
                        alert( translations.tr_meliscore_error_message );
                    });

                    /**
                     * Change the class attribute of Comment button,
                     * once we have our custom comment button, we can now use our own events
                     */
                    // var commentBtnSearch = setInterval(function () {
                    //     $commentBtn = $("#melis-modals-container").find(".btnAddWorkflowComment");
                    //     if ($commentBtn.length) {
                    //         $commentBtn.removeClass();
                    //         $commentBtn.addClass("btn btn-success pull-right btnAddWorkflowComment-news");
                    //         if ($("#melis-modals-container").find(".btnAddWorkflowComment-news").length) {
                    //             clearInterval(commentBtnSearch);
                    //         }
                    //     }
                    // }, 300);
            }
        });

        // click 'Demand' button in modal
        $body.on("click",".workflow-modal-cont-news .wbtn-cont .pw-show-ask", function() {
            var $this = $(this);
            $this.parents('.workflow-modal-cont-news').find(".w-asktovalidate-cont-news").slideToggle(500);   
        });
        
        // workflow dropdown functionalities [ adding data-value to the parent ] (users and roles)
        $body.on("click", ".w-asktovalidate-cont-news .dropdown-menu li a", function() {      
            var $this   = $(this),
                value   = $this.data('text'),
                Id      = $this.data("id");

                $this.parents('.filter-cont').attr('data-id', Id);
                $this.parents('.filter-cont').find('span.filter-key').text(value);
        });
        
        // populate the content of the users dropdown button based from selected role
        $body.on("click", ".w-asktovalidate-cont-news .pw-roles .dropdown-menu li a", function() {       
            var $this   = $(this),
                Id      = $this.data("id");
                
                $(".pw-users").append('<div id="loader" class="overlay-loader button-loader"><img class="loader-icon spinning-cog" src="/MelisCore/assets/images/cog12.svg" data-cog="cog12"></div>');
            
                $.ajax({
                    url         : '/melis/MelisCmsNews/MelisCmsNewsWorkflow/getUserListPerRole?roleId='+ Id,
                    encode      : true
                }).done(function(data){
                    
                    // hide the loader
                    $('.loader-icon').removeClass('spinning-cog').addClass('shrinking-cog');
                    
                    $(".pw-users #loader, .pw-users .button-mask-overlay").delay(350).fadeOut(0, function(){
                        $(".pw-users #loader").remove();
                    });
                    
                    // if returned data is empty. no dropdown list to populate for Users dropdown
                    if( data === '' || data.length === 0 ){
                        $('.pw-users .button-mask-overlay').fadeIn(0);
                    }
                    
                    // add the new values for the Users dropdown
                    $(".pw-users ul.dropdown-menu").html(data);
                    
                    // show notification and make it fade away after 2s + 1s delay 3s all
                    // $(this).next('.wrequest-notif').fadeIn(500).delay(2000).fadeOut(500);
                    
                }).fail(function(xhr, textStatus, errorThrown){
                    alert("ERROR !! Status = "+ textStatus + "\n Error = "+ errorThrown + "\n xhr = "+ xhr.statusText);
                }); 
        });
        
        // -=[ SEND REQUEST ]=- send request after you clicked on the send request button
        $body.on("click", ".workflow-modal-cont-news .w-asktovalidate-cont-news .send-workflow-request", function() {
            var $this       = $(this),
                pageId      = activeTabId.split("_")[0],
                user        = $this.siblings(".pw-users").data("id"),
                roles       = $this.siblings(".pw-roles").data("id"),
                zoneId      = $this.parents(".modal-content").find("div[data-meliskey]").attr('id'),
                meliskey    = $this.parents(".modal-content").find("div[data-meliskey]").attr('data-meliskey'),         
                modalDatas  = $this.parents(".modal-content").data(),
                datastring  = $this.parents(".modal-content").data();

                datastring.user = user;
                datastring.roles = roles;
                datastring.ask = "ask";

                // /MelisCmsNews/MelisCmsNewsWorkflow/saveActions
                // parameter: wfaction=ask roleId=1 userId=1
                gAction     = datastring.ask;
                gZoneId     = zoneId;
                gMelisKey   = meliskey;
                gDataString = datastring;
                
                if ( !$this.attr("disabled") ) {
                    $(".workflow-modal-cont-news .w-asktovalidate-cont-news .send-workflow-request").attr("disabled", true);
                    $.ajax({
                        url         : '/melis/MelisCmsNews/MelisCmsNewsWorkflow/saveWorkflowAsk',
                        data        : datastring,
                        encode      : true
                    }).done(function(data){

                        // show notification and make it fade away after 2s + 1s delay 3s all
                        // $(this).next('.wrequest-notif').fadeIn(500).delay(2000).fadeOut(500);

                        if (data.success === 1) {

                            // update flash messenger values
                            melisCore.flashMessenger();

                            // call melisOkNotification
                            melisHelper.melisOkNotification( data.textTitle, data.textMessage );

                            // zoneReload the modal content to update
                            //melisHelper.zoneReload(zoneId, meliskey, modalDatas);

                            $("#workflow-comments-modal form#idmeliscmsnewscomments #id_cnews_com_title").attr("disabled", "disabled");
                            $("#workflow-comments-modal form#idmeliscmsnewscomments #id_cnews_com_title").parent().hide();
                            $('#workflowCommentModalNews').modal('show');
                            $(document).on('show.bs.modal','#workflowCommentModalNews', function () {
                                $(".workflow-modal-cont-news .w-asktovalidate-cont-news .send-workflow-request").removeAttr("disabled");
                            })
                        } else {

                            // error modal
                            melisHelper.melisKoNotification( data.textTitle, data.textMessage, data.errors );
                            $(".workflow-modal-cont-news .w-asktovalidate-cont-news .send-workflow-request").removeAttr("disabled");
                        }
                    }).fail(function(xhr, textStatus, errorThrown){
                        alert("ERROR !! Status = "+ textStatus + "\n Error = "+ errorThrown + "\n xhr = "+ xhr.statusText);
                        $(".workflow-modal-cont-news .w-asktovalidate-cont-news .send-workflow-request").removeAttr("disabled");
                    });
                }
        });

        // -=[ VALIDATE & REFUSE ]=- workflow in news modal to validate or refuse demand
        $body.on("click",".workflow-modal-cont-news .btn-validate-refuse", function() {            
            var $this       = $(this),
                pageId      = activeTabId.split("_")[0],
                datastring  = $this.parents(".modal-content").data();

                datastring['wfe-id']    = $this.data("wfe-id");
                datastring['wfe_wf_id'] = $this.data("wfe_wf_id");

            var zoneId      = $this.parents(".modal-content").find("div[data-meliskey]").attr('id'),
                meliskey    = $this.parents(".modal-content").find("div[data-meliskey]").attr('data-meliskey'),
                action      = ( $this.hasClass('pw-validate') ? "validate" : "refuse" );

                // set global values
                gZoneId     = zoneId;
                gMelisKey   = meliskey;
                gDataString = datastring;
                gAction     = action;

                melisCoreTool.confirm(
                    translations.tr_meliscore_common_yes,
                    translations.tr_meliscore_common_no,
                    translations['tr_meliscmsnews_workflow_'+ action +'_notification_heading'],
                    translations['tr_meliscmsnews_workflow_'+ action +'_notification_message'],
                    function() {
                        $.ajax({
                            url         : '/melis/MelisCmsNews/MelisCmsNewsWorkflow/saveWorkflowActions?wfaction='+action,
                            data        : datastring,
                            encode      : true
                        }).done(function(data){
                            if ( data.success === 1 ) {
                                $("#workflow-comments-modal form#idmeliscmsnewscomments #id_cnews_com_title").attr("disabled", "disabled");
                                $("#workflow-comments-modal form#idmeliscmsnewscomments #id_cnews_com_title").parent().hide();
                                $('#workflowCommentModalNews').modal('show');

                                // call melisOkNotification
                                melisHelper.melisOkNotification( data.textTitle, data.textMessage, '#72af46' );

                                // update flash messenger values
                                melisCore.flashMessenger();
                            }
                            else {
                                // error modal
                                melisHelper.melisKoNotification( data.textTitle, data.textMessage, data.errors );
                            }

                        }).fail(function(xhr, textStatus, errorThrown){
                            alert("ERROR !! Status = "+ textStatus + "\n Error = "+ errorThrown + "\n xhr = "+ xhr.statusText);
                        });
                    });
        });

         /**
         * Handles the request when user adds a comment to the workflow request
         */
        $body.on('click', '.btnAddWorkflowCommentNews, .btnCommentModalCloseNews', function (e) {           
            var newsId      = $("#melis-modals-container").find(".modal-content").data('wf-id'),
                form        = "workflow-comments-modal form#idmeliscmsnewscomments",
                action      = "ask",
                btn = "";
                              
                if ($(this).hasClass('btnAddWorkflowCommentNews')) {                   
                    btn = ".btnAddWorkflowCommentNews";
                    
                } else {                  
                    //clear comment value 
                    $('#id_cnews_com_text').val('');
                    btn = ".btnCommentModalCloseNews";
                } 

                melisCoreTool.pending(btn);

                var datastring  = $("#" + form).serializeArray();
                datastring.push({
                    name: "cnews_com_news_id",
                    value: newsId,
                });

                datastring.push({
                    name: "action",
                    value: action,
                });
                
                datastring = $.param(datastring);
                
                $.ajax({
                    type    : 'POST',
                    url     : '/melis/MelisCmsNews/MelisCmsNewsWorkflow/addWorkflowComments',
                    data    : datastring,
                    dataType: 'json',
                    encode  : true
                }).done(function (data) {
                    if (data.success) {                                           
                        melisHelper.zoneReload(newsId + '_id_meliscmsnews_center_workflow_tabs_comments_timeline', 'meliscmsnews_workflow_comments_timeline', {newsId: newsId});
                        
                        melisCoreTool.clearForm(form);
                        $('#workflowCommentModalNews').modal('hide');
                        $("#newsWorkflowModal").modal("hide");
                    } else {
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 0);
                        melisCoreTool.highlightErrors(data.success, data.errors, form);
                    }

                    melisCore.flashMessenger();        
                    melisCoreTool.done(btn);

                }).fail(function() {
                    alert( translations.tr_meliscore_error_message );
                });
        });
                 
        /*Add News Comment on Workflow Comment tab*/
        $body.on('click', '.btnAddNewsComment', function(e) {
            var btn         = $(this),
                newsId      = btn.data("newsid"),               
                form        = newsId + "_id_meliscmsnews_center_page_tabs_comments_add form#idmeliscmsnewscomments",
                datastring  = $("#"+form).serializeArray();
                
                datastring.push({
                    name: "cnews_com_news_id", 
                    value: newsId,
                });
                
                datastring = $.param(datastring);
                
                btn.attr('disabled', true);
                
                $.ajax({
                    type        : 'POST', 
                    url         : '/melis/MelisCmsNews/MelisCmsNewsWorkflowComments/addComment',
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
       
        /*End Workflow*/       
});



