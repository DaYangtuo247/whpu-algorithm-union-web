/**
 * Created by Sky on 2015/12/11.
 */
(function ($, window, document) {
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function (elt) {
            var len = this.length >>> 0;

            var from = Number(arguments[1]) || 0;
            from = (from < 0)
                ? Math.ceil(from)
                : Math.floor(from);
            if (from < 0)
                from += len;

            for (; from < len; from++) {
                if (from in this &&
                    this[from] === elt)
                    return from;
            }
            return -1;
        };
    }

    var PLUGIN_NAME = 'emoji',
        VERSION = '1.3.0',
        DEFAULTS = {
            showTab: true,
            animation: 'fade',
            icons: []
        };

    // 索引存在则不重置为0
    if (window.emoji_index === undefined) {
        window.emoji_index = 0;
    }

    function Plugin(element, options) {
        this.$content = $(element);
        this.options = options;
        this.index = emoji_index;
        switch (options.animation) {
            case 'none':
                this.showFunc = 'show';
                this.hideFunc = 'hide';
                this.toggleFunc = 'toggle';
                break;
            case 'slide':
                this.showFunc = 'slideDown';
                this.hideFunc = 'slideUp';
                this.toggleFunc = 'slideToggle';
                break;
            case 'fade':
                this.showFunc = 'fadeIn';
                this.hideFunc = 'fadeOut';
                this.toggleFunc = 'fadeToggle';
                break;
            default:
                this.showFunc = 'fadeIn';
                this.hideFunc = 'fadeOut';
                this.toggleFunc = 'fadeToggle';
                break;
        }
        this._init();
    }

    Plugin.prototype = {
        _init: function () {
            var that = this;
            var btn = this.options.button;
            var newBtn,
                contentTop,
                contentLeft,
                panelTop,
                panelLeft;
            var ix = that.index;
            if (!btn) {
                newBtn = '<input type="image" class="emoji_btn" id="emoji_btn_' + ix + '" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZBAMAAAA2x5hQAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAkUExURUxpcfTGAPTGAPTGAPTGAPTGAPTGAPTGAPTGAPTGAPTGAPTGAOfx6yUAAAALdFJOUwAzbVQOoYrzwdwkAoU+0gAAAM1JREFUGNN9kK0PQWEUxl8fM24iCYopwi0muuVuzGyKwATFZpJIU01RUG/RBMnHxfz+Oef9uNM84d1+23nO+zxHKVG2WWupRJkdcAwtpCK0lpbqWE01pB0QayonREMoIp7AawQrWSgGGb4pn6dSeSh68FAVXqHqy3wKrkJiDGDTg3dnp//w+WnwlwIOJauF+C7sXRVfdha4O4oIJfTbtdSxs2uqhs585A0ko8iLTMEcDE1n65A+29pYAlr72nz9dKu7GuNTcsL2fDQzB/wCPVJ69nZGb3gAAAAASUVORK5CYII="/>';
                contentTop = this.$content.offset().top + this.$content.outerHeight() + 10;
                contentLeft = this.$content.offset().left + 2;
                $(newBtn).appendTo($('body'));
                $('#emoji_btn_' + ix).css({ 'top': contentTop + 'px', 'left': contentLeft + 'px' });
                btn = '#emoji_btn_' + ix;
            }

            var showTab = this.options.showTab;
            var iconsGroup = this.options.icons;
            var groupLength = iconsGroup.length;
            if (groupLength === 0) {
                alert('Missing icons config!');
                return false;
            }

            var emoji_container = '<div class="emoji_container" id="emoji_container_' + ix + '">';
            var emoji_content = '<div class="emoji_content">';
            var emoji_tab = '<div class="emoji_tab" style="' + (groupLength === 1 && !showTab ? 'display:none;' : '') + '"><div class="emoji_tab_prev"></div><div class="emoji_tab_list"><ul>';
            var panel,
                name,
                path,
                maxNum,
                excludeNums,
                file,
                placeholder,
                alias,
                title,
                index,
                notation;
            for (var i = 0; i < groupLength; i++) {
                name = iconsGroup[i].name || 'group' + (i + 1);
                path = iconsGroup[i].path;
                maxNum = iconsGroup[i].maxNum;
                excludeNums = iconsGroup[i].excludeNums;
                file = iconsGroup[i].file || '.jpg';
                placeholder = iconsGroup[i].placeholder || '#em' + (i + 1) + '_{alias}#';
                alias = iconsGroup[i].alias;
                title = iconsGroup[i].title;
                index = 0;
                if (!path || !maxNum) {
                    alert('The ' + i + ' index of icon groups has error config!');
                    continue;
                }
                panel = '<div id="emoji' + i + '" class="emoji_icons" style="' + (i === 0 ? '' : 'display:none;') + '"><ul>';
                for (var j = 1; j <= maxNum; j++) {
                    if (excludeNums && excludeNums.indexOf(j) >= 0) {
                        continue;
                    }
                    if (alias) {
                        if (typeof alias !== 'object') {
                            alert('Error config about alias!');
                            break;
                        }
                        notation = placeholder.replace(new RegExp('{alias}', 'gi'), alias[j].toString());
                    } else {
                        notation = placeholder.replace(new RegExp('{alias}', 'gi'), j.toString());
                    }
                    panel += '<li><a data-emoji_code="' + notation + '" data-index="' + index + '" title="' + (title && title[j] ? title[j] : '') + '"><img src="' + path + j + file + '"/></a></li>';
                    index++;
                }
                panel += '</ul></div>';
                emoji_content += panel;
                emoji_tab += '<li data-emoji_tab="emoji' + i + '" class="' + (i === 0 ? 'selected' : '') + '" title="' + name + '">' + name + '</li>';
            }
            emoji_content += '</div>';
            emoji_tab += '</ul></div><div class="emoji_tab_next"></div></div>';
            var emoji_preview = '<div class="emoji_preview"><img/></div>';
            emoji_container += emoji_content;
            emoji_container += emoji_tab;
            emoji_container += emoji_preview;

            $(emoji_container).appendTo($('body'));

            // calc panel width
            var panelWidth = '544px';
            var winWidth = $(window).width();
            if (winWidth < 544) {
                switch (this.options.position) {
                    case 'topLeft':
                    case 'bottomLeft':
                        panelWidth = (winWidth - $(btn).offset().right * 2) + 'px';
                        break;
                    default:
                        panelWidth = (winWidth - $(btn).offset().left * 2) + 'px';
                }
            }
            $('#emoji_container_' + ix).css('width', panelWidth);
            $('#emoji_container_' + ix + ' .emoji_tab_list').css('width', (parseInt(panelWidth) - 44) + 'px');

            // calc panel position
            switch (this.options.position) {
                case 'topLeft':
                    panelTop = $(btn).offset().top - $('#emoji_container_' + ix).outerHeight() - 5;
                    panelLeft = $(btn).offset().left - $('#emoji_container_' + ix).outerWidth() + $(btn).outerHeight();
                    break;
                case 'topRight':
                    panelTop = $(btn).offset().top - $('#emoji_container_' + ix).outerHeight() - 5;
                    panelLeft = $(btn).offset().left;
                    break;
                case 'bottomLeft':
                    panelTop = $(btn).offset().top + $(btn).outerHeight() + 5;
                    panelLeft = $(btn).offset().left - $('#emoji_container_' + ix).outerWidth() + $(btn).outerHeight();
                    break;
                default:
                    panelTop = $(btn).offset().top + $(btn).outerHeight() + 5;
                    panelLeft = $(btn).offset().left;
            }
            $('#emoji_container_' + ix).css({ 'top': panelTop + 'px', 'left': panelLeft + 'px' });

            $('#emoji_container_' + ix + ' .emoji_content').mCustomScrollbar({
                theme: 'minimal-dark',
                scrollbarPosition: 'inside',
                mouseWheel: {
                    scrollAmount: 275
                }
            });

            var pageCount = groupLength % 8 === 0 ? parseInt(groupLength / 8) : parseInt(groupLength / 8) + 1;
            var pageIndex = 1;
            $(document).on({
                'click': function (e) {
                    var target = e.target;
                    var field = that.$content[0];
                    var code,
                        tab,
                        imgSrc,
                        insertHtml;
                    if (target === $(btn)[0] || $(target).parents(btn)[0] === $(btn)[0]) {
                        var _panelTop, _panelLeft;
                        // 重新计算位置。防止收到图片懒加载之类的影响
                        switch (that.options.position) {
                            case 'topLeft':
                                _panelTop = $(btn).offset().top - $('#emoji_container_' + ix).outerHeight() - 5;
                                _panelLeft = $(btn).offset().left - $('#emoji_container_' + ix).outerWidth() + $(btn).outerHeight();
                                break;
                            case 'topRight':
                                _panelTop = $(btn).offset().top - $('#emoji_container_' + ix).outerHeight() - 5;
                                _panelLeft = $(btn).offset().left;
                                break;
                            case 'bottomLeft':
                                _panelTop = $(btn).offset().top + $(btn).outerHeight() + 5;
                                _panelLeft = $(btn).offset().left - $('#emoji_container_' + ix).outerWidth() + $(btn).outerHeight();
                                break;
                            default:
                                _panelTop = $(btn).offset().top + $(btn).outerHeight() + 5;
                                _panelLeft = $(btn).offset().left;
                        }
                        if (_panelTop !== panelTop || _panelLeft !== panelLeft) {
                            panelTop = _panelTop;
                            panelLeft = _panelLeft;
                            $('#emoji_container_' + ix).css({ 'top': panelTop + 'px', 'left': panelLeft + 'px' });
                        }
                        $('#emoji_container_' + ix)[that.toggleFunc]();
                        that.$content.focus();
                    } else if ($(target).parents('#emoji_container_' + ix).length > 0) {
                        code = $(target).data('emoji_code') || $(target).parent().data('emoji_code');
                        tab = $(target).data('emoji_tab');
                        if (code) {
                            if (field.nodeName === 'DIV') {
                                imgSrc = $('#emoji_container_' + ix + ' a[data-emoji_code="' + code + '"] img').attr('src');
                                insertHtml = '<img class="emoji_icon" src="' + imgSrc + '"/>';
                                that._insertAtCursor(field, insertHtml, false);
                            } else {
                                that._insertAtCursor(field, code);
                            }
                            that.hide();
                        }
                        else if (tab) {
                            if (!$(target).hasClass('selected')) {
                                $('#emoji_container_' + ix + ' .emoji_icons').hide();
                                $('#emoji_container_' + ix + ' #' + tab).show();
                                $(target).addClass('selected').siblings().removeClass('selected');
                            }
                        } else if ($(target).hasClass('emoji_tab_prev')) {
                            if (pageIndex > 1) {
                                $('#emoji_container_' + ix + ' .emoji_tab_list ul').css('margin-left', ('-503' * (pageIndex - 2)) + 'px');
                                pageIndex--;
                            }

                        } else if ($(target).hasClass('emoji_tab_next')) {
                            if (pageIndex < pageCount) {
                                $('#emoji_container_' + ix + ' .emoji_tab_list ul').css('margin-left', ('-503' * pageIndex) + 'px');
                                pageIndex++;
                            }
                        }
                        that.$content.focus();
                    } else if ($('#emoji_container_' + ix + ':visible').length > 0) {
                        that.hide();
                        that.$content.focus();
                    }
                }
            });

            $('#emoji_container_' + ix + ' .emoji_icons a').mouseenter(function () {
                var index = $(this).data('index');
                if (parseInt(index / 5) % 2 === 0) {
                    $('#emoji_container_' + ix + ' .emoji_preview').css({ 'left': 'auto', 'right': 0 });
                } else {
                    $('#emoji_container_' + ix + ' .emoji_preview').css({ 'left': 0, 'right': 'auto' });
                }
                var src = $(this).find('img').attr('src');
                $('#emoji_container_' + ix + ' .emoji_preview img').attr('src', src).parent().show();
            });

            $('#emoji_container_' + ix + ' .emoji_icons a').mouseleave(function () {
                $('#emoji_container_' + ix + ' .emoji_preview img').removeAttr('src').parent().hide();
            });
        },

        _insertAtCursor: function (field, value, selectPastedContent) {
            var sel, range;
            if (field.nodeName === 'DIV') {
                field.focus();
                if (window.getSelection) {
                    sel = window.getSelection();
                    if (sel.getRangeAt && sel.rangeCount) {
                        range = sel.getRangeAt(0);
                        range.deleteContents();
                        var el = document.createElement('div');
                        el.innerHTML = value;
                        var frag = document.createDocumentFragment(), node, lastNode;
                        while ((node = el.firstChild)) {
                            lastNode = frag.appendChild(node);
                        }
                        var firstNode = frag.firstChild;
                        range.insertNode(frag);

                        if (lastNode) {
                            range = range.cloneRange();
                            range.setStartAfter(lastNode);
                            if (selectPastedContent) {
                                range.setStartBefore(firstNode);
                            } else {
                                range.collapse(true);
                            }
                            sel.removeAllRanges();
                            sel.addRange(range);
                        }
                    }
                } else if ((sel = document.selection) && sel.type !== 'Control') {
                    var originalRange = sel.createRange();
                    originalRange.collapse(true);
                    sel.createRange().pasteHTML(value);
                    if (selectPastedContent) {
                        range = sel.createRange();
                        range.setEndPoint('StartToStart', originalRange);
                        range.select();
                    }
                }
            } else {
                if (document.selection) {
                    field.focus();
                    sel = document.selection.createRange();
                    sel.text = value;
                    sel.select();
                }
                else if (field.selectionStart || field.selectionStart === 0) {
                    var startPos = field.selectionStart;
                    var endPos = field.selectionEnd;
                    var restoreTop = field.scrollTop;
                    field.value = field.value.substring(0, startPos) + value + field.value.substring(endPos, field.value.length);
                    if (restoreTop > 0) {
                        field.scrollTop = restoreTop;
                    }
                    field.focus();
                    field.selectionStart = startPos + value.length;
                    field.selectionEnd = startPos + value.length;
                } else {
                    field.value += value;
                    field.focus();
                }
            }

        },

        show: function () {
            $('#emoji_container_' + this.index)[this.showFunc]();
        },

        hide: function () {
            $('#emoji_container_' + this.index)[this.hideFunc]();
        },

        toggle: function () {
            $('#emoji_container_' + this.index)[this.toggleFunc]();
        }
    };

    function fn(option) {
        emoji_index++;
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('plugin_' + PLUGIN_NAME + emoji_index);
            var options = $.extend({}, DEFAULTS, $this.data(), typeof option === 'object' && option);

            if (!data) $this.data('plugin_' + PLUGIN_NAME + emoji_index, (data = new Plugin(this, options)));
            if (typeof option === 'string') data[option]();
        });
    }

    $.fn[PLUGIN_NAME] = fn;
    $.fn[PLUGIN_NAME].Constructor = Plugin;

}(jQuery, window, document));

