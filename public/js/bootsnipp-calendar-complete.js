/*!
 * Bootsnipp Calendar - Complete Implementation
 * Based on https://bootsnipp.com/snippets/PEZZ7
 */

(function($) {
    'use strict';

    // Simple template engine
    $.extend({
        substitute: function(str, object) {
            return str.replace(/\{\{(.+?)\}\}/g, function(match, property) {
                return object[property] || '';
            });
        }
    });

    // Date extensions
    $.extend(Date.prototype, {
        toDateCssClass: function() {
            return '_' + this.getFullYear() + '_' + (this.getMonth() + 1) + '_' + this.getDate();
        },
        toDateInt: function() {
            return ((this.getFullYear() * 12) + this.getMonth()) * 32 + this.getDate();
        }
    });

    // Default calendar template
    var defaultTemplate =
        '<div class="container theme-showcase">' +
        '  <div class="row">' +
        '    <div class="col-md-12">' +
        '      <div class="page-header">' +
        '        <div class="pull-right form-inline">' +
        '          <div class="btn-group">' +
        '            <button class="btn btn-primary js-cal-prev" type="button">&laquo;</button>' +
        '            <button class="btn btn-default" type="button" id="cal-month-box">{{currentMonth}}</button>' +
        '            <button class="btn btn-primary js-cal-next" type="button">&raquo;</button>' +
        '          </div>' +
        '        </div>' +
        '        <h3>{{title}}</h3>' +
        '      </div>' +
        '      <div class="calendar-table">' +
        '        <table class="table table-bordered">' +
        '          <thead>' +
        '            <tr>' +
        '              {{#days}}' +
        '              <th class="cal-header">{{.}}</th>' +
        '              {{/days}}' +
        '            </tr>' +
        '          </thead>' +
        '          <tbody>' +
        '            {{#weeks}}' +
        '            <tr>' +
        '              {{#days}}' +
        '              <td class="calendar-day {{classes}}" data-date="{{date}}">' +
        '                <div class="day-number">{{day}}</div>' +
        '                {{#events}}' +
        '                <div class="event" data-event-id="{{id}}">{{title}}</div>' +
        '                {{/events}}' +
        '              </td>' +
        '              {{/days}}' +
        '            </tr>' +
        '            {{/weeks}}' +
        '          </tbody>' +
        '        </table>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>';

    // Simple mustache-like template renderer
    function renderTemplate(template, data) {
        var result = template;

        // Handle simple variables
        result = result.replace(/\{\{([^#\/][^}]*)\}\}/g, function(match, key) {
            return data[key] || '';
        });

        // Handle arrays
        result = result.replace(/\{\{#(\w+)\}\}([\s\S]*?)\{\{\/\1\}\}/g, function(match, key, content) {
            if (data[key] && Array.isArray(data[key])) {
                return data[key].map(function(item) {
                    return renderTemplate(content, item);
                }).join('');
            }
            return '';
        });

        return result;
    }

    // Main calendar function
    function calendar($el, options) {
        var settings = $.extend({
            date: new Date(),
            data: [],
            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            mode: 'month',
            template: defaultTemplate,
            onDateClick: null,
            onEventClick: null
        }, options);

        // Navigation event handlers
        $el.on('click', '.js-cal-prev', function() {
            switch(settings.mode) {
                case 'month':
                    settings.date.setMonth(settings.date.getMonth() - 1);
                    break;
                case 'week':
                    settings.date.setDate(settings.date.getDate() - 7);
                    break;
                case 'day':
                    settings.date.setDate(settings.date.getDate() - 1);
                    break;
            }
            draw();
        });

        $el.on('click', '.js-cal-next', function() {
            switch(settings.mode) {
                case 'month':
                    settings.date.setMonth(settings.date.getMonth() + 1);
                    break;
                case 'week':
                    settings.date.setDate(settings.date.getDate() + 7);
                    break;
                case 'day':
                    settings.date.setDate(settings.date.getDate() + 1);
                    break;
            }
            draw();
        });

        // Date click handler
        $el.on('click', '.calendar-day', function() {
            var dateStr = $(this).data('date');
            if (settings.onDateClick && dateStr) {
                var date = new Date(dateStr);
                var dayEvents = getEventsForDate(date);
                settings.onDateClick(date, dayEvents);
            }
        });

        // Event click handler
        $el.on('click', '.event', function(e) {
            e.stopPropagation();
            if (settings.onEventClick) {
                var eventId = $(this).data('event-id');
                var event = settings.data.find(function(e) { return e.id == eventId; });
                if (event) {
                    settings.onEventClick(event);
                }
            }
        });

        function getEventsForDate(date) {
            return settings.data.filter(function(event) {
                var eventDate = new Date(event.start || event.date);
                return eventDate.toDateString() === date.toDateString();
            });
        }

        function draw() {
            if (settings.mode === 'month') {
                drawMonth();
            }

            // Add today's highlighting
            $('.calendar-day.' + (new Date()).toDateCssClass()).addClass('today');
        }

        function drawMonth() {
            var year = settings.date.getFullYear();
            var month = settings.date.getMonth();
            var firstDay = new Date(year, month, 1);
            var lastDay = new Date(year, month + 1, 0);
            var startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            var data = {
                title: 'Calendar',
                currentMonth: settings.months[month] + ' ' + year,
                days: settings.days,
                weeks: []
            };

            var currentDate = new Date(startDate);

            for (var week = 0; week < 6; week++) {
                var weekData = { days: [] };

                for (var day = 0; day < 7; day++) {
                    var isCurrentMonth = currentDate.getMonth() === month;
                    var dateStr = currentDate.toISOString().split('T')[0];
                    var dayEvents = getEventsForDate(currentDate);

                    var classes = [];
                    if (!isCurrentMonth) classes.push('other-month');
                    if (currentDate.toDateString() === new Date().toDateString()) classes.push('today');
                    classes.push(currentDate.toDateCssClass());

                    var dayData = {
                        day: currentDate.getDate(),
                        date: dateStr,
                        classes: classes.join(' '),
                        events: dayEvents.slice(0, 3) // Limit to 3 events per day
                    };

                    weekData.days.push(dayData);
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                data.weeks.push(weekData);

                // Break if we've moved past the current month
                if (currentDate.getMonth() !== month && currentDate.getDate() > 7) {
                    break;
                }
            }

            var html = renderTemplate(settings.template, data);
            $el.html(html);
        }

        // Public methods
        this.setData = function(data) {
            settings.data = data;
            draw();
        };

        this.refresh = function() {
            draw();
        };

        this.setDate = function(date) {
            settings.date = new Date(date);
            draw();
        };

        // Initial draw
        draw();

        return this;
    }

    // jQuery plugin
    $.fn.bootsnippCalendar = function(options) {
        return this.each(function() {
            var $this = $(this);
            var instance = calendar($this, options);
            $this.data('bootsnippCalendar', instance);
        });
    };

})(jQuery);