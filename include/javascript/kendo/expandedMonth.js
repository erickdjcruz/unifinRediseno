/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/**
 * Expanded Month View - change from default behavior: instad of showing max 2 events in a cell, show them all
 */
;(function() {
    var SchedulerView = window.kendo.ui.SchedulerView;

    //dependency functions. originally defined in kendo.scheduler.view
    function cellspacing() {
        if (kendo.support.cssBorderSpacing) {
            return '';
        }
        return 'cellspacing="0"';
    }

    function table(tableRows, className) {
        if (!tableRows.length) {
            return '';
        }
        return '<table ' + cellspacing() + ' class="' + $.trim('k-scheduler-table ' + (className || '')) + '">' +
            '<tr>' + tableRows.join('</tr><tr>') + '</tr>' + '</table>';
    }

    function content() {
        return $('<div class="k-scheduler-content">' + '<table ' + cellspacing() +
            ' class="k-scheduler-table"/>' + '</div>');
    }
    //dependencies end

    function getSpaceForDayNumber(element) {
        var fontSize = parseInt(kendo.getComputedStyles(element).fontSize);
        var defaultFontSize = 12;
        return fontSize || defaultFontSize;
    }

    window.kendo.ui.scheduler.MonthGroupedViewAllEvents = window.kendo.ui.scheduler.MonthGroupedView.extend({
        _addDaySlotCollections: function(groupCount, tableRows, startDate) {
            var spaceForDayNumber = getSpaceForDayNumber(this._view.element[0]);
            var view = this._view;
            var columnCount = 7;
            var rowCount = 6;
            for (var groupIndex = 0; groupIndex < groupCount; groupIndex++) {
                var cellCount = 0;
                var rowMultiplier = 0;
                if (view._isVerticallyGrouped()) {
                    rowMultiplier = groupIndex;
                }
                for (var rowIndex = rowMultiplier * rowCount; rowIndex < (rowMultiplier + 1) * rowCount; rowIndex++) {
                    var group = view.groups[groupIndex];
                    var collection = group.addDaySlotCollection(kendo.date.addDays(startDate, cellCount), kendo.date.addDays(startDate, cellCount + columnCount));
                    var tableRow = tableRows[rowIndex];
                    var cells = tableRow.children;
                    var cellMultiplier = 0;
                    tableRow.setAttribute('role', 'row');
                    if (!view._isVerticallyGrouped()) {
                        cellMultiplier = groupIndex;
                    }
                    for (var cellIndex = cellMultiplier * columnCount; cellIndex < (cellMultiplier + 1) * columnCount; cellIndex++) {
                        var cell = cells[cellIndex];

                        //custom code: change cells height, one by one
                        var start = kendo.date.toUtcTime(kendo.date.addDays(view.startDate(), cellCount));
                        var eventCount = view.getNumberOfEventsToday(start, null);

                        if (eventCount >= 2) {
                            marginTop = 30 + spaceForDayNumber; //offset for events + space for day number
                            padding = 3;
                            var newHeight = marginTop + eventCount * (view.options.eventHeight + padding);

                            $(cell).height(newHeight);
                        } else {
                            $(cell).height(98);
                        }
                        //end custom code

                        view.addDaySlot(collection, cell, startDate, cellCount);
                        cellCount++;
                    }
                }
            }
        }
    });

    window.expandedMonth = kendo.ui.scheduler.expandedMonth = kendo.ui.MonthView.extend({
        id: "scheduler.expandedMonth",
        name: "month",//Kendo uses this name to generate some classes needed to render the view
        type: "expandedMonth",
        options: {
            name: "month",
            type: "expandedMonth",
            title: "Month",
        },

        /**
         * We'll not group the calendar by Date
         */
        _getGroupedView: function() {
            if (this._isGroupedByDate()) {
                return new kendo.ui.scheduler.MonthGroupedByDateView(this);
            } else {
                return new kendo.ui.scheduler.MonthGroupedViewAllEvents(this);
            }
        },

        /**
         * The only customizations here are to set the numberOfEventsEachDay
         * and update resources position in order to correctly show near each calendar
         */
        render: function(events) {
            this.numberOfEventsEachDay = this.getNumberOfEventsEachDay(events);

            this.content.children(".k-event,.k-more-events,.k-events-container").remove();
            this._groups();

            events = new kendo.data.Query(events).sort([{ field: "start", dir: "asc" },{ field: "end", dir: "desc" }]).toArray();
            var resources = this.groupedResources;
            if (resources.length) {
                this._renderGroups(events, resources, 0, 1);
            } else {
                this._renderEvents(events, 0);
            }
            this.refreshLayout();
        },

        getNumberOfEventsToday: function(day, resource) {
            if (typeof(this.numberOfEventsEachDay) === 'undefined') {
                return 2;//default number of events in a cell
            }

            if (resource !== null && this.groupedResources && this.groupedResources.length === 1) {
                return this.numberOfEventsEachDay[resource][day];
            } else {
                return this.numberOfEventsEachDay[day];
            }
        },

        /**
         * Get number of events in each day (based on group)
         */
        getNumberOfEventsEachDay: function(events) {
            var localFormat = "YYYY-MM-DD[T]HH:mm:ss";
            var start = moment(moment(this.startDate()).format(localFormat));
            var end = moment(moment(this.endDate()).format(localFormat));

            //enumerate dates between start and end
            var datesEnum = [];
            var startDateTimestamp = kendo.date.toUtcTime(start.clone().toDate());
            datesEnum.push(startDateTimestamp);

            var currDate = start.clone().startOf('day');
            var lastDate = end.clone().startOf('day');

            while(currDate.add(1, 'days').diff(lastDate) < 0) {
                var newDayTimeStamp = kendo.date.toUtcTime(currDate.clone().toDate());
                datesEnum.push(newDayTimeStamp);
            }

            var endDateTimeStamp = kendo.date.toUtcTime(end.clone().toDate());
            datesEnum.push(endDateTimeStamp);


            //iterate over each day and build the result
            var result = {};
            _.each(datesEnum, function(dateInCalendar) {
                eventsInThisDay = _.filter(events, function(event) {
                    eventStart = kendo.date.toUtcTime(event.start);
                    eventEnd = kendo.date.toUtcTime(event.end);

                    /*
                        For some reason toUtcTime returns timestamp and 000 at the end.
                        So here i remove that 000 then i add 24 hours - 1 second and then i add the 000 back to get date end timestamp
                    */
                    endDateInCalendar = (dateInCalendar/1000 + (24*60*60 - 1)) * 1000;

                    var inThisDay = false;
                    if ((eventStart >= dateInCalendar && eventStart <= endDateInCalendar)
                        || (eventEnd >= dateInCalendar && eventEnd <= endDateInCalendar)
                        || (eventStart < dateInCalendar && eventEnd > endDateInCalendar)) {

                        inThisDay = true;
                    }

                    return inThisDay;
                });

                result[dateInCalendar] = eventsInThisDay.length;
            });

            return result;
        },

        /**
         * Remove the "more" button
         * Added space for day number
         */
        _positionEvent: function (slotRange, element, group) {
            var spaceForDayNumber = getSpaceForDayNumber(element[0]);
            var eventHeight = this.options.eventHeight;
            var startSlot = slotRange.start;
            if (slotRange.start.offsetLeft > slotRange.end.offsetLeft) {
                startSlot = slotRange.end;
            }
            var startIndex = slotRange.start.index;
            var endIndex = slotRange.end.index;
            var eventCount = startSlot.eventCount;
            var events = SchedulerView.collidingEvents(slotRange.events(), startIndex, endIndex);
            var rightOffset = startIndex !== endIndex ? 5 : 4;
            events.push({
                element: element,
                start: startIndex,
                end: endIndex
            });
            var rows = SchedulerView.createRows(events);
            //when asked to add an event in the scheduler, kendo recalculates the top for each event in the target cell/cells
            //here we manually set the top for each event div, considering the previous ones in that cell
            if (rows.length <= eventCount) {
                // ex: a cell has one event/row in it, but another cell in the same row (visual row) will have 2 events
                for (var idx = 0, length = Math.min(rows.length, eventCount); idx < length; idx++) {
                    var rowEvents = rows[idx].events;
                    var eventTop = startSlot.offsetTop + startSlot.firstChildHeight + idx * eventHeight + 3 * idx + spaceForDayNumber+ 'px';
                    for (var j = 0, eventLength = rowEvents.length; j < eventLength; j++) {
                        rowEvents[j].element[0].style.top = eventTop;
                    }
                }
            } else {
                //this is the custom case for us.
                //we have to set the "top" on the events kendo doesn't expect
                for (var idx = 0, length = Math.max(rows.length, eventCount); idx < length; idx++) {
                    var rowEvents = rows[idx].events;
                    var eventTop = startSlot.offsetTop + startSlot.firstChildHeight + idx * eventHeight + 3 * idx + spaceForDayNumber+ 'px';
                    for (var j = 0, eventLength = rowEvents.length; j < eventLength; j++) {
                        rowEvents[j].element[0].style.top = eventTop;
                    }
                }
            }

            slotRange.addEvent({
                element: element,
                start: startIndex,
                end: endIndex,
                groupIndex: startSlot.groupIndex
            });
            element[0].style.width = slotRange.innerWidth() - rightOffset + 'px';
            element[0].style.left = startSlot.offsetLeft + 2 + 'px';
            element[0].style.height = eventHeight + 'px';

            group._continuousEvents.push({
                element: element,
                uid: element.attr(kendo.attr('uid')),
                start: slotRange.start,
                end: slotRange.end
            });
            element.appendTo(this.content);
        },

        _bottomSection: function(columnLevels, rowLevels, rowCount) {
            this.content = content(columnLevels[columnLevels.length - 1], rowLevels[rowLevels.length - 1]);
            return $('<tr>').append(this.content.wrap('<td>').parent());
        }
    });
})();

