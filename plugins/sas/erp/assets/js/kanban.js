function addTask(data) {
    var wip = check_number($('.task_pool_header:first').find('[n=wip]').attr("wip"));
    if ((wip != 0) && ($('.task_pool:first .big_container').length >= wip)) {
        return false;
    }

    if(!data.id){ return false; }
    var id = data.id;
    var boxId = "box_itm" + id;

    var idx = $('.task_pool_header[n="'+data.status+'"]').index();
    console.log(idx, id);
    $('.task_pool:eq(' + idx + ')').append(' \
               <div class="big_container"> \
                  <div id="'+ boxId +'" class="box_itm rounded" n="' + id + '"> \
                      <div class="read-mode"> \
                          <div class="name box-data" n="name">' + data.title + '</div> \
                          <div class="name box-data" n="desc">' + data.description + '</div> \
                          <progress max="100" class="pbar box-data" n="progress" value="0"></progress> \
                          <div class="small"> \
                              <div class="itm_box_option"><input class="color colorete" type="color" data-text="hidden" data-colorlink="' + boxId +'" value="#F6E788"></div> \
                              <div class="option close itm_box_option"><button class="btn btn-danger btn-sm"><i class="icon-white icon-remove"></i></button></div> \
                              <div class="option edit itm_box_option"><button class="btn btn-info btn-sm"><i class="icon-white icon-pencil"></i></button></div> \
                          </div> \
                      </div> \
                      <div class="edit-mode" style="display: none;"> \
                        <div><span class="small">Name:</span><input name="name" class="input box-data"/></div>  \
                        <div><span class="small">Responsible:</span><input name="desc" class="input box-data"/></div>  \
                        <div><span class="small">Progress:</span><input name="progress" class="input box-data"/></div>  \
                        <div class="small"> \
                            <div class="option save"><button class="btn btn-success btn-mini"><i class="icon-white icon-ok">&nbsp;</i></button></div> \
                        </div> \
                      </div> \
                      <div class="clear"></div> \
                  </div> \
               <div> \
            ');

    $('#' + boxId).hover(function(){
        $(this).find('.itm_box_option').show();
    }, function(){
        $('.itm_box_option').hide();
    });

    //$('#' + boxId).find('progress').progressbar();
    $('#' + boxId).find('.edit-mode input').on('keypress', function (e) {
        var code;
        if (!e) var e = window.event;
        if (e.keyCode) code = e.keyCode;
        else if (e.which) code = e.which;
        if(code==13) { $(this).closest('.box_itm').find('.save').trigger('click'); }
    });
    $('#' + boxId).find('.colorete').on('change', function () {
        $(this).closest(".box_itm").css('background', $(this).val());
    });
    $('#' + boxId).find('.close').on('click', function() {
        $(this).closest(".box_itm").remove();
    });
    $('#' + boxId).find(".save,.edit").on('click', function(){
        var box = $(this).closest(".box_itm");
        if (box.find(".edit-mode").is(':visible')) {
            var src = box.find(".edit-mode");
            var tar = box.find(".read-mode");
            tar.find('[n=name]').html(src.find('input[name=name]').val());
            tar.find('[n=desc]').html(src.find('input[name=desc]').val());
            tar.find('[n=progress]').val(src.find('input[name=progress]').val());

            updateTask({
                tsk: box.attr('n'),
                title: src.find('input[name=name]').val(),
                description: src.find('input[name=desc]').val()
            }, null, box.attr('id'));
        } else {
            var src = box.find(".read-mode");
            var tar = box.find(".edit-mode");
            tar.find('[name=name]').val(src.find('[n=name]').html());
            tar.find('[name=desc]').val(src.find('[n=desc]').html());
            tar.find('[name=progress]').val(src.find('[n=progress]').val());
        }

        box.find(".edit-mode, .read-mode").toggle();
    });

    $('.itm_box_option').hide();
}

