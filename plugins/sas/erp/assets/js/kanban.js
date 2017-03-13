+function ($) { "use strict";
    var replacementSplitter = /^(html|class|attr|data|value)\s*(.*)$/;
    function resetFormValues(f) {
        $(f).find('input[type="text"], textarea').val('');
    }
    function getJTemplate(key) {
        return $('#hidden-template-' + key).html();
    }
    function bindJTemplate(tcontent, jobj) {
        var jcontent = $(tcontent);
        var vars = jcontent.find('[data-var]');
        if(jcontent.data('var')){
            vars.push(jcontent);
        }
        vars.each(function (k, ve) {
            var jve = $(ve);
            var vk = jve.data('var'),
                vr = jve.data('replacement');
            var match = (vr||"").match(replacementSplitter);
            if(match && match.length > 1 && typeof jobj[vk] != "undefined"){
                var rval = jobj[vk];
                switch (match[1]) {
                    case 'html':
                        jve.html(rval || match[2]);
                        break;
                    case 'data':
                        jve.data(match[2], rval);
                        break;
                    case 'class':
                        jve.find('.' + match[2]).html(rval || "");
                        break;
                    case 'value':
                        jve.val(rval);
                        break;
                    case 'attr':
                        jve.attr(match[2], rval);
                        break;
                }
            }
        });
        return jcontent;
    }
    function parseJTemplate(tcontent) {
        var jcontent = $(tcontent);
        var jobj = {};
        var vars = jcontent.find('[data-var]');
        if(jcontent.data('var')){
            vars.push(jcontent);
        }
        vars.each(function (k, ve) {
            var jve = $(ve);
            var vk = jve.data('var'),
                vr = jve.data('replacement');
            var match = (vr||"").match(replacementSplitter);
            if(match && match.length > 1){
                var v = null;
                switch (match[1]) {
                    case 'html':
                        v = jve.html();
                        break;
                    case 'data':
                        v = jve.data(match[2]);
                        break;
                    case 'class':
                        v = jve.find('.' + match[2]).html();
                        break;
                    case 'value':
                        v = jve.val();
                        break;
                    case 'attr':
                        if(match[2] == "title" && jve.data('original-title')){
                            match[2] = "data-original-title";
                        }
                        v = jve.attr(match[2]);
                        break;
                }
                jobj[vk] = v;
            }
        });
        return jobj;
    }

    var ConfirmModal = {
        modal: null,
        item: {},
        modal_selector: '#confirmModal',
        _construct: function () {
            var that = this;
            var modal = $(this.modal_selector);
            modal.on('show.bs.modal', function (event) {
                bindJTemplate($(this), that.item);
            });
            return modal;
        },
        init: function (modal, message) {
            this.item = parseJTemplate(modal);
            if (this.item.name) {
                // column
                this.item.title = this.item.name;
            }

            if (message) {
                this.item.message = message;
            }

            if (!this.modal) {
                this.modal = this._construct();
                this.modal.on('click', '.js-confirm-modal-agree-btn', $.proxy(this._agreeBtn, this));
            }

            this.modal.modal('show');
        },
        _agreeBtn: function () {
            this.agree();
        },
        agree: function () {
            // todo implement agree handler
            alert('agree');
        },
        close: function () {
            this.modal.modal('hide');
        }
    };
    var ItemModal = $.extend({}, ConfirmModal, {
        modal_selector: '#itemModal',
        init: function (modal) {
            this.item = parseJTemplate(modal);

            if (!this.modal) {
                this.modal = this._construct();
                this.modal.on("click", ".js-item-modal-edit-btn", $.proxy(this.showEditForm, this));
                this.modal.on("click", ".js-item-modal-save-btn", $.proxy(this._submitEditForm, this));
            }

            this.hideEditForm();
            this.modal.modal('show');
        },
        showEditForm: function (ev) {
            this.modal.find(".view-group").addClass("hide");
            this.modal.find(".form-group").removeClass("hide");
        },
        hideEditForm: function () {
            this.modal.find(".view-group").removeClass("hide");
            this.modal.find(".form-group").addClass("hide");
        },
        _submitEditForm: function () {
            var data = {};
            this.modal.find('input, textarea').each(function (k, el) {
                var jel = $(el);
                var val = jel.val();
                if(el.tagName == "textarea" && !val){
                    val = jel.html();
                }
                data[jel.attr('name')] = val;
            });
            this.submitEditForm(data);
        },
        submitEditForm: function (data) {
            // todo implement submit handler
        }
    });

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
                if(!data.id){
                    data.id = this.item_id;
                }
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
                    complete: function (d, s) {
                        if(callback){
                            callback(d,s);
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
                data.callback = $.proxy(function (cdata, cstatus) {
                    if(cstatus == "success" && cdata.responseJSON){
                        this.addItem(cdata.responseJSON.column_id, cdata.responseJSON);
                        this._toggleItemButton(cdata.responseJSON.column_id, true, true);
                    } else {
                        // show error
                    }
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
                data.callback = $.proxy(function (cdata, cstatus) {
                    if (cstatus == "success" && cdata.responseJSON) {
                        this.addColumn(cdata.responseJSON);
                        $.proxy(this.$view.hideAddListForm, this)(ev);
                    }
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
                data.callback = $.proxy(function (cdata, cstatus) {
                    if (cstatus == "success" && cdata.responseJSON){
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
                    }
                }, this);

                this.$model.saveList(data);
            },
            removeItem: function (ev) {
                ev.preventDefault();

                var data = {};
                this.$model.item_id = this.findItemId(ev.target);
                data.del = 1;
                data.callback = $.proxy(function (cdata, cstatus) {
                    if (cstatus == "success") {
                        // remove item
                        this.removeItem(this.$model.item_id);
                    }
                }, this);
                this.$model.saveItem(data);
            },
            showConfirmItemDelete: function (ev) {
                ev.preventDefault();

                var target = this.findItem(ev.target);
                ConfirmModal.init(target);
                ConfirmModal.agree = $.proxy(function () {
                    var data = {};
                    this.$model.item_id = this.findItemId(ev.target);
                    data.del = 1;
                    data.callback = $.proxy(function (cdata, cstatus) {
                        if (cstatus == "success") {
                            // remove column
                            this.removeItem(this.$model.item_id);
                            ConfirmModal.close();
                        }
                    }, this);
                    this.$model.saveItem(data);
                }, this);
            },
            showConfirmArchiveItems: function (ev) {
                ev.preventDefault();

                var target = this.findColumn(ev.target);
                ConfirmModal.init(target, "Are you sure to archive all items");
                ConfirmModal.agree = $.proxy(function () {
                    var data = {};
                    this.$model.list_id = this.findColumnId(ev.target);
                    data.archive = 1;
                    data.callback = $.proxy(function (cdata, cstatus) {
                        if (cstatus == "success") {
                            // remove column
                            cdata.responseJSON.items = [];
                            this.addColumn(cdata.responseJSON);
                            ConfirmModal.close();
                        }
                    }, this);
                    this.$model.saveList(data);
                }, this);
            },
            showConfirmListDelete: function (ev) {
                ev.preventDefault();

                var target = this.findColumn(ev.target);
                ConfirmModal.init(target);
                ConfirmModal.agree = $.proxy(function () {
                    var data = {};
                    this.$model.list_id = this.findColumnId(ev.target);
                    data.del = 1;
                    data.callback = $.proxy(function (cdata, cstatus) {
                        if (cstatus == "success") {
                            // remove column
                            this.removeColumn(this.$model.list_id);
                            ConfirmModal.close();
                        }
                    }, this);
                    this.$model.saveList(data);
                }, this);
            },
            showItemModal: function (ev) {
                ev.preventDefault();

                var target = this.findItem(ev.target);
                ItemModal.init(target);

                ItemModal.submitEditForm = $.proxy(function (data) {
                    data.callback = $.proxy(function (cdata, cstatus) {
                        if(cstatus == "success" && cdata.responseJSON){
                            this.addItem(cdata.responseJSON.column_id, cdata.responseJSON);
                            ItemModal.close();
                        }
                    }, this);
                    this.$model.saveItem(data);
                }, this);
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
        'click .js-delete-item-btn': 'showConfirmItemDelete',
        'click .js-show-confirm-list-delete': 'showConfirmListDelete',
        'click .js-show-modal-item-btn': 'showItemModal',
        'click .js-show-confirm-archive-items': 'showConfirmArchiveItems',
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
            var template = getJTemplate("column");
            column.linked_status = $('#selectStatus option[value="'+column.linked_status+'"]').html();
            var jColumn = bindJTemplate(template, column);
            var col = column.id ? this.findColumn(column.id) : null;
            if(col && col.length){
                if(column.items && column.items.length == 0){
                    col.replaceWith(jColumn);
                } else {
                    col.find('.js-list-head').replaceWith(jColumn.find('.js-list-head'));
                }
            } else {
                boardList.find("#js-add-list-block").before(jColumn);
            }
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
            target.find(".js-show-edit-list-config").addClass('hide');
        } else {
            target.find(".js-edit-list").addClass('hide');
            target.find(".js-show-edit-list-form").removeClass('hide');
            target.find(".js-show-edit-list-config").removeClass('hide');
        }
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
            var template = getJTemplate("item");
            item.vote_class = "hide";
            item.desc_class = item.description ? "" : "hide";
            var jItem = bindJTemplate(template, item);

            var itm = item.id ? this.findItem(item.id) : null;
            if(itm && itm.length){
                itm.replaceWith(jItem);
            } else {
                column.find(".js-board-list-items").append(jItem);
            }

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