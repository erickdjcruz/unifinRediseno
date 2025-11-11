/*!
 * Chart.Funnel.js
 * A funnel plugin for Chart.js(http://chartjs.org/)
 * Version: 1.0.2
 *
 * Copyright 2016 Jone Casaper
 * Released under the MIT license
 * https://github.com/xch89820/Chart.Funnel.js/blob/master/LICENSE.md
 */
/**
 * Changes by SugarCRM
 * 11/9/2021: Updated code to reference Chart2 instead of Chart since Sugar loads Chartjs v2.9.4 as Chart2
 *            Added getCenterPoint function: see https://github.com/xch89820/Chart.Funnel.js/issues/7
 *            Added custom tooltip positioner
 */
(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}(g.Chart2 || (g.Chart2 = {})).Funnel = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){

},{}],2:[function(require,module,exports){
/**
 *
 * Main file of Chart.Funnel.js
 * @author Jone Casper
 * @email xu.chenhui@live.com
 *
 */

/* global window */
"use strict";

var Chart2 = require(1);
Chart2 = typeof(Chart2) === 'function' ? Chart2 : window.Chart2;

require(4)(Chart2);
require(3)(Chart2);

module.exports = Chart2;
},{"1":1,"3":3,"4":4}],3:[function(require,module,exports){
/**
 *
 * Extend funnel Charts
 * @author Jone Casper
 * @email xu.chenhui@live.com
 *
 * @example
 * var data = {
 *  labels: ["A", "B", "C"],
 * 	datasets: [{
 * 	  data: [300, 50, 100],
 * 	  backgroundColor: [
 *       "#FF6384",
 *       "#36A2EB",
 *       "#FFCE56"
 *    ],
 *    hoverBackgroundColor: [
 *        "#FF6384",
 *        "#36A2EB",
 *        "#FFCE56"
 *    ]
 * 	}]
 * }
 *
 */

"use strict";

module.exports = function(Chart2) {
	var helpers = Chart2.helpers;

	Chart2.defaults.funnel = {
		hover: {
			mode: "label"
		},
		sort: 'asc',// sort options: 'asc', 'desc'
		gap: 2,
		bottomWidth: null,// the bottom width of funnel
		topWidth: 0, // the top width of funnel
		keep: 'auto', // Keep left or right
		elements: {
			borderWidth: 0
		},
		tooltips: {
			callbacks: {
				title: function (tooltipItem, data) {
					return '';
				},
				label: function (tooltipItem, data) {
					return data.labels[tooltipItem.index] + ': ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
				}
			}
		},
		legendCallback: function (chart) {
			var text = [];
			text.push('<ul class="' + chart.id + '-legend">');

			var data = chart.data;
			var datasets = data.datasets;
			var labels = data.labels;

			if (datasets.length) {
				for (var i = 0; i < datasets[0].data.length; ++i) {
					text.push('<li><span style="background-color:' + datasets[0].backgroundColor[i] + '"></span>');
					if (labels[i]) {
						text.push(labels[i]);
					}
					text.push('</li>');
				}
			}

			text.push('</ul>');
			return text.join("");
		},
		legend: {
			labels: {
				generateLabels: function (chart) {
					var data = chart.data;
					if (data.labels.length && data.datasets.length) {
						return data.labels.map(function (label, i) {
							var meta = chart.getDatasetMeta(0);
							var ds = data.datasets[0];
							var trap = meta.data[i];
							var custom = trap.custom || {};
							var getValueAtIndexOrDefault = helpers.getValueAtIndexOrDefault;
							var trapeziumOpts = chart.options.elements.trapezium;
							var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, trapeziumOpts.backgroundColor);
							var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, trapeziumOpts.borderColor);
							var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, trapeziumOpts.borderWidth);

							return {
								text: label,
								fillStyle: fill,
								strokeStyle: stroke,
								lineWidth: bw,
								hidden: isNaN(ds.data[i]) || meta.data[i].hidden,

								// Extra data used for toggling the correct item
								index: i,
								// Original Index
								_index: trap._index
							};
						});
					} else {
						return [];
					}
				}
			},

			onClick: function (e, legendItem) {
				var index = legendItem.index;
				var chart = this.chart;
				var i, ilen, meta;

				for (i = 0, ilen = (chart.data.datasets || []).length; i < ilen; ++i) {
					meta = chart.getDatasetMeta(i);
					meta.data[index].hidden = !meta.data[index].hidden;
				}

				chart.update();
			}
		}
	};

	Chart2.controllers.funnel = Chart2.DatasetController.extend({

		dataElementType: Chart2.elements.Trapezium,

		linkScales: helpers.noop,

		update: function update(reset) {
			var me = this;
			var chart = me.chart,
				chartArea = chart.chartArea,
				opts = chart.options,
				meta = me.getMeta(),
				elements = meta.data,
				borderWidth = opts.elements.borderWidth || 0,
				availableWidth = chartArea.right - chartArea.left - borderWidth * 2,
				availableHeight = chartArea.bottom - chartArea.top - borderWidth * 2;

			// top and bottom width
			var bottomWidth = availableWidth,
				topWidth = (opts.topWidth < availableWidth ? opts.topWidth : availableWidth) || 0;
			if (opts.bottomWidth) {
				bottomWidth = opts.bottomWidth < availableWidth ? opts.bottomWidth : availableWidth;
			}

			// percentage calculation and sort data
			var sort = opts.sort,
				dataset = me.getDataset(),
				valAndLabels = [],
				visiableNum = 0,
				dMax = 0;
			helpers.each(dataset.data, function (val, index) {
				var backgroundColor = helpers.getValueAtIndexOrDefault(dataset.backgroundColor, index),
					hidden = elements[index].hidden;
				//if (!elements[index].hidden) {
				valAndLabels.push({
					hidden: hidden,
					orgIndex: index,
					val: val,
					backgroundColor: backgroundColor,
					borderColor: helpers.getValueAtIndexOrDefault(dataset.borderColor, index, backgroundColor),
					label: helpers.getValueAtIndexOrDefault(dataset.label, index, chart.data.labels[index])
				});
				//}
				if (!elements[index].hidden) {
					visiableNum++;
					dMax = val > dMax ? val : dMax;
				}
			});
			var dwRatio = bottomWidth / dMax;
				// sortedDataAndLabels = valAndLabels.sort(
				// 	sort === 'asc' ?
				// 		function (a, b) {
				// 			return a.val - b.val;
				// 		} :
				// 		function (a, b) {
				// 			return b.val - a.val;
				// 		}
				// );
            var sortedDataAndLabels = valAndLabels;
			// For render hidden view
			// TODO: optimization....
			var _viewIndex = 0;
			helpers.each(sortedDataAndLabels, function (dal, index) {
				dal._viewIndex = !dal.hidden ? _viewIndex++ : -1;
			});

			// Elements height calculation
			var gap = opts.gap || 0,
				elHeight = (availableHeight - ((visiableNum - 1) * gap)) / visiableNum;

			// save
			me.topWidth = topWidth;
			me.dwRatio = dwRatio;
			me.elHeight = elHeight;
			me.sortedDataAndLabels = sortedDataAndLabels;

			helpers.each(elements, function (trapezium, index) {
				me.updateElement(trapezium, index, reset);
			}, me);
		},

		// update elements
		updateElement: function updateElement(trapezium, index, reset) {
			var me = this,
				chart = me.chart,
				chartArea = chart.chartArea,
				opts = chart.options,
				sort = opts.sort,
				dwRatio = me.dwRatio,
				elHeight = me.elHeight,
				gap = opts.gap || 0,
				borderWidth = opts.elements.borderWidth || 0;

			// calculate x,y,base, width,etc.
			var x, y, x1, x2,
				elementType = 'isosceles',
				elementData = me.sortedDataAndLabels[index], upperWidth, bottomWidth,
				viewIndex = elementData._viewIndex < 0 ? index : elementData._viewIndex,
				base = chartArea.top + (viewIndex + 1) * (elHeight + gap) - gap;

			if (sort === 'asc') {
				// Find previous element which is visible
				var previousElement = helpers.findPreviousWhere(me.sortedDataAndLabels,
					function (el) {
						return !el.hidden;
					},
					index
				);
				upperWidth = previousElement ? previousElement.val * dwRatio : me.topWidth;
				bottomWidth = elementData.val * dwRatio;
			} else {
				var nextElement = helpers.findNextWhere(me.sortedDataAndLabels,
					function (el) {
						return !el.hidden;
					},
					index
				);
				upperWidth = elementData.val * dwRatio;
				bottomWidth = nextElement ? nextElement.val * dwRatio : me.topWidth;
			}

			let corners = trapezium.getCorners();
			let topY = _.max(_.pluck(corners, 1));
			let bottomY = _.min(_.pluck(corners, 1));
			y = (topY + bottomY) / 2;

			if (opts.keep === 'left') {
				elementType = 'scalene';
				x1 = chartArea.left + upperWidth / 2;
				x2 = chartArea.left + bottomWidth / 2;
			} else if (opts.keep === 'right') {
				elementType = 'scalene';
				x1 = chartArea.right - upperWidth/ 2;
				x2 = chartArea.right - bottomWidth / 2;
			} else {
				x = (chartArea.left + chartArea.right) / 2;
			}

			helpers.extend(trapezium, {
				// Utility
				_datasetIndex: me.index,
				_index: elementData.orgIndex,

				// Desired view properties
				_model: {
					type: elementType,
					y: y,
					base: base > chartArea.bottom ? chartArea.bottom : base,
					x: x,
					x1: x1,
					x2: x2,
					upperWidth: (reset || !!elementData.hidden) ? 0 : upperWidth,
					bottomWidth: (reset || !!elementData.hidden) ? 0 : bottomWidth,
					borderWidth: borderWidth,
					backgroundColor: elementData && elementData.backgroundColor,
					borderColor: elementData && elementData.borderColor,
					label: elementData && elementData.label
				}
			});

			trapezium.pivot();
		},
		removeHoverStyle: function (trapezium) {
			Chart2.DatasetController.prototype.removeHoverStyle.call(this, trapezium, this.chart.options.elements.trapezium);
		}
	});
};
},{}],4:[function(require,module,exports){
/**
 *
 * Extend trapezium element
 * @author Jone Casper
 * @email xu.chenhui@live.com
 *
 */

"use strict";

Chart2.Tooltip.positioners.custom = function getTooltipPosition(elements, eventPosition) {
    return {
        x : eventPosition.x,
        y : eventPosition.y
    };
};

module.exports = function(Chart2) {
	var helpers = Chart2.helpers,
		globalOpts = Chart2.defaults.global;

	globalOpts.elements.trapezium = {
		backgroundColor: globalOpts.defaultColor,
		borderWidth: 0,
		borderColor: globalOpts.defaultColor,
		borderSkipped: 'bottom',
		type: 'isosceles'  // isosceles, scalene
	};

	// Thanks for https://github.com/substack/point-in-polygon
	var pointInPolygon = function (point, vs) {
		// ray-casting algorithm based on
		// http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html

		var x = point[0], y = point[1];

		var inside = false;
		for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
			var xi = vs[i][0], yi = vs[i][1];
			var xj = vs[j][0], yj = vs[j][1];

			var intersect = ((yi > y) != (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
			if (intersect) inside = !inside;
		}

		return inside;
	};
    var calculatedHeight;
    var calculatedWidth;
    var contextChartWidth;
    var calculatedCenter
    var r = 0.3; // resonable amound for ratio of width to height (or slope)

	Chart2.elements.Trapezium = Chart2.Element.extend({
        /**
         * Calculate corners of the trapezium

         var calculatedHeight = 411;
         var calculatedWidth = 374;
         var calculatedCenter = 510;
         var r = 0.3; // ratio of width to height (or slope)

         Chart.elements.Trapezium.prototype.pointsTrapezoid({
				_top:335.2673228221171,
				_bottom:347.19232494739117
			}, calculatedWidth)

         * @param {Object} d
         * @param {Float} w
         * @return {Array}
         */
        pointsTrapezoid: function(d, w) {
            var y0 = d._bottom,
                y1 = d._top,
                w0 = w / 2 - r * y0,
                w1 = w / 2 - r * y1,
                c = calculatedCenter;

            return (
                (c - w0) + ',' + y0 + ' ' +
                (c - w1) + ',' + y1 + ' ' +
                (c + w1) + ',' + y1 + ' ' +
                (c + w0) + ',' + y0
            );
        },

        /**
         Projects funnel data into the plane. Vertical positioning for each element is returned.
         It calculates scales in the same manner sucrose does: bottom to top

         var calculatedHeight = 0;
         var calculatedWidth = 352;
         var calculatedCenter = 176;
         var r = 0.3; // ratio of width to height (or slope)

         Chart.elements.Trapezium.prototype.calcScales([
         30, 70 //in sync with funnelTotal
         ])
         * @param {Array} data
         * @param {Trapezium} self
         * @return {Array}
         */
        calcScales: function(data, self) {
            var areaTrapezoid = function(h, w) {
                return h * (w - h * r);
            };
            var heightTrapezoid = function(a, b) {
                var x = b / r / 2;
                return Math.abs(Math.sqrt(a / r + x * x)) - x;
            };

            // sum the values of visible elements
            var funnelTotal = 0;
            for(var i = 0; i< data.length; i++) {
                if (self._chart.getDatasetMeta(0).data[i].hidden === false) {
                    funnelTotal += data[i];
                }
            }
            var funnelArea = areaTrapezoid(calculatedHeight, calculatedWidth),
                funnelShift = 0,
                funnelMinHeight = 4,
                _base = calculatedWidth - 2 * r * calculatedHeight,
                _bottom = calculatedHeight;

            //------------------------------------------------------------
            // Adjust points to compensate for parallax of slice
            // by increasing height relative to area of funnel
            var res = [];
            // // runs from bottom to top
            for (var i=0; i < data.length; i++) {
                var value = data[i];
                var pointValue = data[i];

                var _height = funnelTotal > 0 ?
                    heightTrapezoid(funnelArea * pointValue / funnelTotal, _base) :
                    0;

                var point = {};
                point._base = _base;
                point._bottom = _bottom;
                point._top = point._bottom - _height;

                //update the pointers the next element will use to calculate his position
                if (pointValue !== 0) {
                    _base += 2 * r * _height;
                    _bottom -= _height;
                }

                res.push({
                    _top: point._top,
                    _bottom: point._bottom,
                });
            };

            return res;
        },

        /**
         * ChartJs entrypoint
         * Calculates corners of current dataset trapezium
         */
        getCorners: function () {
            var vm = this._view;
            var topOffset = this._getTopOffset();

            calculatedHeight = this._chart.height - topOffset;
            calculatedWidth =  this._chart.width - 40;
            contextChartWidth = this._chart.width;
            calculatedCenter = contextChartWidth / 2;// ox center

            var initialVals = [];
            var allValues = this._chart.data.datasets[0].data;

            for (var i = 0; i < allValues.length; i++) {
                if (this._chart.getDatasetMeta(0).data[i].hidden === false) {
                    initialVals.push(allValues[i]);
                } else {
                    initialVals.push(0);
                }
            }

            var data = Chart2.elements.Trapezium.prototype.calcScales(initialVals, this);

            // var revertedDataIndex = data.length - this._index - 1;
            var d = data[this._index];

            var poligon = Chart2.elements.Trapezium.prototype.pointsTrapezoid(d, calculatedWidth);

            var finalCoordinates = this._formatCoordinates(poligon);
            finalCoordinates = this._addTopOffset(finalCoordinates, topOffset);

            if (initialVals[this._index] === 0) {
                // don't draw any shape when value is 0
                return [
                    [0,0],
                    [0,0],
                    [0,0],
                    [0,0],
                ];
            }

            //add some space or border between elements
            //1 2
            //0 3
            finalCoordinates[0][1] -= 2;
            finalCoordinates[3][1] -= 2;

            return finalCoordinates;
        },

        /**
         * 	Given coordinates in Sugar format,
         *  returns coordinates in ChartJS format
         *
         * @param {string} poligon
         * @return Array
         */
        _formatCoordinates: function(poligon) {
            var coordinates = poligon.split(" ");
            var finalCoordinates = [];
            for(var i = 0; i< coordinates.length; i++) {
                var c = coordinates[i];
                c = c.split(",");
                c[0] = parseFloat(c[0]);
                c[1] = parseFloat(c[1]);
                finalCoordinates.push(c);
            }
            return finalCoordinates;
        },

        _getTopOffset: function() {
            var topOffset = 0;
            for(var i = 0; i< this._chart.boxes.length; i++) {
                var box = this._chart.boxes[i];
                if (box.position === "top") {
                    topOffset += box.height;
                }
            }
            return topOffset;
        },

        _addTopOffset: function(poligon, topOffset) {
            poligon[0][1] = parseFloat(poligon[0][1]) + topOffset;
            poligon[1][1] = parseFloat(poligon[1][1]) + topOffset;
            poligon[2][1] = parseFloat(poligon[2][1]) + topOffset;
            poligon[3][1] = parseFloat(poligon[3][1]) + topOffset;

            return poligon;
        },

        getCornersBackup: function () {
			var vm = this._view;
			var globalOptionTrapeziumElements = globalOpts.elements.trapezium;

			var corners = [],
				type = vm.type || globalOptionTrapeziumElements.type,
				top = vm.y,
				borderWidth = vm.borderWidth || globalOptionTrapeziumElements.borderWidth,
				upHalfWidth = vm.upperWidth / 2,
				botHalfWidth = vm.bottomWidth / 2,
				halfStroke = borderWidth / 2;

			halfStroke = halfStroke < 0 ? 0 : halfStroke;

			// An isosceles trapezium
			if (type == 'isosceles') {
				var x = vm.x;

				// Corner points, from bottom-left to bottom-right clockwise
				// | 1 2 |
				// | 0 3 |
				corners = [
					[x - botHalfWidth + halfStroke, vm.base],
					[x - upHalfWidth + halfStroke, top + halfStroke],
					[x + upHalfWidth - halfStroke, top + halfStroke],
					[x + botHalfWidth - halfStroke, vm.base]
				];
			} else if (type == 'scalene') {
				var x1 = vm.x1,
					x2 = vm.x2;

				corners = [
					[x2 - botHalfWidth + halfStroke, vm.base],
					[x1 - upHalfWidth + halfStroke, top + halfStroke],
					[x1 + upHalfWidth - halfStroke, top + halfStroke],
					[x2 + botHalfWidth - halfStroke, vm.base]
				];
			}


			return corners;
		},
		draw: function () {
			var ctx = this._chart.ctx;
			var vm = this._view;
			var globalOptionTrapeziumElements = globalOpts.elements.trapezium;

			var corners = this.getCorners();
			this._cornersCache = corners;

			ctx.beginPath();
			ctx.fillStyle = vm.backgroundColor || globalOptionTrapeziumElements.backgroundColor;
			ctx.strokeStyle = vm.borderColor || globalOptionTrapeziumElements.borderColor;
			ctx.lineWidth = vm.borderWidth || globalOptionTrapeziumElements.borderWidth;

			// Find first (starting) corner with fallback to 'bottom'
			var borders = ['bottom', 'left', 'top', 'right'];
			var startCorner = borders.indexOf(
				vm.borderSkipped || globalOptionTrapeziumElements.borderSkipped,
				0);
			if (startCorner === -1)
				startCorner = 0;

			function cornerAt(index) {
				return corners[(startCorner + index) % 4];
			}

			// Draw rectangle from 'startCorner'
			ctx.moveTo.apply(ctx, cornerAt(0));
			for (var i = 1; i < 4; i++)
				ctx.lineTo.apply(ctx, cornerAt(i));

			ctx.fill();
			if (vm.borderWidth) {
				ctx.stroke();
			}
		},
		height: function () {
			var vm = this._view;
			if (!vm) {
				return 0;
			}

			return vm.base - vm.y;
		},
		inRange: function (mouseX, mouseY) {
			var vm = this._view;
			if (!vm) {
				return false;
			}
			var corners = this._cornersCache ? this._cornersCache : this.getCorners();
			return pointInPolygon([mouseX, mouseY], corners);
		},
		inLabelRange: function (mouseX) {
			var x,
				vm = this._view;

			if (!vm) {
				return false;
			}

			if (vm.type == 'scalene') {
				if (vm.x1 > vm.x2) {
					return mouseX >= vm.x2 - vm.bottomWidth / 2 && mouseX <= vm.x1 + vm.upperWidth / 2;
				} else {
					return mouseX <= vm.x2 + vm.bottomWidth / 2 && mouseX >= vm.x1 - vm.upperWidth / 2;
				}
			}

			var maxWidth = Math.max(vm.upperWidth, vm.bottomWidth);
			return mouseX >= vm.x - maxWidth / 2 && mouseX <= vm.x + maxWidth / 2;

		},
        getCenterPoint: function () { var vm = this._view; return { x: vm.x, y: vm.y }; },


        tooltipPosition: function () {
			var vm = this._view;
			return {
				x: vm.x || vm.x2,
				y: vm.base - (vm.base - vm.y)/2
			};
		}
	});
};
},{}]},{},[2])(2)
});