(function ($, window, document) {

    var PLUGIN_NAME = 'emojiParse',
        VERSION = '1.3.0',
        DEFAULTS = {
            icons: []
        };

    function Plugin(element, options) {
        this.$content = $(element);
        this.options = options;
        this._init();
    }

    Plugin.prototype = {
        _init: function () {
            var that = this;
            var iconsGroup = this.options.icons;
            var groupLength = iconsGroup.length;
            var path,
                file,
                placeholder,
                alias,
                pattern,
                regexp,
                revertAlias = {};
            if (groupLength > 0) {
                for (var i = 0; i < groupLength; i++) {
                    path = iconsGroup[i].path;
                    file = iconsGroup[i].file || '.jpg';
                    placeholder = iconsGroup[i].placeholder;
                    alias = iconsGroup[i].alias;
                    if (!path) {
                        alert('Path not config!');
                        continue;
                    }
                    if (alias) {
                        for (var attr in alias) {
                            if (alias.hasOwnProperty(attr)) {
                                revertAlias[alias[attr]] = attr;
                            }
                        }
                        pattern = placeholder.replace(new RegExp('{alias}', 'gi'), '([\\s\\S]+?)');
                        regexp = new RegExp(pattern, 'gm');
                        that.$content.html(that.$content.html().replace(regexp, function ($0, $1) {
                            var n = revertAlias[$1];
                            if (n) {
                                return '<img class="emoji_icon" src="' + path + n + file + '"/>';
                            } else {
                                return $0;
                            }
                        }));
                    } else {
                        pattern = placeholder.replace(new RegExp('{alias}', 'gi'), '(\\d+?)');
                        that.$content.html(that.$content.html().replace(new RegExp(pattern, 'gm'), '<img class="emoji_icon" src="' + path + '$1' + file + '"/>'));
                    }
                }
            }
        }
    };

    function fn(option) {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('plugin_' + PLUGIN_NAME);
            var options = $.extend({}, DEFAULTS, $this.data(), typeof option === 'object' && option);

            if (!data) $this.data('plugin_' + PLUGIN_NAME, (data = new Plugin(this, options)));
            if (typeof option === 'string') data[option]();
        });
    }

    $.fn[PLUGIN_NAME] = fn;
    $.fn[PLUGIN_NAME].Constructor = Plugin;

}(jQuery, window, document));
