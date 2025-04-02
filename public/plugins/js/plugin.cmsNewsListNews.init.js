function cmsNewsListNews_init(pluginId){
    // declaring parameters variable for old / cross browser compatability
    if(typeof idPlugin === "undefined") idPlugin = null;

	// Checking if the plugin is not null
	if(pluginId == null || !$("#"+pluginId).length){
		return false;
	}
	
	/**
	 * Date pickers initializations
	 */
    $("#"+pluginId+" #date-min ,#"+pluginId+" #date-max").datetimepicker({
    	useCurrent : false,
    	format : 'MM/DD/YYYY',
    	dayViewHeaderFormat : 'MMMM YYYY',
    	showClear : true,
    	showClose : true,
    	ignoreReadonly : true,
    	keepOpen : true,
        maxDate: moment().endOf('day')
    });
    // Pre-init of date min if date max has value
    if($("#"+pluginId+" #date-max").val().length){
    	$("#"+pluginId+" #date-min").data("DateTimePicker").maxDate($("#"+pluginId+" #date-max").val());
    }
    // Pre-init of date max if date min has value
    if($("#"+pluginId+" #date-min").val().length){
    	$("#"+pluginId+" #date-max").data("DateTimePicker").minDate($("#"+pluginId+" #date-min").val());
    }
    // Datepickers events
    $("#"+pluginId+" #date-min").on("dp.change", function (e) {
        $("#"+pluginId+" #date-max").data("DateTimePicker").minDate(e.date);
    });
    $("#"+pluginId+" #date-max").on("dp.change", function (e) {
        $("#"+pluginId+" #date-min").data("DateTimePicker").maxDate(e.date);
    });
}

$(function(){
	cmsNewsListNews_init("news-list-plugin");
})