function addColumn(colData) {
    colData = colData || {};
    if(typeof colData.id == "undefined") colData.id = $(".task_pool").size();
    if(typeof colData.wip == "undefined") colData.wip = 0;
    if(typeof colData.name == "undefined") colData.name = "New Column";

    var headerId = "headBox" + colData.id;

    $(".task_pool_header:last").addClass("dotted_separator");
    $(".task_pool:last").addClass("dotted_separator");

    $("#task_pool_header_container").append(
        '<th id="' + headerId + '" class="task_pool_header" n="' + colData.id + '">' +
            '<div class="read-mode">' +
                '<div class="header_name edit_head"><i class="icon icon-th-large"><span class="column-data" n="name">' + colData.name + '</span></i></div>' +
                '<div wip="' + colData.wip + '" class="WIP column-data" n="wip">' + (colData.wip || "WIP: Unlimited") + '</div>' +
            '</div>' +
            '<div class="edit-mode" style="display: none;">' +
                '<div class="header_input">' +
                    'Name<br/><input name="name" class="input column-data" />' +
                '</div>' +
                '<div class="header_input">' +
                    'WIP<br/><input name="wip" class="input column-data" />' +
                '</div>' +
                '<div class="small">' +
                    '<div class="option save_header"><button class="btn btn-success btn-mini save_head"><i class="icon-white icon-ok">&nbsp;</i></button>' +
                '</div>' +
            '</div>' +
            '<div class="clear"></div>' +
        '</th>'
    );
    $("#task_pool_container").append('<td class="task_pool"><div /></td>');

    $('#' + headerId).find('.edit-mode input').on('keypress', function (e) {
        var code;
        if (!e) var e = window.event;
        if (e.keyCode) code = e.keyCode;
        else if (e.which) code = e.which;
        if(code==13) { $(this).closest('.task_pool_header').find('.save_head').trigger('click'); }
    });

    $('#' + headerId).find(".save_head,.edit_head").on('click', function(){
        var head = $(this).closest(".task_pool_header");
        if (head.find(".edit-mode").is(':visible')) {
            var src = head.find(".edit-mode");
            var tar = head.find(".read-mode");
            var wip = check_number(src.find('input[name=wip]').val());
            tar.find('[n=name]').html(src.find('input[name=name]').val());
            tar.find('[n=wip]').attr("wip", wip);
            tar.find('[n=wip]').html(wip || "WIP: Unlimited");

        } else {
            var src = head.find(".read-mode");
            var tar = head.find(".edit-mode");
            tar.find('[name=name]').val(src.find('[n=name]').html());
            tar.find('[name=wip]').val(src.find('[n=wip]').attr("wip"));
        }

        head.find(".edit-mode, .read-mode").toggle();
    });

    intialize_sortables();
}

function removeColumn(idx) {
    if($(".task_pool_header").size() > 1 || typeof idx == "undefined" || $(".task_pool_header").size() <= idx){

        idx = idx || $(".task_pool_header").size() - 1;

        if($(".task_pool_header").size() == idx + 1){
            $('.task_pool_header:eq('+(idx-1)+')').addClass("dotted_separator");
            $('.task_pool:eq('+(idx-1)+')').addClass("dotted_separator");
        }

        $('.task_pool_header:eq('+idx+')').remove();
        $('.task_pool:eq('+idx+')').remove();

        intialize_sortables();
    }
}

function intialize_sortables(){
    $( ".task_pool" ).sortable({
        connectWith: ".task_pool",
        delay: 25,
        revert: true,
        dropOnEmpty: true,
        forcePlaceHolderSize: true,
        helper: 'clone',
        forceHelperSize: true,
        receive: function(event, ui) {
            var itms = $(this).children(".big_container").length;
            var index = $(this).index();
            var colId = $(this).closest("table").find('th:eq('+index+')').attr('n');
            var tskId = $(ui.item).find('.box_itm').attr('n');

            var wip = check_number($(this).closest("table").find('th:eq('+index+') [n=wip]').attr("wip"));
            if ( (wip != 0 && itms > wip) || !colId || !tskId) {
                $(ui.sender).sortable('cancel');
            }

            updateTask({col: colId, tsk: tskId}, null, $(ui.item).find('.box_itm').attr('id'));
        }
    });
    $('.itm_box_option').hide();
}

function updateTask(submitData, cback, loadingId) {
    $('#'+loadingId).toggleLoading();
    $.oc.stripeLoadIndicator.show();

    $.request('onUpdateTask', {
        data: submitData,
        success: function(data) {
            typeof cback == "function" ? cback.call(data) : false;
        },
        complete: function () {
            $.oc.stripeLoadIndicator.hide();
            $('#'+loadingId).toggleLoading();
        }
    });
}

function find_next_box_itm_free(id){
    if($('#box_itm'+id).length)
    {
        id++;
        return find_next_box_itm_free(id);
    }
    else
    {
        return id;
    }
}
function check_number(number){
    return isNaN(number) || number < 0 ? 0 : (number > 100 ? 100 : parseInt(number));
}