
// $('.has-many-master .add').css('visibility','hidden');

$('#customer_organization').parents('.form-group').hide();
$(document).on('change','.customer_status_',function(){
    if($(this).val() === 'on'){
        $('#customer_organization').parents('.form-group').show();
    } else {
        $('#customer_organization').parents('.form-group').hide();
    };
});


function appendImplement(ev){
    $(window.master).select2('val', window.newImplement.master );
    $(window.startTime).val(window.newImplement.startTime);
    $(window.endTime).val(window.newImplement.endTime);
}

$(document).on('changeDate',function(ev){
    window.target = $( ev.target );

    var event = document.createEvent('Event');
    event.initEvent('initModal', true, true);
    document.dispatchEvent(event);

    window.master = $(window.target).parents('tr').find('select.master');
    window.startTime = $(window.target).parents('tr').find('input.start_date');
    window.endTime = $(window.target).parents('tr').find('input.end_date');

});

$(document).on('click','.close',function(){
   $('.grid-expand').remove();
   $('.modal').remove();
   $('.modal-backdrop').remove();
   $('body').removeClass('modal-open');
});

$(document).on('click','#ppp',function(){
    $('#rrr').val('dddd')

});


 function unserialize (str){
    var items = str.split('&');
    var ret = "{";
    var arrays = [];
    var index = "";
    for (var i = 0; i < items.length; i++) {
        var parts = items[i].split(/=/);
        //console.log(parts[0], parts[0].indexOf("%5B"),  parts[0].indexOf("["));
        if (parts[0].indexOf("%5B") > -1 || parts[0].indexOf("[") > -1){
            //Array serializado
            index = (parts[0].indexOf("%5B") > -1) ? parts[0].replace("%5B","").replace("%5D","") : parts[0].replace("[","").replace("]","");
            //console.log("array detectado:", index);
            //console.log(arrays[index] === undefined);
            if (arrays[index] === undefined){
                arrays[index] = [];
            }
            arrays[index].push( decodeURIComponent(parts[1].replace(/\+/g," ")));
            //console.log("arrays:", arrays);
        } else {
            //console.log("common item (not array)");
            if (parts.length > 1){
                ret += "\""+parts[0] + "\": \"" + decodeURIComponent(parts[1].replace(/\+/g," ")).replace(/\n/g,"\\n").replace(/\r/g,"\\r") + "\", ";
            }
        }

    };

    ret = (ret != "{") ? ret.substr(0,ret.length-2) + "}" : ret + "}";
    //console.log(ret, arrays);
    var ret2 = JSON.parse(ret);
    //proceso los arrays
    for (arr in arrays){
        ret2[arr] = arrays[arr];
    }
    return ret2;
}

