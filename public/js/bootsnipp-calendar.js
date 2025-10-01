/**
 * Bootsnipp Calendar Plugin
 * A lightweight calendar component based on the Bootsnipp PEZZ7 snippet
 */
(function($) {
    'use strict';

    // Date prototype extensions
    Date.prototype.toDateCssClass = function() {
        return '_' + this.getFullYear() + '_' + (this.getMonth() + 1) + '_' + this.getDate();
    };

    Date.prototype.toDateInt = function() {
        return ((this.getFullYear() * 12) + this.getMonth()) * 32 + this.getDate();
    };

    // Main calendar plugin
    $.fn.bootsnippCalendar = function(options) {
        var $el = this;

        // Default options
        var settings = $.extend({
            date: new Date(),
            mode: 'month',
            data: [],
            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            onDateClick: function(date, events) {},
            onEventClick: function(event) {}
        }, options);

        // Initialize calendar
        function init() {
            draw();
            bindEvents();
        }

        // Bind navigation events
        function bindEvents() {
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

            $el.on('click', 'td[data-date]', function() {
                var dateStr = $(this).data('date');
                var date = new Date(dateStr);
                var dayEvents = getEventsForDate(date);
                settings.onDateClick(date, dayEvents);
            });

            $el.on('click', '.calendar-event', function(e) {
                e.stopPropagation();
                var eventData = $(this).data('event');
                settings.onEventClick(eventData);
            });
        }

        // Get events for a specific date
        function getEventsForDate(date) {
            return settings.data.filter(function(event) {
                var eventDate = new Date(event.start || event.date);
                return eventDate.toDateString() === date.toDateString();
            });
        }

        // Main draw function
        function draw() {
            if (settings.mode === 'month') {
                drawMonth();
            }
        }

        // Draw month view
        function drawMonth() {
            var html = buildMonthHtml();
            $el.html(html);

            // Add today's class
            var today = new Date();
            var todayCell = $el.find('td[data-date="' + today.toISOString().split('T')[0] + '"]');
            todayCell.addClass('today');
        }

        // Build month HTML
        function buildMonthHtml() {
            var year = settings.date.getFullYear();
            var month = settings.date.getMonth();
            var firstDay = new Date(year, month, 1);
            var lastDay = new Date(year, month + 1, 0);
            var startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            var html = '<div class="bootsnipp-calendar">';

            // Header
            html += '<div class="calendar-header">';
            html += '<button class="calendar-nav js-cal-prev">&laquo;</button>';
            html += '<h3 class="calendar-title">' + settings.months[month] + ' ' + year + '</h3>';
            html += '<button class="calendar-nav js-cal-next">&raquo;</button>';
            html += '</div>';

            // Calendar table
            html += '<table>';

            // Day headers
            html += '<thead><tr>';
            for (var i = 0; i < 7; i++) {
                html += '<th>' + settings.days[i].substring(0, 3) + '</th>';
            }
            html += '</tr></thead>';

            // Calendar body
            html += '<tbody>';
            var currentDate = new Date(startDate);

            for (var week = 0; week < 6; week++) {
                html += '<tr>';

                for (var day = 0; day < 7; day++) {
                    var isCurrentMonth = currentDate.getMonth() === month;
                    var dateStr = currentDate.toISOString().split('T')[0];
                    var dayEvents = getEventsForDate(currentDate);

                    html += '<td data-date="' + dateStr + '"';
                    if (!isCurrentMonth) {
                        html += ' class="other-month"';
                    }
                    html += '>';

                    html += '<div class="day-number">' + currentDate.getDate() + '</div>';

                    // Add events
                    dayEvents.forEach(function(event, index) {
                        if (index < 2) { // Limit to 2 events per day
                            var eventClass = 'calendar-event';
                            if (event.type) {
                                eventClass += ' event-type-' + event.type;
                            }
                            html += '<div class="' + eventClass + '" data-event=\'' + JSON.stringify(event) + '\'>';
                            html += event.title || event.name || 'Event';
                            html += '</div>';
                        }
                    });

                    if (dayEvents.length > 2) {
                        html += '<div class="calendar-event more-events">+' + (dayEvents.length - 2) + ' more</div>';
                    }

                    html += '</td>';
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                html += '</tr>';

                // Break if we've gone past the current month and completed a week
                if (currentDate.getMonth() !== month && day === 6) {
                    break;
                }
            }

            html += '</tbody></table></div>';

            return html;
        }

        // Public methods
        this.refresh = function() {
            draw();
        };

        this.setDate = function(date) {
            settings.date = new Date(date);
            draw();
        };

        this.getDate = function() {
            return new Date(settings.date);
        };

        this.setData = function(data) {
            settings.data = data;
            draw();
        };

        // Initialize
        init();

        return this;
    };

})(jQuery);