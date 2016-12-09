$(document).ready(function(){
	var body = $("body");	
	
	body.on("click", '.addNews', function(){
		var newsId = 0;
		var name = translations.tr_meliscmsnews_list_header_title_new;
		toolNews.tabOpen(toolNews.trimLength(name), newsId);
	});
	
	body.on("click", '.newsEdit', function(){
		var newsId = $(this).closest('tr').attr('id');
		var name = $(this).closest('tr').find("td:nth-child(3)").text();
		toolNews.tabOpen(toolNews.trimLength(name), newsId);
	});
	
	body.on("click", '.newsListRefresh', function(){
		toolNews.refreshTable();
	});
	
	body.on("click", ".newsDelete", function(){ 
		var newsId   = $(this).closest('tr').attr('id');		
		var ajaxUrl = 'melis/MelisCmsNews/MelisCmsNewsList/deleteNews';
		var dataString = [];
		dataString.push({
			name : 'newsId',
			value: newsId,
		});
		melisCoreTool.pending(".newsDelete");
		
		melisCoreTool.confirm(
			translations.tr_meliscmsnews_common_label_yes,
			translations.tr_meliscmsnews_common_label_no,
			translations.tr_meliscmsnews_common_label_delete_news, 
			translations.tr_meliscmsnews_common_label_delete_confirm,
			function(){
				$.ajax({
			        type        : 'POST', 
			        url         : ajaxUrl,
			        data		: dataString,
			        dataType    : 'json',
			        encode		: true,
			     }).success(function(data){
			    	if(data.success){				
							melisHelper.melisOkNotification( data.textTitle, data.textMessage, '#72af46' );
							toolNews.tabClose(newsId);
							melisHelper.zoneReload("id_meliscmsnews_list_content_table", "meliscmsnews_list_content_table", {});
					}else{
						melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 'closeByButtonOnly');				
					}		
					melisCore.flashMessenger();	
			     }).error(function(){
			    	 console.log('failed');
			     });
		});
		
		melisCoreTool.done(".newsDelete");
	});	
	
	body.on("click", ".removeAttachFile", function(){ 
		var newsId   = $(this).data('newsid');
		var column	 = $(this).data('column');
		var type 	 = $(this).data('type');
		var ajaxUrl = 'melis/MelisCmsNews/MelisCmsNews/removeAttachFile';
		var dataString = [];
		dataString.push({
			name : 'newsId',
			value: newsId,
		});
		dataString.push({
			name : 'column',
			value: column,
		});
		melisCoreTool.pending(".removeAttachFile");
		
		melisCoreTool.confirm(
			translations.tr_meliscmsnews_common_label_yes,
			translations.tr_meliscmsnews_common_label_no,
			translations.tr_meliscmsnews_common_label_remove_file, 
			translations.tr_meliscmsnews_common_label_delete_confirm,
			function(){
				$.ajax({
			        type        : 'POST', 
			        url         : ajaxUrl,
			        data		: dataString,
			        dataType    : 'json',
			        encode		: true,
			     }).success(function(data){
			    	if(data.success){				
							melisHelper.melisOkNotification( data.textTitle, data.textMessage, '#72af46' );
							melisHelper.zoneReload(newsId+"_id_meliscmsnews_content_tabs_properties_details_left_images", "meliscmsnews_content_tabs_properties_details_left_images", {'newsId' : newsId});
							melisHelper.zoneReload(newsId+"_id_meliscmsnews_content_tabs_properties_details_left_documents", "meliscmsnews_content_tabs_properties_details_left_documents", {'newsId' : newsId});
					}else{
						melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 'closeByButtonOnly');				
					}		
					melisCore.flashMessenger();	
			     }).error(function(){
			    	 console.log('failed');
			     });
		});
		
		melisCoreTool.done(".removeAttachFile");
	});
	
	body.on("click", ".saveNewsLetter", function(){
		melisCoreTool.pending(".saveNewsLetter");
		var ajaxUrl = 'melis/MelisCmsNews/MelisCmsNews/saveNewsLetter';
		var newsPage = $(this).closest('.container-level-a');
		var dataString = newsPage.find('form#newsLetterForm').serializeArray();
		var newsId = newsPage.data('newsid');
		
		newsPage.find('.make-switch div').each(function(){
			var field = $(this).find('input').attr('name');
			var status = $(this).hasClass('switch-on');
			var saveStatus = 0;
			if(status) {
				saveStatus = 1;
			}
			dataString.push({ name : field, value : saveStatus});
		});
		
		var c = 1;
		newsPage.find('#'+newsId+'_id_meliscmsnews_content_tabs_properties_details_right_paragraphs textarea').each(function(){
			var field = 'cnews_paragraph'+c;
			var content = $(this).val();
			dataString.push({ name : field, value : content});			
			c++;
		});
		
		$.ajax({
	        type        : 'POST', 
	        url         : ajaxUrl,
	        data		: dataString,
	        dataType    : 'json',
	        encode		: true,
	     }).success(function(data){
	    	 if(data.success){
	    		 	toolNews.tabClose(newsId);
					melisHelper.melisOkNotification( data.textTitle, data.textMessage, '#72af46' );
					melisCore.flashMessenger();
					toolNews.tabOpen(toolNews.trimLength(data.chunk.cnews_title), data.chunk.cnews_id);
					toolNews.refreshTable();
			}else{
				melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 'closeByButtonOnly');				
			}
	    	 
	     }).error(function(){
	    	 console.log('failed');
	     });
		
		melisCoreTool.done(".saveNewsLetter");
	});
	
	body.on("click", '.newsAttachFile', function(){
		melisCoreTool.pending('.newsAttachFile');
		var newsId = $(this).data('newsid');
		var type = $(this).data('filetype');
		// initialation of local variable
		zoneId = 'id_meliscmsnews_modal_documents_form';
		melisKey = 'meliscmsnews_modal_documents_form';
		modalUrl = 'melis/MelisCmsNews/MelisCmsNews/renderModal';
		// requesitng to create modal and display after
    	melisHelper.createModal(zoneId, melisKey, false, {'newsId' : newsId, 'type' : type}, modalUrl, function(){
    		melisCoreTool.done('.newsAttachFile');
    	});
	});
	
	body.on("click", '.newsAttachImage', function(){
		melisCoreTool.pending('.newsAttachImage');
		var newsId = $(this).data('newsid');
		var type = $(this).data('filetype');
		// initialation of local variable
		zoneId = 'id_meliscmsnews_modal_documents_form_image';
		melisKey = 'meliscmsnews_modal_documents_form_image';
		modalUrl = 'melis/MelisCmsNews/MelisCmsNews/renderModal';
		// requesitng to create modal and display after
    	melisHelper.createModal(zoneId, melisKey, false, {'newsId' : newsId, 'type' : type}, modalUrl, function(){
    		melisCoreTool.done('.newsAttachImage');
    	});
	});
	
	body.on("click", "#newsAttachment", function(){
		var ajaxUrl = 'melis/MelisCmsNews/MelisCmsNews/saveFileForm';
		var newsId = $('form#newsFileForm input[name=cnews_id]').val();
		var tmpForm = $('#newsFileForm').get(0);		
		var sliderData = new FormData(tmpForm);

		$.ajax({
	        type        : 'POST', 
	        url         : ajaxUrl,
	        data		: sliderData,
	        dataType    : 'json',
	        processData : false,
			cache       : false,
			contentType : false,
	        encode		: true,
	        xhr: function() {
				var fileXhr = $.ajaxSettings.xhr();
				if(fileXhr.upload){
					fileXhr.upload.addEventListener('progress',toolNews.progress, false);
				}
				return fileXhr;
			},
	     }).success(function(data){
	    	 if(data.success){
	    		 	if(data.chunk.type == 'image'){
	    		 		toolNews.imageModal(data.chunk);
	    		 	}else{
	    		 		toolNews.fileModal(data.chunk);
	    		 	}
	    		 		    		 	
					melisHelper.melisOkNotification( data.textTitle, data.textMessage, '#72af46' );					
					melisCore.flashMessenger();
			}else{
				melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors, 'closeByButtonOnly');				
			}
	    	 $("div.progressContent").addClass("hidden");
	     }).error(function(){
	    	 console.log('failed');
	     });
		
		
	});
	
});
var toolNews = {
		
		refreshTable: function(){
			melisHelper.zoneReload("id_meliscmsnews_list_content_table", "meliscmsnews_list_content_table", {});
		},
		
		tabOpen: function(name, id){
			melisHelper.tabOpen(name, 'fa fa-list-alt', id+'_id_meliscmsnews_page', 'meliscmsnews_page', { newsId : id});
		},
		
		tabClose: function(id){
			melisHelper.tabClose(id+'_id_meliscmsnews_page');
		},
		
		imageModal: function(data){
			$("#id_meliscmsnews_modal_documents_form_image_container").modal("hide");
			melisHelper.zoneReload(data.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_images", "meliscmsnews_content_tabs_properties_details_left_images", {'newsId' : data.cnews_id});
		},
		
		fileModal: function(data){
			$("#id_meliscmsnews_modal_documents_form_container").modal("hide");	    		 	
		 	melisHelper.zoneReload(data.cnews_id+"_id_meliscmsnews_content_tabs_properties_details_left_documents", "meliscmsnews_content_tabs_properties_details_left_documents", {'newsId' : data.cnews_id});
		},
		
		trimLength : function (text){
			var maxLength = 15;
			var ellipsis = "...";
		    text = $.trim(text);

		    if (text.length > maxLength)
		    {
		        text = text.substring(0, maxLength - ellipsis.length)
		        return text.substring(0, text.lastIndexOf(" ")) + ellipsis;
		    }
		    else
		        return text;
		},
		
		progress : function progress(e) {
			$("div.progressContent").removeClass("hidden");
			$("div.progressContent > div.progress > div.progress-bar").attr("aria-valuenow", 0);
			$("div.progressContent > div.progress > div.progress-bar").css("width", '0%');
			$("div.progressContent > div.progress > span.status").html("");
			if(e.lengthComputable){
				var max = e.total;
				var current = e.loaded;
				var percentage = (current * 100)/max;
				$("div.progressContent > div.progress > div.progress-bar").attr("aria-valuenow", percentage);
				$("div.progressContent > div.progress > div.progress-bar").css("width", percentage+"%");

				if(percentage > 100)
				{
					$("div.progressContent").addClass("hidden");
				}
				else {
					$("div.progressContent > div.progress > span.status").html(Math.round(percentage)+"%");
				}
			}
		}
    	
}