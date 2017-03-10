+function ($) { "use strict";
    function resetFormValues(f) {
        $(f).find('input[type="text"], textarea').val('');
    }

    var KanbanB = function (board, modelCfg, viewCfg) {
        this.$board = $(board);
        this.cid = Math.random().toString(36).substring(7);
        this.$model = $.extend(true, {
            list_id: '',
            item_id: '',
            project_id: '',
            moveList: function (previous_list_id, next_list_id) {
                var d = {};
                if(previous_list_id != null){
                    d.previous_list_id = previous_list_id;
                }
                if(next_list_id != null){
                    d.next_list_id = next_list_id;
                }
                return this.saveList(d, 'onUpdateOrder');
            },
            saveList: function (data, task) {
                data.pid = this.project_id;
                data.id = this.list_id;
                data.mode = "list";
                return this.saveRequest(task || 'onUpdateColumn', data);
            },
            moveItem: function (previous_item_id, next_item_id) {
                var d = {};
                if(previous_item_id != null){
                    d.previous_item_id = previous_item_id;
                }
                if(next_item_id != null){
                    d.next_item_id = next_item_id;
                }
                return this.saveItem(d, 'onUpdateOrder');
            },
            saveItem: function (data, task) {
                if(!this.project_id) return false;
                data.id = this.item_id;
                if(this.list_id) {
                    data.list_id = this.list_id;
                }
                data.pid = this.project_id;
                data.mode = "item";
                return this.saveRequest(task || 'onUpdateItem', data);
            },
            saveRequest: function (task, data) {
                //$('#'+loadingId).toggleLoading();
                $.oc.stripeLoadIndicator.show();

                var callback = null;
                if(data.callback && typeof data.callback == "function") {
                    callback = data.callback;
                    data.callback = null;
                }
                $.request(task, {
                    data: data,
                    complete: function (d) {
                        console.log(arguments);
                        if(callback){
                            callback(d);
                        }
                        $.oc.stripeLoadIndicator.hide();
                        //$('#'+loadingId).toggleLoading();
                    }
                });

                return true;
            }
        }, modelCfg);

        this.$view = $.extend(true, {
            listSort: function (e, ev, ui) {
                console.log('list sort');
                console.log(arguments);

                var target = $(ui.item);
                this.$model.list_id = target.data('list_id');
                var previous_list_id = target.prev('.js-board-list').data('list_id');
                var next_list_id = target.next('.js-board-list').data('list_id');
                if (typeof previous_list_id == 'undefined' && typeof next_list_id == 'undefined') {
                    previous_list_id = 1;
                    next_list_id = 1;
                }
                if (typeof previous_list_id != 'undefined' && typeof next_list_id != 'undefined') {
                    this.$model.moveList(previous_list_id, next_list_id);
                } else if (typeof previous_list_id != 'undefined') {
                    this.$model.moveList(previous_list_id, null);
                } else if (typeof next_list_id != 'undefined') {
                    this.$model.moveList(null, next_list_id);
                } else {
                    if (this.findColumns().length != 1) {
                        throw 'Unable to determine position';
                    }
                }
            },
            itemSort: function (e, ev, ui) {
                console.log('item sort');
                console.log(arguments);

                var target = $(ui.item);
                this.$model.list_id = this.findColumnId(target);
                this.$model.item_id = target.data('item_id');
                var previous_item_id = target.prev('.js-board-list-item').data('item_id');
                var next_item_id = target.next('.js-board-list-item').data('item_id');

                if (typeof previous_item_id != 'undefined' && typeof next_item_id != 'undefined') {
                    this.$model.moveItem(previous_item_id, next_item_id);
                } else if (typeof previous_item_id != 'undefined') {
                    this.$model.moveItem(previous_item_id, null);
                } else if (typeof next_item_id != 'undefined') {
                    this.$model.moveItem(null, next_item_id);
                } else {
                    this.$model.saveItem({});
                }
            },
            showAddItemForm: function (ev) {
                ev.preventDefault();
                this._toggleItemButton(this.findColumnId(ev.target), false);
            },
            hideAddItemForm: function (ev) {
                ev.preventDefault();
                this._toggleItemButton(this.findColumnId(ev.target), true);
            },
            submitItemAdd: function (ev) {
                console.log('submit item');
                console.log(arguments);
                ev.preventDefault();

                var formValues = $(ev.target).closest('form[name="itemAddForm"]').serializeArray();
                var data = {};
                for(var i in formValues){
                    var a = formValues[i];
                    data[a.name] = a.value;
                }

                this.$model.list_id = this.findColumnId(ev.target);
                var previous_item_id = this.findColumn(ev.target).find('.js-board-list-items .js-board-list-item:last').data('item_id');
                if(previous_item_id){
                    data.previous_item_id = previous_item_id;
                }
                data.callback = $.proxy(function (cdata) {
                    this.addItem(cdata.responseJSON.column_id, cdata.responseJSON);
                    this._toggleItemButton(cdata.responseJSON.column_id, true, true);
                }, this);

                this.$model.saveItem(data);
            },
            showAddListForm: function (ev) {
                ev.preventDefault();
                this._toggleListButton(false);
            },
            hideAddListForm: function (ev) {
                ev.preventDefault();
                this._toggleListButton(true);
            },
            submitListAdd: function (ev) {
                console.log('submit list');
                console.log(arguments);
                ev.preventDefault();

                var formValues = $(ev.target).closest('form[name="listAddForm"]').serializeArray();
                var data = {};
                for(var i in formValues){
                    var a = formValues[i];
                    data[a.name] = a.value;
                }

                this.$model.list_id = '';
                var previous_list_id = $('#js-add-list-block').prev('.js-board-list').data('list_id');
                if(previous_list_id){
                    data.previous_list_id = previous_list_id;
                }
                data.callback = $.proxy(function (cdata) {
                    this.addColumn(cdata.responseJSON);
                    $.proxy(this.$view.hideAddListForm, this)(ev);
                }, this);

                this.$model.saveList(data);
            },
            showEditListForm: function (ev) {
                ev.preventDefault();
                this._toggleListForm(this.findColumnId(ev.target), true);
            },
            hideEditListForm: function (ev) {
                ev.preventDefault();
                this._toggleListForm(this.findColumnId(ev.target), false);
            },
            submitListEdit: function (ev) {
                ev.preventDefault();

                var formValues = $(ev.target).closest('form[name="listEditForm"]').serializeArray();
                var data = {};
                for(var i in formValues){
                    var a = formValues[i];
                    data[a.name] = a.value;
                }

                this.$model.list_id = this.findColumnId(ev.target);
                data.callback = $.proxy(function (cdata) {
                    // bind new value
                    this.findColumn(cdata.responseJSON.id).find('[data-var="name"]').each(function () {
                        var je = $(this);
                        if(je.prop('tagName') == 'input') {
                            je.val(cdata.responseJSON.name);
                        } else {
                            je.html(cdata.responseJSON.name);
                        }
                    });
                    $.proxy(this.$view.hideEditListForm, this)(ev);
                }, this);

                this.$model.saveList(data);
            },
            removeItem: function (ev) {
                ev.preventDefault();

                var data = {};
                this.$model.item_id = this.findItemId(ev.target);
                data.del = 1;
                data.callback = $.proxy(function (cdata) {
                    // remove item
                    this.removeItem(this.$model.item_id);
                }, this);
                this.$model.saveItem(data);
            },
            showConfirmListDelete: function (ev) {
                var data = {};
                this.$model.list_id = this.findColumnId(ev.target);
                data.del = 1;
                data.callback = $.proxy(function (cdata) {
                    // remove column
                    this.removeColumn(this.$model.list_id);
                }, this);
                this.$model.saveList(data);
            }
        }, viewCfg);

        if(modelCfg.columns){
            this.parseData(modelCfg.columns);
        }

        this.updateBoard();
        this.delegateEvents(this.events);
    };

    KanbanB.prototype.parseData = function (columns) {
        if(!columns || !columns.length) return false;

        for(var i in columns) {
            var col = columns[i];
            this.addColumn(col);
            if(col.items && col.items.length){
                for(var j in col.items){
                    this.addItem(col.id, col.items[j]);
                }
            }
        }
    };

    KanbanB.prototype.delegateEvents = function(events) {
        if (!events) return this;
        this.undelegateEvents();
        var delegateEventSplitter = /^(\S+)\s*(.*)$/;
        for (var key in events) {
            var method = events[key];
            if (!$.isFunction(method)) method = this.$view[events[key]];
            if (!method) continue;

            var match = key.match(delegateEventSplitter);
            var eventName = match[1], selector = match[2];
            method = $.proxy(method, this);
            eventName += '.delegateEvents' + this.cid;
            if (selector === '') {
                this.$board.on(eventName, method);
            } else {
                this.$board.on(eventName, selector, method);
            }
        }
        return this;
    };

    KanbanB.prototype.undelegateEvents = function() {
        this.$board.off('.delegateEvents' + this.cid);
        return this;
    };

    KanbanB.prototype.events = {
        'click .js-hide-add-item-form': 'hideAddItemForm',
        'click .js-show-add-item-form': 'showAddItemForm',
        'click .js-itemAddForm-btn': 'submitItemAdd',
        'click .js-show-add-list-form': 'showAddListForm',
        'click .js-hide-add-list-form': 'hideAddListForm',
        'click .js-listAddForm-btn': 'submitListAdd',
        'click .js-show-edit-list-form': 'showEditListForm',
        'click .js-hide-edit-list-form': 'hideEditListForm',
        'click .js-listEditForm-btn': 'submitListEdit',
        'click .js-delete-item-btn': 'removeItem',
        'click .js-show-confirm-list-delete': 'showConfirmListDelete',
        'itemSort': 'itemSort',
        'listSort': 'listSort'
    };

    KanbanB.prototype.updateBoard = function () {
        // add draggable on columns
        this.sortableColumns();
        // add draggable on items
        this.sortableItems();
    };

    KanbanB.prototype.sortableColumns = function () {
        var list = this.findBoardList();
        if (list && list.length) {
            var that = this;
            list.sortable({
                containment: 'window',
                axis: 'x',
                items: 'div.js-board-list',
                placeholder: 'col-lg-3 col-md-3 col-sm-4 col-xs-12 board-list-placeholder list ',
                forcePlaceholderSize: true,
                appendTo: document.body,
                cursor: 'grab',
                scrollSensitivity: 100,
                scrollSpeed: 50,
                handle: '.js-list-head',
                tolerance: 'pointer',
                update: function(ev, ui) {
                    that.$board.trigger('listSort', [ev, ui]);
                }
            });
        }
    };

    KanbanB.prototype.sortableItems = function () {
        var columns = this.findColumns();
        if (columns && columns.length) {
            var that = this;
            columns.find(".js-board-list-items").sortable({
                items: 'div.js-board-list-item',
                connectWith: '.js-board-list-items',
                placeholder: 'item-list-placeholder',
                appendTo: document.body,
                dropOnEmpty: true,
                cursor: 'grab',
                helper: 'clone',
                tolerance: 'pointer',
                scrollSensitivity: 100,
                scrollSpeed: 50,
                update: function(ev, ui) {
                    if (this === ui.item.parent()[0]) {
                        that.$board.trigger('itemSort', [ev, ui]);
                    }
                }
            });
        }
    };

    KanbanB.prototype.addColumn = function (column, trigger) {
        // add last column
        var boardList = this.findBoardList();
        if (boardList && boardList.length) {
            var template = $( "#hidden-template-column" ).html();
            var linked_status = $('#selectStatus option[value="'+column.linked_status+'"]').html();
            var jColumn = $(template
                .replace(/\${2}LIST_ID\${2}/g, column.id)
                .replace(/\${2}LINKED_STATUS\${2}/g, linked_status)
                .replace(/\${2}NAME\${2}/g, column.name));

            boardList.find("#js-add-list-block").before(jColumn);
            if (trigger) {
                this.$board.trigger('columnAdded', column);
            }
        }
    };

    KanbanB.prototype.removeColumn = function (columnId, trigger) {
        var column = this.findColumn(columnId);
        if (column && column.length) {
            column.remove();
            if (trigger) {
                this.$board.trigger('columnDeleted', columnId);
            }
        }

        // update board
        this.updateBoard();
    };

    KanbanB.prototype._toggleItemButton = function (columnId, toggle, forceAll) {
        if(typeof toggle == "undefined"){
            toggle = true;
        }

        var target = forceAll ? this.$board : this.findColumn(columnId);

        if (toggle) {
            this._toggleListButton(true);
            target.find(".js-show-add-item-form").removeClass('hide');
            resetFormValues(target.find(".js-show-modal-item-view form"));
            target.find(".js-itemAddForm").parent().addClass('hide');
        } else {
            this._toggleItemButton(null, true, true);
            target.find(".js-show-add-item-form").addClass('hide');
            target.find(".js-itemAddForm").parent().removeClass('hide');
        }
    };

    KanbanB.prototype._toggleListButton = function (toggle) {
        if(typeof toggle == "undefined"){
            toggle = true;
        }

        var target = this.$board;

        if (toggle) {
            target.find(".js-show-add-list-form").removeClass('hide');
            resetFormValues(target.find(".js-add-list"));
            target.find(".js-add-list").addClass('hide');
        } else {
            this._toggleItemButton(null, true, true);
            target.find(".js-show-add-list-form").addClass('hide');
            target.find(".js-add-list").removeClass('hide');
        }
    };

    KanbanB.prototype._toggleListForm = function (columnId, toggle, forceAll) {
        if(typeof toggle == "undefined"){
            toggle = true;
        }

        var target = forceAll ? this.$board : this.findColumn(columnId);

        if (toggle) {
            this._toggleListForm(null, false, true);
            target.find(".js-edit-list").removeClass('hide');
            target.find(".js-show-edit-list-form").addClass('hide');
        } else {
            target.find(".js-edit-list").addClass('hide');
            target.find(".js-show-edit-list-form").removeClass('hide');
        }
    };

    KanbanB.prototype._toggleItemForm = function (itemId, toggle, forceAll) {
        //
    };

    KanbanB.prototype.removeItem = function (itemId, trigger) {
        var item = this.findItem(itemId);
        if (item && item.length) {
            item.remove();
            if (trigger) {
                this.$board.trigger('itemDeleted', itemId);
            }
        }

        // update board
        this.updateBoard();
    };

    KanbanB.prototype.addItem = function (columnId, item, trigger) {
        // add last item
        var column = this.findColumn(columnId);
        if (column && column.length) {
            var template = $( "#hidden-template-item" ).html();
            var jItem = $(template
                .replace(/\${2}ITEM_ID\${2}/g, item.id)
                .replace(/\${2}VOTE_CLASS\${2}/g, 'hide')
                .replace(/\${2}DESC_CLASS\${2}/g, item.description ? "" : 'hide')
                .replace(/\${2}DESCRIPTION\${2}/g, item.description)
                .replace(/\${2}TITLE\${2}/g, item.title));

            column.find(".js-board-list-items").append(jItem);
            if (trigger) {
                this.$board.trigger('itemAdded', item);
            }
        }

        // update board
        this.updateBoard();
    };

    KanbanB.prototype.findItemId = function (obj) {
        return $(obj).closest('[data-item_id]').data('item_id');
    };
    KanbanB.prototype.findItem = function (itemId) {
        if(typeof itemId == "object" && itemId !== null) {
            return $(itemId).closest('[data-item_id]');
            //itemId = this.findItemId(itemId);
        }
        return this.findItems().filter(function () {
            return $(this).data('item_id') == itemId;
        });
    };
    KanbanB.prototype.findColumnId = function (obj) {
        return $(obj).closest('[data-list_id]').data('list_id');
    };
    KanbanB.prototype.findColumn = function (columnId) {
        if(typeof columnId == "object" && columnId !== null) {
            return $(columnId).closest('[data-list_id]');
            //columnId = this.findColumnId(columnId);
        }
        return this.findColumns().filter(function () {
            return $(this).data('list_id') == columnId;
        });
    };
    KanbanB.prototype.findItems = function () {
        return this.findBoardList().find('[data-item_id]');
    };
    KanbanB.prototype.findColumns = function () {
        return this.findBoardList().find('.js-board-list');
    };
    KanbanB.prototype.findBoardList = function () {
        return this.$board.find('.board-list-view');
    };

    $.fn.extend({
        // Take a wrapper and show thats its laoding something
        // this places a div over the top of the content to prevent interaction.
        kanbanB: function (modelCfg, viewCfg) {
            return this.each( function() {
                new KanbanB($(this), modelCfg, viewCfg);
            } );
        }
    });

}(window.jQuery);

/*
function addTask(data) { console.log(arguments);
    if(!data || !data.id){ return false; }

    var id = data.id,
        boxId = "box_itm" + id,
        head = $('.task_pool_header[n="'+data.column_id+'"]'),
        idx = head.index();

    var wip = check_number(head.find('[n=wip]').attr("wip"));
    if ((wip != 0) && ($('.task_pool:eq(' + idx + ') .big_container').length >= wip)) {
        return false;
    }

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
        var box = $(this).closest(".box_itm");
        updateTask({
            tsk: box.attr("n"),
            del: 1
        }, function () {
            box.remove();
        }, box.attr("id"));
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

    $('#'+loadingId).toggleLoading();
    $.oc.stripeLoadIndicator.show();

    $.request('onAddColumn', {
        data: colData,
        success: function(data) {
            typeof cback == "function" ? cback.call(this, data) : false;
        },
        complete: function () {
            $.oc.stripeLoadIndicator.hide();
            $('#'+loadingId).toggleLoading();
        }
    });

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
            typeof cback == "function" ? cback.call(this, data) : false;
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
*/