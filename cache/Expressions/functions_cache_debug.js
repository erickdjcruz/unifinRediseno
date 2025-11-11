/**
 * Construct a new YearExpression.
 */
SUGAR.expressions.YearExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.YearExpression, SUGAR.expressions.NumericExpression, {
    className: "YearExpression",
    evaluate: function() {
            var time = this.getParameters().evaluate();
            if (_.isString(time) && _.isEmpty(time)) {
                return '';
            }
            return new Date(time).getFullYear();
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['date'];
                }
});

/**
 * Construct a new DaysUntilExpression.
 */
SUGAR.expressions.DaysUntilExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DaysUntilExpression, SUGAR.expressions.NumericExpression, {
    className: "DaysUntilExpression",
    evaluate: function() {
            var then = SUGAR.util.DateUtils.parse(this.getParameters().evaluate(), 'user');
			var now = new Date();
			then.setHours(0);
			then.setMinutes(0);
			then.setSeconds(0);
			var diff = then - now;
			var days = Math.ceil(diff / 86400000);

			return days;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['date'];
                }
});

/**
 * Construct a new NowExpression.
 */
SUGAR.expressions.NowExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.NowExpression, SUGAR.expressions.DateExpression, {
    className: "NowExpression",
    evaluate: function() {
		    var d = SUGAR.util.DateUtils.getUserTime();
		    d.setSeconds(0);

		    // if we're calling this from Sidecar, we need to pass back the date
            // as a string, not a Date object otherwise it won't validate properly
            if (this.context.view) {
                d = App.date.format(d, 'Y-m-d H:i:s');
            }

		    return d;
    }
    ,getParamCount: function() {
        return 0;
    }
});

/**
 * Construct a new HoursUntilExpression.
 */
SUGAR.expressions.HoursUntilExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.HoursUntilExpression, SUGAR.expressions.NumericExpression, {
    className: "HoursUntilExpression",
    evaluate: function() {
            var value = this.getParameters().evaluate();
            var then = SUGAR.util.DateUtils.parse(value);

            if (!then && then !== 0) return false;
            var now = new Date();

            // If we have a date field
            if (typeof value == 'string' && value.indexOf(' ') == -1 && value.indexOf('T') == -1) {
                now.setHours(0, 0, 0, 0);
            }

            var diff = then - now;

            return ~~(diff / 3600000);
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['date'];
                }
});

/**
 * Construct a new TodayExpression.
 */
SUGAR.expressions.TodayExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.TodayExpression, SUGAR.expressions.DateExpression, {
    className: "TodayExpression",
    evaluate: function() {
		  var d = new Date();
		  d.setHours(0);
		  d.setMinutes(0);
		  d.setSeconds(0);

		    // if we're calling this from Sidecar, we need to pass back the date
            // as a string, not a Date object otherwise it won't validate properly
            if (this.context.view) {
                d = App.date.format(d, 'Y-m-d');
            }

		  return d;
    }
    ,getParamCount: function() {
        return 0;
    }
});

/**
 * Construct a new DefineDateExpression.
 */
SUGAR.expressions.DefineDateExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DefineDateExpression, SUGAR.expressions.DateExpression, {
    className: "DefineDateExpression",
    evaluate: function() {
			var params = this.getParameters().evaluate();
			var time   = SUGAR.util.DateUtils.parse(params, 'user');
			if (time == false)	throw "Incorrect date format";

			return time;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new AddDaysExpression.
 */
SUGAR.expressions.AddDaysExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.AddDaysExpression, SUGAR.expressions.DateExpression, {
    className: "AddDaysExpression",
    evaluate: function() {
		    var params = this.getParameters();
            var fromDate = params[0].evaluate();
            if (!fromDate) {
                return '';
            }
			var days = parseInt(params[1].evaluate(), 10);
			if (_.isNaN(days)) {
				return '';
			}
			var date = SUGAR.util.DateUtils.parse(fromDate, 'user');

            //Clone the object to prevent possible issues with other operations on this variable.
            var d = new Date(date);
            d.setDate(d.getDate() + days);

            // if we're calling this from Sidecar, we need to pass back the date
            // as a string, not a Date object otherwise it won't validate properly
            if (this.context.view) {
                d = App.date.format(d, 'Y-m-d');
            }

            return d;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['date','number'];
                }
});

/**
 * Construct a new MinDateConditionalRelatedExpression.
 */
SUGAR.expressions.MinDateConditionalRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MinDateConditionalRelatedExpression, SUGAR.expressions.DateExpression, {
    className: "MinDateConditionalRelatedExpression",
    evaluate: function() {

        if (App === undefined) {
            console.log('The rollupConditionalMinDate SugarLogic function has been deprecated since 10.0.0 ' +
            'and will be removed in a later release');
            return SUGAR.expressions.Expression.FALSE;
        }
        App.logger.warn('The rollupConditionalMinDate SugarLogic function has been deprecated since 10.0.0 ' +
            'and will be removed in a later release');
        
        // Parse the arguments
        var params = this.params;
        var target = this.context.target;
        var relationship = params[0].evaluate();
        var rel_field = params[1].evaluate();
        
        // Get information about the model
        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship);
        var hasModelBeenRemoved = this.context.isRemoveEvent || false;
        
        // Get the values of the field in the related beans
        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'rollupConditionalMinDate',
                rel_field,
                model,
                (hasModelBeenRemoved)? 'remove' : 'add'
            );
        }
        var all_values = this.context.getRelatedCollectionValues(this.context.model,
            relationship, 'rollupConditionalMinDate', rel_field) || {};
        
        // Parse the related values as dates, sort in ascending order, and take
        // the first one
        var rollupValue = '';
        var dates = [];
        _.each(all_values, function(_date) {
            dates.push(new App.date(_date));
        });
        var sortByDateAsc = function (lhs, rhs) { return lhs > rhs ? 1 : lhs < rhs ? -1 : 0; };
        rollupValue = dates.sort(sortByDateAsc)[0];
        
        // Convert the minimum value found into the correct format for this field (date or timestamp)
        if (this.context.getField(target).type === 'date') {
            rollupValue = rollupValue.format('YYYY-MM-DD');
        } else {
            rollupValue = rollupValue.valueOf();
        }

        // If the rollup value is different from the current value, set the
        // rollup value as the new field value. Otherwise, just return the old value
        var currentValue = this.context.model.get(target);
        if (!_.isEqual(rollupValue, currentValue)) {
            this.context.model.set(target, rollupValue);
            this.context.updateRelatedFieldValue(
                relationship,
                'rollupConditionalMinDate',
                rel_field,
                rollupValue,
                this.context.model.isNew()
            );
            return rollupValue;
        } else {
            return currentValue;
        }
    }
    ,getParamCount: function() {
        return 4;
    }
    ,getParameterTypes: function() {
        return ['relate','string','enum','enum'];
                }
});

/**
 * Construct a new DayOfMonthExpression.
 */
SUGAR.expressions.DayOfMonthExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DayOfMonthExpression, SUGAR.expressions.NumericExpression, {
    className: "DayOfMonthExpression",
    evaluate: function() {
            var time = this.getParameters().evaluate();
            if (_.isString(time) && _.isEmpty(time)) {
                return '';
            }
            return new Date(time).getDate();
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['date'];
                }
});

/**
 * Construct a new MaxRelatedDateExpression.
 */
SUGAR.expressions.MaxRelatedDateExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MaxRelatedDateExpression, SUGAR.expressions.DateExpression, {
    className: "MaxRelatedDateExpression",
    evaluate: function() {
        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }

        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            rel_field = params[1].evaluate();

        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship),
            model_id = model.id || model.cid,
            // has the model been removed from it's collection
            sortByDateDesc = function (lhs, rhs) { return lhs < rhs ? 1 : lhs > rhs ? -1 : 0; },
            hasModelBeenRemoved = this.context.isRemoveEvent || false,
            current_value = this.context.getRelatedField(relationship, 'maxRelatedDate', rel_field) || '',
            new_value = model.get(rel_field) || '',
            dates = [],
            rollup_value = '';

        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'maxRelatedDate',
                rel_field,
                model,
                (hasModelBeenRemoved ? 'remove' : 'add')
            );
        }

        var all_values = this.context.getRelatedCollectionValues(this.context.model, relationship, 'maxRelatedDate', rel_field) || {};

        if (_.isEqual(new_value, '')) {
            return current_value;
        }

        _.each(all_values, function(_date) {
            dates.push(new App.date(_date));
        });

        // now the furthest out is on top
        rollup_value = dates.sort(sortByDateDesc)[0].format('YYYY-MM-DD');

        if (!_.isEqual(rollup_value, current_value)) {
            this.context.model.set(target, rollup_value);
            this.context.updateRelatedFieldValue(
                relationship,
                'maxRelatedDate',
                rel_field,
                rollup_value,
                this.context.model.isNew()
            );
        }
    }
    ,getParameterTypes: function() {
        return ['relate','string'];
                }
});

/**
 * Construct a new MonthOfYearExpression.
 */
SUGAR.expressions.MonthOfYearExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MonthOfYearExpression, SUGAR.expressions.NumericExpression, {
    className: "MonthOfYearExpression",
    evaluate: function() {
            var time = this.getParameters().evaluate();
            if (_.isString(time) && _.isEmpty(time)) {
                return '';
            }
            return new Date(time).getMonth() + 1;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['date'];
                }
});

/**
 * Construct a new DayOfWeekExpression.
 */
SUGAR.expressions.DayOfWeekExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DayOfWeekExpression, SUGAR.expressions.NumericExpression, {
    className: "DayOfWeekExpression",
    evaluate: function() {
            var day,
                time = this.getParameters().evaluate();

            if (_.isString(time) && _.isEmpty(time)) {
                return '';
            }
            //Checks to see if the user is on a sidecar view and return results as a string
            if (this.context.view) {
                day = App.date(time).format('d').toString();
            } else {
                day = new Date(time).getDay();
            }

           return day;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['date'];
                }
});

/**
 * Construct a new TimestampExpression.
 */
SUGAR.expressions.TimestampExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.TimestampExpression, SUGAR.expressions.DateExpression, {
    className: "TimestampExpression",
    evaluate: function() {
	    var datetime = this.getParameters().evaluate(),
            arr,
            ret = [],
            date = this.context.parseDate(datetime);

        return Math.round(+date.getTime()/1000);
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new ForecastOnlySalesStageExpression.
 */
SUGAR.expressions.ForecastOnlySalesStageExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ForecastOnlySalesStageExpression, SUGAR.expressions.EnumExpression, {
    className: "ForecastOnlySalesStageExpression",
    evaluate: function() {

            // this doesn't support BWC modules, so it should return the full list of dom elememnts
            if (App === undefined) {
                return SUGAR.language.get('app_list_strings', 'sales_stage_dom');
            }

            var SEE = SUGAR.expressions.Expression;
            var config = App.metadata.getModule('Forecasts', 'config');
            var params = this.getParameters();
            var includeWon = params[0].evaluate();
            var includeClosed = params[1].evaluate();
            var includeEverythingElse = params[2].evaluate();
            var array = _.keys(App.lang.getAppListStrings('sales_stage_dom'));
            var keysToInclude = [];

            if (SEE.isTruthy(includeWon)) {
                keysToInclude = _.union(keysToInclude, config.sales_stage_won);
            }

            if (SEE.isTruthy(includeClosed)) {
                keysToInclude = _.union(keysToInclude, config.sales_stage_lost);
            }

            if (SEE.isTruthy(includeEverythingElse)) {
                var nonClosedKeys = _.difference(array, config.sales_stage_won, config.sales_stage_lost)
                keysToInclude = _.union(keysToInclude, nonClosedKeys);
            }

            return keysToInclude;
    }
    ,getParamCount: function() {
        return 3;
    }
    ,getParameterTypes: function() {
        return ['boolean','boolean','boolean'];
                }
});

/**
 * Construct a new ForecastSalesStageExpression.
 */
SUGAR.expressions.ForecastSalesStageExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ForecastSalesStageExpression, SUGAR.expressions.EnumExpression, {
    className: "ForecastSalesStageExpression",
    evaluate: function() {

            // this doesn't support BWC modules, so it should return the full list of dom elememnts
            if (App === undefined) {
		        return SUGAR.language.get('app_list_strings', 'sales_stage_dom');
			}

			var SEE = SUGAR.expressions.Expression,
			    config = App.metadata.getModule('Forecasts', 'config'),
			    params = this.getParameters(),
			    includeWon = params[0].evaluate(),
			    includeClosed = params[1].evaluate(),
			    array = _.keys(App.lang.getAppListStrings('sales_stage_dom')),
			    keysToRemove = [];

            if (!SEE.isTruthy(includeWon)) {
                keysToRemove = _.union(keysToRemove, config.sales_stage_won);
            }

            if (!SEE.isTruthy(includeClosed)) {
                keysToRemove = _.union(keysToRemove, config.sales_stage_lost);
            }

			return _.difference(array, keysToRemove);
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['boolean','boolean'];
                }
});

/**
 * Construct a new SugarTranslatedDropDownExpression.
 */
SUGAR.expressions.SugarTranslatedDropDownExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SugarTranslatedDropDownExpression, SUGAR.expressions.EnumExpression, {
    className: "SugarTranslatedDropDownExpression",
    evaluate: function() {
			var dd = this.getParameters().evaluate(),
				arr, ret = [];
			if (App){
				arr = App.lang.getAppListStrings(dd);
			}
			else {
				arr = SUGAR.language.get('app_list_strings', dd);
			}
			if (arr && arr != "undefined") {
				for (var i in arr) {
					if (typeof i == "string")
						ret[ret.length] = arr[i];
				}
			}

			return ret;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return 'string';
    }
});

/**
 * Construct a new DefineEnumExpression.
 */
SUGAR.expressions.DefineEnumExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DefineEnumExpression, SUGAR.expressions.EnumExpression, {
    className: "DefineEnumExpression",
    evaluate: function() {
			var params = this.getParameters();
			var array = [];
			if (typeof(params.length) != "undefined")
			{
				for ( var i = 0; i < params.length; i++ ) {
					array[array.length] = params[i].evaluate();
				}
			} else {
				return [params.evaluate()];
			}
			return array;
    }
    ,getParameterTypes: function() {
        return 'generic';
    }
});

/**
 * Construct a new SugarListWhereExpression.
 */
SUGAR.expressions.SugarListWhereExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SugarListWhereExpression, SUGAR.expressions.EnumExpression, {
    className: "SugarListWhereExpression",
    evaluate: function() {
        	var params = this.getParameters();
        	var trigger = params[0].evaluate();
        	var lists = params[1].evaluate();
        	var array = [];
        	for ( var i = 0; i < lists.length; i++ ) {
        	    if (lists[i].length > 0) {
        	        if (lists[i][0] == trigger) {
        	            array = lists[i][1];
        	            break;
        	        }
        	    }
        	}
        	return array == "undefined" ? [] : array;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['string','enum'];
                }
});

/**
 * Construct a new SugarDropDownExpression.
 */
SUGAR.expressions.SugarDropDownExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SugarDropDownExpression, SUGAR.expressions.EnumExpression, {
    className: "SugarDropDownExpression",
    evaluate: function() {
			var dd = this.getParameters().evaluate(),
				arr, ret = [];
			if (App){
				arr = App.lang.getAppListStrings(dd);
			}
			else {
				arr = SUGAR.language.get('app_list_strings', dd);
			}
			if (arr && arr != "undefined")
			{
				for (var i in arr) {
					if (typeof i == "string")
						ret[ret.length] = i;
				}
			}
			return ret;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return 'string';
    }
});

/**
 * Construct a new ForecastIncludedCommitStagesExpression.
 */
SUGAR.expressions.ForecastIncludedCommitStagesExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ForecastIncludedCommitStagesExpression, SUGAR.expressions.EnumExpression, {
    className: "ForecastIncludedCommitStagesExpression",
    evaluate: function() {

            // this doesn't support BWC modules, so it should return the full list of dom elememnts
            if (App === undefined) {
                return SUGAR.language.get('app_list_strings', 'sales_stage_dom');
            }

            var config = App.metadata.getModule('Forecasts', 'config');

            return config.commit_stages_included;
    }
    ,getParamCount: function() {
        return 0;
    }
    ,getParameterTypes: function() {
        return [];
                }
});

/**
 * Construct a new HourOfDayExpression.
 */
SUGAR.expressions.HourOfDayExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.HourOfDayExpression, SUGAR.expressions.TimeExpression, {
    className: "HourOfDayExpression",
    evaluate: function() {
			var time = this.getParameters().evaluate();
			return new Date(time).getHours();
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new DefineTimeExpression.
 */
SUGAR.expressions.DefineTimeExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DefineTimeExpression, SUGAR.expressions.TimeExpression, {
    className: "DefineTimeExpression",
    evaluate: function() {
			var params = this.getParameters().evaluate();
			var time   = Date.parse(params);

			if ( isNaN(time) )	throw "Incorrect time format";

			return time;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new SubtractExpression.
 */
SUGAR.expressions.SubtractExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SubtractExpression, SUGAR.expressions.NumericExpression, {
    className: "SubtractExpression",
    evaluate: function() {
			var params = this.getParameters(),
			diff   = params[0].evaluate();
			for (var i = 1; i < params.length; i++) {
                diff = this.context.subtract(diff, params[i].evaluate());
            }
			return diff;
    }
});

/**
 * Construct a new DivideExpression.
 */
SUGAR.expressions.DivideExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DivideExpression, SUGAR.expressions.NumericExpression, {
    className: "DivideExpression",
    evaluate: function() {
			var params = this.getParameters(),
			    numerator   = params[0].evaluate();
			    denominator = params[1].evaluate();
            if (denominator == 0) {
			    throw "Division by 0 error";
            }
			return this.context.divide(numerator, denominator);
    }
    ,getParamCount: function() {
        return 2;
    }
});

/**
 * Construct a new LogExpression.
 */
SUGAR.expressions.LogExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.LogExpression, SUGAR.expressions.NumericExpression, {
    className: "LogExpression",
    evaluate: function() {
		      var params = this.getParameters();

            var base = params[1].evaluate();
            var value = params[0].evaluate();

            return this.context.divide(Math.log(value), Math.log(base));
    }
    ,getParamCount: function() {
        return 2;
    }
});

/**
 * Construct a new AverageExpression.
 */
SUGAR.expressions.AverageExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.AverageExpression, SUGAR.expressions.NumericExpression, {
    className: "AverageExpression",
    evaluate: function() {
			var sum   = 0;
			var count = 0;
			var params = this.getParameters();
			for (var i = 0; i < params.length; i++) {
			    sum = this.context.add(sum, params[i].evaluate());
				count++;
			}
			// since Expression guarantees at least 1 parameter
			// we can safely assume / by 0 will not happen
			return this.context.divide(sum, count);
    }
});

/**
 * Construct a new MedianExpression.
 */
SUGAR.expressions.MedianExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MedianExpression, SUGAR.expressions.NumericExpression, {
    className: "MedianExpression",
    evaluate: function() {
			var params = this.getParameters();
			var values = new Array();

			for ( var i = 0; i < params.length; i++ )
				values[values.length] = parseFloat(params[i].evaluate());

			// sort numerically
			values.sort(function (a, b) {return a - b;});

			if (values.length % 2 == 0) {
				return (values[values.length/2] + values[values.length/2 - 1]) / 2;
			}

			return values[ Math.round(values.length/2) - 1 ];
    }
});

/**
 * Construct a new AverageRelatedExpression.
 */
SUGAR.expressions.AverageRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.AverageRelatedExpression, SUGAR.expressions.NumericExpression, {
    className: "AverageRelatedExpression",
    evaluate: function() {
        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }
        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            rel_field = params[1].evaluate();
        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship),
            // has the model been removed from it's collection
            isCurrency = (model.fields[rel_field].type === 'currency'),
            precision = model.fields[rel_field].precision || 6,
            hasModelBeenRemoved = this.context.isRemoveEvent || false,
            current_value = this.context.getRelatedField(relationship, 'rollupAve', rel_field) || '',
            rollup_value = '0';

        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'rollupAve',
                rel_field,
                model,
                (hasModelBeenRemoved ? 'remove' : 'add')
            );
        }
        var all_values = this.context.getRelatedCollectionValues(this.context.model, relationship, 'rollupAve', rel_field) || {};

        if (_.size(all_values) > 0) {
            rollup_value = _.reduce(all_values, function(memo, number) {
                return App.math.add(memo, number, precision, true);
            }, '0');

            rollup_value = App.math.div(rollup_value, _.size(all_values), precision, true);

            if (isCurrency) {
                rollup_value = App.currency.convertFromBase(
                    rollup_value,
                    this.context.model.get('currency_id')
                );
            }
        }

        if (!_.isEqual(rollup_value, current_value)) {
            this.context.model.set(target, rollup_value);
            this.context.updateRelatedFieldValue(
                relationship,
                'rollupAve',
                rel_field,
                rollup_value,
                this.context.model.isNew()
            );
        }

        return rollup_value;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['relate','string'];
                }
});

/**
 * Construct a new FloorExpression.
 */
SUGAR.expressions.FloorExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.FloorExpression, SUGAR.expressions.NumericExpression, {
    className: "FloorExpression",
    evaluate: function() {
			return Math.floor( this.getParameters().evaluate() );
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new MinimumExpression.
 */
SUGAR.expressions.MinimumExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MinimumExpression, SUGAR.expressions.NumericExpression, {
    className: "MinimumExpression",
    evaluate: function() {
			var params = this.getParameters();
			var min = null;
			for (var i = 0; i < params.length; i++) {
				var val = 	params[i].evaluate();
				if(min == null || val < min)
					min = val;
			}
			return min;
    }
});

/**
 * Construct a new AbsoluteValueExpression.
 */
SUGAR.expressions.AbsoluteValueExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.AbsoluteValueExpression, SUGAR.expressions.NumericExpression, {
    className: "AbsoluteValueExpression",
    evaluate: function() {
			return Math.abs(this.getParameters().evaluate());
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new MinRelatedExpression.
 */
SUGAR.expressions.MinRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MinRelatedExpression, SUGAR.expressions.NumericExpression, {
    className: "MinRelatedExpression",
    evaluate: function() {
        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }
        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            rel_field = params[1].evaluate();
        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship),
            // has the model been removed from it's collection
            isCurrency = (model.fields[rel_field].type === 'currency'),
            sortByDesc = function (lhs, rhs) { return parseFloat(lhs) > parseFloat(rhs) ? 1 : parseFloat(lhs) < parseFloat(rhs) ? -1 : 0; },
            hasModelBeenRemoved = this.context.isRemoveEvent || false,
            current_value = this.context.getRelatedField(relationship, 'rollupMin', rel_field) || '',
            finite_values = {},
            rollup_value = '0';

        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'rollupMin',
                rel_field,
                model,
                (hasModelBeenRemoved ? 'remove' : 'add')
            );
        }
        var all_values = this.context.getRelatedCollectionValues(this.context.model, relationship, 'rollupMin', rel_field) || {};

        if (_.size(all_values) > 0) {
            finite_values = _.map(_.values(all_values), _.partial(parseInt, _, 10));
            finite_values = _.filter(finite_values, _.isFinite);

            // get all the values and sort them so the highest is on top
            rollup_value = finite_values.sort(sortByDesc)[0];

            if (isCurrency) {
                rollup_value = App.currency.convertFromBase(
                    rollup_value,
                    this.context.model.get('currency_id')
                );
            }
        }

        if (!_.isEqual(rollup_value, current_value)) {
            this.context.model.set(target, rollup_value);
            this.context.updateRelatedFieldValue(
                relationship,
                'rollupMin',
                rel_field,
                rollup_value,
                this.context.model.isNew()
            );
        }

        return rollup_value;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['relate','string'];
                }
});

/**
 * Construct a new RoundExpression.
 */
SUGAR.expressions.RoundExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.RoundExpression, SUGAR.expressions.NumericExpression, {
    className: "RoundExpression",
    evaluate: function() {
			var params = this.getParameters();

			var base = params[0].evaluate();
			var precision = params[1].evaluate();

			return this.context.round(base, precision);
    }
    ,getParamCount: function() {
        return 2;
    }
});

/**
 * Construct a new AddExpression.
 */
SUGAR.expressions.AddExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.AddExpression, SUGAR.expressions.NumericExpression, {
    className: "AddExpression",
    evaluate: function() {
			var params = this.getParameters(),
			    sum = 0;
			for (var i = 0; i < params.length; i++) {
                sum = this.context.add(sum, params[i].evaluate());
            }
			return sum;
    }
});

/**
 * Construct a new PowerExpression.
 */
SUGAR.expressions.PowerExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.PowerExpression, SUGAR.expressions.NumericExpression, {
    className: "PowerExpression",
    evaluate: function() {
			var params = this.getParameters();

			var base = params[0].evaluate();
			var power = params[1].evaluate();

			return Math.pow(base, power);
    }
    ,getParamCount: function() {
        return 2;
    }
});

/**
 * Construct a new NegateExpression.
 */
SUGAR.expressions.NegateExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.NegateExpression, SUGAR.expressions.NumericExpression, {
    className: "NegateExpression",
    evaluate: function() {
			return this.context.multiply('-1', this.getParameters().evaluate());
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new StringLengthExpression.
 */
SUGAR.expressions.StringLengthExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.StringLengthExpression, SUGAR.expressions.NumericExpression, {
    className: "StringLengthExpression",
    evaluate: function() {
			var p = this.getParameters().evaluate() + "";
			return p.length;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return 'string';
    }
});

/**
 * Construct a new SentimentScoreToStringExpression.
 */
SUGAR.expressions.SentimentScoreToStringExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SentimentScoreToStringExpression, SUGAR.expressions.NumericExpression, {
    className: "SentimentScoreToStringExpression",
    evaluate: function() {
			var score = this.getParameters().evaluate();
			if (score > 1.3) {
                return 'Positive';
            } else if (score < -1.3) {
                return 'Negative'
            } else {
                return 'Neutral'
            }
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new ValueOfExpression.
 */
SUGAR.expressions.ValueOfExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ValueOfExpression, SUGAR.expressions.NumericExpression, {
    className: "ValueOfExpression",
    evaluate: function() {
			var val = this.getParameters().evaluate() + "";
			val = val.replace(/,/g, "");
			var out = 0;
			if (val.indexOf(".") != -1)
				out = parseFloat(val);
			else
			    out = parseInt(val);
			if (isNaN(out))
			   throw "Error: '" + val + "' is not a number";

			return out;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return 'generic';
    }
});

/**
 * Construct a new CountConditionalRelatedExpression.
 */
SUGAR.expressions.CountConditionalRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.CountConditionalRelatedExpression, SUGAR.expressions.NumericExpression, {
    className: "CountConditionalRelatedExpression",
    evaluate: function() {

        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }

        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            condition_field = params[1].evaluate(),
            condition_values = params[2].evaluate();

        //_.contains expects this to be an array, so convert it if it isn't already.
        if (!_.isArray(condition_values)) {
            condition_values = [condition_values];
        }

        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship);
        // has the model been removed from it's collection
        var hasModelBeenRemoved = this.context.isRemoveEvent || false;

        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'countConditional',
                target,
                model,
                (hasModelBeenRemoved) ? 'remove' : 'add'
            );
        }

        // get the updated values array/object and get the size of it, which will be the correct count
        var rollup_value = _.size(this.context.getRelatedCollectionValues(this.context.model, relationship, 'countConditional', target));

        // rollup_value won't exist if we didn't do any math, so just ignore this
        if (!_.isUndefined(rollup_value) && _.isFinite(rollup_value)) {
            // update the model
            this.context.model.set(target, rollup_value);
            // update the relationship defs on the model
            this.context.updateRelatedFieldValue(
                relationship,
                'countConditional',
                target,
                rollup_value,
                this.context.model.isNew()
            );
        }
    }
    ,getParamCount: function() {
        return 3;
    }
    ,getParameterTypes: function() {
        return ['relate','string','generic'];
                }
});

/**
 * Construct a new CountRelatedExpression.
 */
SUGAR.expressions.CountRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.CountRelatedExpression, SUGAR.expressions.NumericExpression, {
    className: "CountRelatedExpression",
    evaluate: function() {

        var linkField = this.getParameters().evaluate();
        // if App is undefined, then we should still use what was there since it works in BWC mode.
        if (App === undefined) {

            if (typeof(linkField) == 'string' && linkField != '') {
                return this.context.getRelatedField(linkField, 'count');
            }

            return '';
        }

        // just the the length of the collection for the given linkField
        var target = this.context.target,
            current_value = 0,
            relatedColl = this.context.model.getRelatedCollection(linkField);

        // if the model is new, it will never have the dataFetched flag set on it, so we should always use what
        // was returned by the call above.
        if (relatedColl.dataFetched || this.context.model.isNew()) {
            current_value = relatedColl.length;
        } else if (this.context.model.has(linkField)) {
            // check the case where related data still hasn't been loaded
            // but we made a call to ExpressionEngine earlier
            var link = this.context.model.get(linkField);
            if (link.count) {
                current_value = link.count;
            // otherwise, check if target field is set in context model,
            // which may be called by a processes dashlet
            } else if (this.context.model.has(target)) {
                current_value = this.context.model.get(target);
            }
        }

        this.context.model.set(target, current_value);
        // update the relationship defs on the model
        this.context.updateRelatedFieldValue(
            linkField,
            'count',
            '',
            current_value,
            this.context.model.isNew()
        );
        return current_value;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['relate'];
                }
});

/**
 * Construct a new SumConditionalRelatedExpression.
 */
SUGAR.expressions.SumConditionalRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SumConditionalRelatedExpression, SUGAR.expressions.NumericExpression, {
    className: "SumConditionalRelatedExpression",
    evaluate: function() {

        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }

        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            rel_field = params[1].evaluate(),
            condition_field = params[2].evaluate(),

            //_.contains expects this to be an array, so convert it if it isn't already.
            condition_values = params[3].evaluate();
            if (!_.isArray(condition_values)) {
                condition_values = [condition_values];
            }

        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship);
        var precision = model.fields[rel_field].precision || 6;
        // has the model been removed from it's collection
        var hasModelBeenRemoved = this.context.isRemoveEvent || false;
        // did the condition field change at some point?
        var conditionChanged = _.has(model.changed, condition_field);
        
        var isCurrency = (model.fields[rel_field].type === 'currency');
        var current_value = this.context.getRelatedField(relationship, 'rollupConditionalSum', rel_field) || '0';
        var related_collection = this.context.model.getRelatedCollection(relationship);
        var rollup_value = '0';
        
        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'rollupConditionalSum',
                rel_field,
                model,
                (hasModelBeenRemoved)? 'remove' : 'add'
            );
        }

        var all_values = this.context.getRelatedCollectionValues(this.context.model, relationship, 'rollupConditionalSum', rel_field) || {};

        if (_.size(all_values) > 0) {
            rollup_value = _.reduce(all_values, function(memo, number, key) {
                // Check the condition against the live model value
                // or assume the model is valid if the server included it.
                var rel_model = related_collection.get(key);
                if (!rel_model || _.contains(condition_values, rel_model.get(condition_field))) {
                    return App.math.add(memo, number, precision, true);
                }

                return memo;
            }, '0');

            if (isCurrency) {
                rollup_value = App.currency.convertFromBase(
                    rollup_value,
                    this.context.model.get('currency_id')
                );
            }
        }

        if (!_.isEqual(rollup_value, current_value)) {
            this.context.model.set(target, rollup_value);
            this.context.updateRelatedFieldValue(
                relationship,
                'rollupConditionalSum',
                rel_field,
                rollup_value,
                this.context.model.isNew()
            );
        }

        return rollup_value;
        
    }
    ,getParamCount: function() {
        return 4;
    }
    ,getParameterTypes: function() {
        return ['relate','string','string','generic'];
                }
});

/**
 * Construct a new StandardDeviationExpression.
 */
SUGAR.expressions.StandardDeviationExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.StandardDeviationExpression, SUGAR.expressions.NumericExpression, {
    className: "StandardDeviationExpression",
    evaluate: function() {
			var params = this.getParameters();
			var values = new Array();

			// find the mean
			var sum   = 0;
			var count = params.length;
			for (var i = 0; i < params.length; i++) {
				value = params[i].evaluate();
				values[values.length] = value;
				sum = this.context.add(sum, value);
			}
			var mean = this.context.divide(sum, count);

			// find the summation of deviations
			var deviation_sum = 0;
			for ( var i = 0; i < values.length; i++ )
				deviation_sum += Math.pow(this.context.subtract(values[i], mean), 2);

			// find the std dev
			var variance = this.context.multiply(this.context.divide(1, count), deviation_sum);

			return Math.sqrt(variance);
    }
});

/**
 * Construct a new MaximumExpression.
 */
SUGAR.expressions.MaximumExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MaximumExpression, SUGAR.expressions.NumericExpression, {
    className: "MaximumExpression",
    evaluate: function() {
			var params = this.getParameters();
			var max = null;
			for (var i = 0; i < params.length; i++) {
				var val = 	params[i].evaluate();
				if(max == null || val > max)
					max = val;
			}
			return max;
    }
});

/**
 * Construct a new IndexOfExpression.
 */
SUGAR.expressions.IndexOfExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IndexOfExpression, SUGAR.expressions.NumericExpression, {
    className: "IndexOfExpression",
    evaluate: function() {
			var params = this.getParameters();
			var arr  = params[1].evaluate();
			var val  = params[0].evaluate();

			for (var i=0; i < arr.length; i++) {
			if (arr[i] == val) {
				return i;
			}
		}
		return -1;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['generic','enum'];
                }
});

/**
 * Construct a new CeilingExpression.
 */
SUGAR.expressions.CeilingExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.CeilingExpression, SUGAR.expressions.NumericExpression, {
    className: "CeilingExpression",
    evaluate: function() {
			return Math.ceil(this.getParameters().evaluate());
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new ProrateValueExpression.
 */
SUGAR.expressions.ProrateValueExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ProrateValueExpression, SUGAR.expressions.NumericExpression, {
    className: "ProrateValueExpression",
    evaluate: function() {
            // The list of valid units of duration, ordered from smallest to largest
            var validDurationUnits = ['day', 'month', 'year'];

            // Get the values of the parameters passed in
			var params = this.getParameters();
			var baseValue = params[0].evaluate();
			var numeratorValue = params[1].evaluate();
			var numeratorUnit = params[2].evaluate();
			var denominatorValue = params[3].evaluate();
			var denominatorUnit = params[4].evaluate();
			
			// Checks that the duration units used in the formula are valid
			var validateDurationUnits = function(units) {
			    _.each(units, function(unit) {
			        if (!_.contains(validDurationUnits, unit)) {
			            throw ('prorateValue: Attempt to prorate value with invalid duration unit "' + unit + '"');
			        }
			    });
			}
			
			// We can't prorate unless we know what units to convert between
			validateDurationUnits([numeratorUnit, denominatorUnit]);

            // Compares two duration units to find the lowest common unit between them
			var getSmallestCommonUnit = function(unit1, unit2) {
			    return _.find(validDurationUnits, function(validDurationUnit) {
			        return (_.contains([unit1, unit2], validDurationUnit));
			    }) || '';
			}

			// Converts a value from one duration unit to another
			var convertValue = function(value, fromUnit, toUnit) {
			    if (fromUnit === 'year' && toUnit === 'month') {
			        return value * 12;
			    } else if (fromUnit === 'month' && toUnit === 'year') {
			        return value / 12;
			    } else if (fromUnit === 'year' && toUnit === 'day') {
			        return value * 365;
			    } else if (fromUnit === 'day' && toUnit === 'year') {
			        return value / 365;
			    } else if (fromUnit === 'month' && toUnit === 'day') {
			        return value * (365/12);
			    } else if (fromUnit === 'day' && toUnit === 'month') {
			        return value / (365/12);
			    } else {
			        // Either the units are the same or we don't support this conversion
			        return value;
			    }
			}

            // Convert the numerator and denominator values to use a common unit
            var commonUnit = getSmallestCommonUnit(numeratorUnit, denominatorUnit);
            var numeratorValue = convertValue(numeratorValue, numeratorUnit, commonUnit);
            var denominatorValue = convertValue(denominatorValue, denominatorUnit, commonUnit);

            // Multiply the value by the numerator, then divide by the denominator
            return this.context.divide(this.context.multiply(baseValue, numeratorValue), denominatorValue);
    }
    ,getParamCount: function() {
        return 5;
    }
    ,getParameterTypes: function() {
        return ['number','number','string','number','string'];
                }
});

/**
 * Construct a new MaxRelatedExpression.
 */
SUGAR.expressions.MaxRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MaxRelatedExpression, SUGAR.expressions.NumericExpression, {
    className: "MaxRelatedExpression",
    evaluate: function() {
        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }
        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            rel_field = params[1].evaluate();
        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship),
            model_id = model.id || model.cid,
            // has the model been removed from it's collection
            isCurrency = (model.fields[rel_field].type === 'currency'),
            sortByDesc = function (lhs, rhs) { return parseFloat(lhs) < parseFloat(rhs) ? 1 : parseFloat(lhs) > parseFloat(rhs) ? -1 : 0; },
            hasModelBeenRemoved = this.context.isRemoveEvent || false,
            current_value = this.context.getRelatedField(relationship, 'rollupMax', rel_field) || '',
            finite_values = {},
            rollup_value = '0';
            
        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'rollupMax',
                rel_field,
                model,
                (hasModelBeenRemoved ? 'remove' : 'add')
            );
        }

        var all_values = this.context.getRelatedCollectionValues(this.context.model, relationship, 'rollupMax', rel_field) || {};

        if (_.size(all_values) > 0) {
            finite_values = _.map(_.values(all_values), _.partial(parseInt, _, 10));
            finite_values = _.filter(finite_values, _.isFinite);

            // get all the values and sort them so the highest is on top
            rollup_value = finite_values.sort(sortByDesc)[0];

            if (isCurrency) {
                rollup_value = App.currency.convertFromBase(
                    rollup_value,
                    this.context.model.get('currency_id')
                );
            }
        }

        if (!_.isEqual(rollup_value, current_value)) {
            this.context.model.set(target, rollup_value);
            this.context.updateRelatedFieldValue(
                relationship,
                'rollupMax',
                rel_field,
                rollup_value,
                this.context.model.isNew()
            );
        }

        return rollup_value;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['relate','string'];
                }
});

/**
 * Construct a new NaturalLogExpression.
 */
SUGAR.expressions.NaturalLogExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.NaturalLogExpression, SUGAR.expressions.NumericExpression, {
    className: "NaturalLogExpression",
    evaluate: function() {

            return Math.log( this.getParameters().evaluate() );
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new SumRelatedExpression.
 */
SUGAR.expressions.SumRelatedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SumRelatedExpression, SUGAR.expressions.NumericExpression, {
    className: "SumRelatedExpression",
    evaluate: function() {
        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }
        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            rel_field = params[1].evaluate(),
            parent_type = this.context.model.fields[target].type,
            parent_precision = this.context.model.fields[target].precision;
        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship),
            // has the model been removed from it's collection
            isCurrency = (model.fields[rel_field].type === 'currency'),
            child_precision = model.fields[rel_field].precision || 6,
            child_type = model.fields[rel_field].type,
            hasModelBeenRemoved = this.context.isRemoveEvent || false,
            current_value = this.context.model.attributes[target] || '',
            rollup_value = '0';

        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'rollupSum',
                rel_field,
                model,
                (hasModelBeenRemoved ? 'remove' : 'add')
            );
        }

        var all_values = this.context.getRelatedCollectionValues(this.context.model, relationship, 'rollupSum', rel_field) || {};

        if (_.size(all_values) > 0) {
            rollup_value = _.reduce(all_values, function(memo, number) {
                return App.math.add(memo, number, child_precision, true);
            }, '0');

            if (parent_precision < child_precision) {
                rollup_value = App.math.round(rollup_value, parent_precision, true);
            }

            if (isCurrency) {
                rollup_value = App.currency.convertFromBase(
                    rollup_value,
                    this.context.model.get('currency_id')
                );
            }
        }

        // Needed to truncate app.math return value if child field is type int
        if (child_type === 'int') {
            rollup_value = rollup_value.split('.')[0];
        }

        // Needed to convert rollup value back to parent model type if parent model type is numeric
        if (parent_type === 'int' || parent_type === 'float' || parent_type === 'decimal') {
            rollup_value = App.utils.convertNumericType(rollup_value, parent_type);
        }

        if (!_.isEqual(rollup_value, current_value)) {
            this.context.model.set(target, rollup_value);
            this.context.updateRelatedFieldValue(
                relationship,
                'rollupSum',
                rel_field,
                rollup_value,
                this.context.model.isNew()
            );
        }

        return rollup_value;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['relate','string'];
                }
});

/**
 * Construct a new MultiplyExpression.
 */
SUGAR.expressions.MultiplyExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.MultiplyExpression, SUGAR.expressions.NumericExpression, {
    className: "MultiplyExpression",
    evaluate: function() {
			var params = this.getParameters(),
			product = '1';
			for (var i = 0; i < params.length; i++) {
                product = this.context.multiply(product, params[i].evaluate());
            }
			return product;
    }
});

/**
 * Construct a new IsValidPhoneExpression.
 */
SUGAR.expressions.IsValidPhoneExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsValidPhoneExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsValidPhoneExpression",
    evaluate: function() {
		var phoneStr = this.getParameters().evaluate();
		if(phoneStr.length== 0) 	return SUGAR.expressions.Expression.TRUE;
		if( ! /^\+?[0-9\-\(\)\s]+$/.test(phoneStr) )
			return SUGAR.expressions.Expression.FALSE;
		return SUGAR.expressions.Expression.TRUE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new IsValidDBNameExpression.
 */
SUGAR.expressions.IsValidDBNameExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsValidDBNameExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsValidDBNameExpression",
    evaluate: function() {
		var str = this.getParameters().evaluate();
		if(str.length== 0) {
			return true;
		}
		// must start with a letter
		if(!/^[a-zA-Z][a-zA-Z\_0-9]+$/.test(str))
			return SUGAR.expressions.Expression.FALSE;
		return SUGAR.expressions.Expression.TRUE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new NotExpression.
 */
SUGAR.expressions.NotExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.NotExpression, SUGAR.expressions.BooleanExpression, {
    className: "NotExpression",
    evaluate: function() {
			if ( this.getParameters().evaluate() == SUGAR.expressions.Expression.FALSE )
				return SUGAR.expressions.Expression.TRUE;
			else
				return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new IsForecastClosedExpression.
 */
SUGAR.expressions.IsForecastClosedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsForecastClosedExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsForecastClosedExpression",
    evaluate: function() {
			var value = this.getParameters().evaluate();

			// this doesn't support BWC modules, so it should return false if it doesn't have app.
			// we can't use undersore as it's not in BWC mode here
			if (App === undefined) {
		        return SUGAR.expressions.Expression.FALSE;
			}

			var config = App.metadata.getModule('Forecasts', 'config'),
			    status = ['Closed Won', 'Closed Lost'];
            if (!_.isUndefined(config)) {
			    status = _.union(
                    config.sales_stage_won,
                    config.sales_stage_lost
                );
            }

            if (status.indexOf(value) === -1) {
                return SUGAR.expressions.Expression.FALSE
            }

			return SUGAR.expressions.Expression.TRUE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new isBeforeExpression.
 */
SUGAR.expressions.isBeforeExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.isBeforeExpression, SUGAR.expressions.BooleanExpression, {
    className: "isBeforeExpression",
    evaluate: function() {
			var params = this.getParameters();
			var a = params[0].evaluate();
			var b = params[1].evaluate();

            if (!a || !b || isNaN(a) || isNaN(b)) {
                return SUGAR.expressions.Expression.FALSE;
            }

			if ( a < b )	return SUGAR.expressions.Expression.TRUE;
			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['date','date'];
                }
});

/**
 * Construct a new IsValidTimeExpression.
 */
SUGAR.expressions.IsValidTimeExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsValidTimeExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsValidTimeExpression",
    evaluate: function() {
		var timeStr = this.getParameters().evaluate();
		var time_reg_format = /^(\d{1,2}):(\d\d)\s*([ap]m)?$/i;
		if (timeStr.length == 0)	return SUGAR.expressions.Expression.TRUE;
		myregexp = new RegExp(time_reg_format)
		if(!myregexp.test(timeStr))	return SUGAR.expressions.Expression.FALSE;
		var matches = timeStr.match(time_reg_format);
		if (matches[1] > 23 || matches[2] > 59){return SUGAR.expressions.Expression.FALSE;}
		if (matches[3] && (matches[1] > 12 || matches[1] == 0)){return SUGAR.expressions.Expression.FALSE;}
		return SUGAR.expressions.Expression.TRUE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new IsAlphaNumericExpression.
 */
SUGAR.expressions.IsAlphaNumericExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsAlphaNumericExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsAlphaNumericExpression",
    evaluate: function() {
			var params = this.getParameters().evaluate();
			if ( /^[a-zA-Z0-9]+$/.test(params) )	return SUGAR.expressions.Expression.TRUE;
			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new GreaterThanExpression.
 */
SUGAR.expressions.GreaterThanExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.GreaterThanExpression, SUGAR.expressions.BooleanExpression, {
    className: "GreaterThanExpression",
    evaluate: function() {
			var params = this.getParameters();
			var a = params[0].evaluate();
			var b = params[1].evaluate();
			if ( a > b )	return SUGAR.expressions.Expression.TRUE;
			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['number','number'];
                }
});

/**
 * Construct a new EqualExpression.
 */
SUGAR.expressions.EqualExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.EqualExpression, SUGAR.expressions.BooleanExpression, {
    className: "EqualExpression",
    evaluate: function() {
            var SEE = SUGAR.expressions.Expression,
                params = this.getParameters(),
                a = params[0].evaluate(),
                b = params[1].evaluate(),
                hasBool = params[0] instanceof SUGAR.expressions.BooleanExpression ||
                    params[1] instanceof SUGAR.expressions.BooleanExpression;

            if ( a == b  || (hasBool && ((SEE.isTruthy(a) && SEE.isTruthy(b)) || (!SEE.isTruthy(a) && !SEE.isTruthy(b))))) {
               return SEE.TRUE;
            }
            return SEE.FALSE;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['generic','generic'];
                }
});

/**
 * Construct a new IsOwnerExpression.
 */
SUGAR.expressions.IsOwnerExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsOwnerExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsOwnerExpression",
    evaluate: function() {
            // this doesn't support BWC modules
            if (App === undefined) {
                return SUGAR.expressions.Expression.FALSE;
            }

            if (this.context.model &&
                this.context.model.get('assigned_user_id') === App.user.id) {
                return SUGAR.expressions.Expression.TRUE
            }

            return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 0;
    }
});

/**
 * Construct a new IsForecastClosedLostExpression.
 */
SUGAR.expressions.IsForecastClosedLostExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsForecastClosedLostExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsForecastClosedLostExpression",
    evaluate: function() {
			var value = this.getParameters().evaluate();

			// this doesn't support BWC modules, so it should return false if it doesn't have app.
			// we can't use underscore as it's not in BWC mode here
			if (App === undefined) {
		        return SUGAR.expressions.Expression.FALSE;
			}

			var config = App.metadata.getModule('Forecasts', 'config') || {},
			    status = config.sales_stage_lost || ['Closed Lost'];

            if (status.indexOf(value) === -1) {
                return SUGAR.expressions.Expression.FALSE
            }

			return SUGAR.expressions.Expression.TRUE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new IsInRangeExpression.
 */
SUGAR.expressions.IsInRangeExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsInRangeExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsInRangeExpression",
    evaluate: function() {
			var params = this.getParameters();
			var number = params[0].evaluate();
			var min    = params[1].evaluate();
			var max    = params[2].evaluate();

			if ( number >= min && number <= max )
				return SUGAR.expressions.Expression.TRUE;

			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 3;
    }
    ,getParameterTypes: function() {
        return ['number','number','number'];
                }
});

/**
 * Construct a new IsRequiredCollectionExpression.
 */
SUGAR.expressions.IsRequiredCollectionExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsRequiredCollectionExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsRequiredCollectionExpression",
    evaluate: function() {
			var params = this.getParameters().evaluate();
            table = document.getElementById(params);
            children = YAHOO.util.Dom.getElementsByClassName('sqsEnabled', 'input', table);
            for(id in children) {
                if(trim(children[id].value) != '') {
                   return SUGAR.expressions.Expression.TRUE;
                }
            }
			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new isAfterExpression.
 */
SUGAR.expressions.isAfterExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.isAfterExpression, SUGAR.expressions.BooleanExpression, {
    className: "isAfterExpression",
    evaluate: function() {
			var params = this.getParameters();
			var a = SUGAR.util.DateUtils.parse(params[0].evaluate());
			var b = SUGAR.util.DateUtils.parse(params[1].evaluate());

            if (!a || !b || isNaN(a) || isNaN(b)) {
                return SUGAR.expressions.Expression.FALSE;
            }

			if ( a > b )	return SUGAR.expressions.Expression.TRUE;
			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['date','date'];
                }
});

/**
 * Construct a new IsAssignedExpression.
 */
SUGAR.expressions.IsAssignedExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsAssignedExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsAssignedExpression",
    evaluate: function() {
            // this doesn't support BWC modules
            if (App === undefined) {
                return SUGAR.expressions.Expression.FALSE;
            }

            if (this.context.model &&
                this.context.model.get('assigned_user_id')) {
                return SUGAR.expressions.Expression.TRUE
            }

            return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 0;
    }
});

/**
 * Construct a new IsValidDateExpression.
 */
SUGAR.expressions.IsValidDateExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsValidDateExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsValidDateExpression",
    evaluate: function() {
		var dtStr = this.getParameters().evaluate();
        if (typeof dtStr != "string" || dtStr == "") return SUGAR.expressions.Expression.FALSE;
        var format = "Y-m-d";
        if (SUGAR.expressions.userPrefs)
            format = SUGAR.expressions.userPrefs.datef;
        var date = SUGAR.util.DateUtils.parse(dtStr, format);
        if(date != false && date != "Invalid Date")
		    return SUGAR.expressions.Expression.TRUE;
		return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new IsForecastClosedWonExpression.
 */
SUGAR.expressions.IsForecastClosedWonExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsForecastClosedWonExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsForecastClosedWonExpression",
    evaluate: function() {
			var value = this.getParameters().evaluate();

			// this doesn't support BWC modules, so it should return false if it doesn't have app.
			// we can't use undersore as it's not in BWC mode here
			if (App === undefined) {
		        return SUGAR.expressions.Expression.FALSE;
			}

			var config = App.metadata.getModule('Forecasts', 'config') || {},
			    status = config.sales_stage_won || ['Closed Won'];

            if (status.indexOf(value) === -1) {
                return SUGAR.expressions.Expression.FALSE
            }

			return SUGAR.expressions.Expression.TRUE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new IsAlphaExpression.
 */
SUGAR.expressions.IsAlphaExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsAlphaExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsAlphaExpression",
    evaluate: function() {
			var params = this.getParameters().evaluate();
			if ( /^[a-zA-Z]+$/.test(params) )	return SUGAR.expressions.Expression.TRUE;
			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new IsNumericExpression.
 */
SUGAR.expressions.IsNumericExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsNumericExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsNumericExpression",
    evaluate: function() {
            var params = this.getParameters().evaluate();
            if (params === '' || params === null) {
                return SUGAR.expressions.Expression.FALSE
            }
            if (isFinite(params) && !isNaN(parseFloat(params))) {
                return SUGAR.expressions.Expression.TRUE;
            }

            return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new OrExpression.
 */
SUGAR.expressions.OrExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.OrExpression, SUGAR.expressions.BooleanExpression, {
    className: "OrExpression",
    evaluate: function() {
			var params = this.getParameters();		
			for ( var i = 0; i < params.length; i++ ) {
				if ( params[i].evaluate() == SUGAR.expressions.Expression.TRUE )
					return SUGAR.expressions.Expression.TRUE;
			}
			return SUGAR.expressions.Expression.FALSE;
    }
});

/**
 * Construct a new IsValidEmailExpression.
 */
SUGAR.expressions.IsValidEmailExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsValidEmailExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsValidEmailExpression",
    evaluate: function() {
		var emailStr = this.getParameters().evaluate();
		
		if ( typeof emailStr != "string" ) return SUGAR.expressions.Expression.FALSE;

		if ( emailStr == "" ) return SUGAR.expressions.Expression.TRUE;
		
		var lastChar = emailStr.charAt(emailStr.length - 1);
		if ( !lastChar.match(/[^\.]/i) )	return SUGAR.expressions.Expression.FALSE;

		// validate it
		var emailArr = emailStr.split(/[,;]/);		// if multiple e-mail addresses
		for (var i = 0; i < emailArr.length; i++) {
			var emailAddress = emailArr[i];
			emailAddress = emailAddress.replace(/^\s+|\s+$/g,"");
            if (emailAddress != '' &&
                !/^(([^<>()\[\]\.,;:\s@"]+(\.[^<>()\[\]\.,;:\s@"]+)*)|(".+"))@\S+$/.test(emailAddress)
            ) {
                return SUGAR.expressions.Expression.FALSE;
            }
		}

		return SUGAR.expressions.Expression.TRUE;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new BinaryDependencyExpression.
 */
SUGAR.expressions.BinaryDependencyExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.BinaryDependencyExpression, SUGAR.expressions.BooleanExpression, {
    className: "BinaryDependencyExpression",
    evaluate: function() {
			var params = this.getParameters();
			var a = params[0].evaluate();
			var b = params[1].evaluate();
			if ( a != null && b != null && a != '' && b != '' )
				return SUGAR.expressions.Expression.TRUE;
			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['string','string'];
                }
});

/**
 * Construct a new AndExpression.
 */
SUGAR.expressions.AndExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.AndExpression, SUGAR.expressions.BooleanExpression, {
    className: "AndExpression",
    evaluate: function() {
			var params = this.getParameters();
            if(!(params instanceof Array)) params = [params];
			for ( var i = 0; i < params.length; i++ ) {
				if ( params[i].evaluate() != SUGAR.expressions.Expression.TRUE )
					return SUGAR.expressions.Expression.FALSE;
			}
			return SUGAR.expressions.Expression.TRUE;
    }
});

/**
 * Construct a new IsInEnumExpression.
 */
SUGAR.expressions.IsInEnumExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IsInEnumExpression, SUGAR.expressions.BooleanExpression, {
    className: "IsInEnumExpression",
    evaluate: function() {
			var params = this.getParameters();
			var haystack = params[1].evaluate();
			var needle   = params[0].evaluate();

			for ( var i = 0 ; i < haystack.length ; i++ ) {
				var value = haystack[i] instanceof SUGAR.expressions.Expression ? haystack[i].evaluate() : haystack[i];
				if ( value == needle ) {
					return SUGAR.expressions.Expression.TRUE;
				}
				if ( Array.isArray(value) && _.contains(value, needle) ) {
					return SUGAR.expressions.Expression.TRUE;
				}
			}

			return SUGAR.expressions.Expression.FALSE;
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['generic','enum'];
                }
});

/**
 * Construct a new RelatedFieldExpression.
 */
SUGAR.expressions.RelatedFieldExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.RelatedFieldExpression, SUGAR.expressions.GenericExpression, {
    className: "RelatedFieldExpression",
    evaluate: function() {
		    var params = this.getParameters(),
			    linkField = params[0].evaluate(),
			    relField = params[1].evaluate();

			if (typeof(linkField) == "string" && linkField != "")
			{
                return this.context.getRelatedField(linkField, 'related', relField);
			} else if (typeof(rel) == "object") {
			    //Assume we have a Link object that we can delve into.
			    //This is mostly used for n level dives through relationships.
			    //This should probably be avoided on edit views due to performance issues.
			}

			return "";
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['relate','string'];
                }
});

/**
 * Construct a new SugarFieldExpression.
 */
SUGAR.expressions.SugarFieldExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SugarFieldExpression, SUGAR.expressions.GenericExpression, {
    className: "SugarFieldExpression",
    evaluate: function() {
		    var varName = this.getParameters().evaluate();
			return SUGAR.forms.AssignmentHandler.getValue(varName);
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new CurrentUserFieldExpression.
 */
SUGAR.expressions.CurrentUserFieldExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.CurrentUserFieldExpression, SUGAR.expressions.GenericExpression, {
    className: "CurrentUserFieldExpression",
    evaluate: function() {
            // We can't do this on BWC views since we don't have the user data available
            if (!App) {
                return SUGAR.expressions.Expression.FALSE;
            }
            
            let formatField = function(context, fieldDef, userFieldName) {
                let value = App.user.get('sugar_logic_fields')[userFieldName];
                switch (fieldDef.type) {
                    case 'date':
                    case 'datetime':
                    case 'datetimecombo':
                        let dateOnly = fieldDef.type === 'date';
                        if (!value) {
                            return '';
                        }
                        return App.date(value).formatUser(dateOnly, App.user);
                    case 'bool':
                        if (value) {
                            return SUGAR.expressions.Expression.TRUE;
                        } else {
                            return SUGAR.expressions.Expression.FALSE;
                        }
                    case 'currency':
                        let contextCurrencyId = context.model.get('currency_id');
                        if (contextCurrencyId) {
                            return App.currency.convertFromBase(value, contextCurrencyId);
                        } else {
                            return value;
                        }
                    default:
                        return value;
                }
            };
            
            let getFieldDef = function(userFieldName) {
                let fieldDefs = App.user.get('sugar_logic_fielddefs');
                return fieldDefs.find(field => field.name === userFieldName);
            };
            
            let userFieldName = this.getParameters().evaluate();
            let fieldDef = getFieldDef(userFieldName);
            
            if (fieldDef) {
                return formatField(this.context, fieldDef, userFieldName);
            } else {
                throw 'currentUserField: Parameter "' + userFieldName + '" is not a valid User field';
            }
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new CurrencyRateExpression.
 */
SUGAR.expressions.CurrencyRateExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.CurrencyRateExpression, SUGAR.expressions.GenericExpression, {
    className: "CurrencyRateExpression",
    evaluate: function() {
			// this doesn't support BWC modules, so it should return false if it doesn't have Apps.
			if (App === undefined) {
		        return SUGAR.expressions.Expression.FALSE;
			}

			var currencyId = this.getParameters().evaluate();
			return App.metadata.getCurrency(currencyId).conversion_rate;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['string'];
                }
});

/**
 * Construct a new ConditionExpression.
 */
SUGAR.expressions.ConditionExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ConditionExpression, SUGAR.expressions.GenericExpression, {
    className: "ConditionExpression",
    evaluate: function() {
            var SEE = SUGAR.expressions.Expression,
                params = this.getParameters(),
                cond = params[0].evaluate();
            if (SEE.isTruthy(cond)) {
                return params[1].evaluate();
            } else {
                return params[2].evaluate();
            }
    }
    ,getParamCount: function() {
        return 3;
    }
    ,getParameterTypes: function() {
        return ['boolean','generic','generic'];
                }
});

/**
 * Construct a new IndexValueExpression.
 */
SUGAR.expressions.IndexValueExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.IndexValueExpression, SUGAR.expressions.GenericExpression, {
    className: "IndexValueExpression",
    evaluate: function() {
			var params = this.getParameters();
			var array  = params[1].evaluate();
			var index  = params[0].evaluate();

			if (typeof index == 'string' && !isNaN(index))
				index = Number(index);

			if ( index >= array.length || index < 0 )
				throw ("value_at: Attempt to access an index out of bounds");

			return array[index];
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['number','enum'];
                }
});

/**
 * Construct a new SubStrExpression.
 */
SUGAR.expressions.SubStrExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SubStrExpression, SUGAR.expressions.StringExpression, {
    className: "SubStrExpression",
    evaluate: function() {
            var params = this.getParameters();
            var str = params[0].evaluate() + "";
            var fromIdx = params[1].evaluate();
            var strLength = params[2].evaluate();
            return str.substr(fromIdx, strLength);
    }
    ,getParamCount: function() {
        return 3;
    }
    ,getParameterTypes: function() {
        return ['string','number','number'];
                }
});

/**
 * Construct a new ConcatenateExpression.
 */
SUGAR.expressions.ConcatenateExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ConcatenateExpression, SUGAR.expressions.StringExpression, {
    className: "ConcatenateExpression",
    evaluate: function() {
			var concat = "";
			var params = this.getParameters() ;
			for ( var i = 0; i < params.length; i++ ) {
				concat += params[i].evaluate();
			}
			return concat;
    }
});

/**
 * Construct a new SugarDropDownValueExpression.
 */
SUGAR.expressions.SugarDropDownValueExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SugarDropDownValueExpression, SUGAR.expressions.StringExpression, {
    className: "SugarDropDownValueExpression",
    evaluate: function() {
		    var params = this.getParameters();
		    var list = params[0].evaluate();
		    var key = params[1].evaluate();
            var arr = this.context.getAppListStrings(list);
            if (arr == "undefined") return "";
            for (var i in arr) {
                if (typeof i == "string" && i == key)
                    return arr[i];
            }
            return "";
    }
    ,getParamCount: function() {
        return 2;
    }
});

/**
 * Construct a new StrReplaceExpression.
 */
SUGAR.expressions.StrReplaceExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.StrReplaceExpression, SUGAR.expressions.StringExpression, {
    className: "StrReplaceExpression",
    evaluate: function() {
            var params = this.getParameters();
            var search = params[0].evaluate();
            var replace = params[1].evaluate();
            var subject = params[2].evaluate();
            var caseSensitivity = params[3].evaluate();
            
            // go through the search and try to regex escape everything
            search = search.replace(/[-\/\^$*+?.()|[\]{}]/g, '\\$&');
            
            var flags = 'g';
            if (caseSensitivity === SUGAR.expressions.Expression.FALSE) {
                flags += 'i';
            }
            var regex = new RegExp(search, flags);
            return subject.replace(regex, replace);
			
    }
    ,getParameterTypes: function() {
        return ['string','string','string','boolean'];
                }
});

/**
 * Construct a new FormatedNameExpression.
 */
SUGAR.expressions.FormatedNameExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.FormatedNameExpression, SUGAR.expressions.StringExpression, {
    className: "FormatedNameExpression",
    evaluate: function() {
var params	= this.getParameters();
var comp = {s:params[0].evaluate(), f:params[1].evaluate(), l:params[2].evaluate(), t:params[3].evaluate()};
var name = '';
for(i=0; i<name_format.length; i++) {
	if(comp[name_format.substr(i,1)] != undefined) {
    	name += comp[name_format.substr(i,1)];
	} else {
		name += name_format.substr(i,1);
	}
}
return name;
    }
    ,getParamCount: function() {
        return 4;
    }
});

/**
 * Construct a new StrToUpperExpression.
 */
SUGAR.expressions.StrToUpperExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.StrToUpperExpression, SUGAR.expressions.StringExpression, {
    className: "StrToUpperExpression",
    evaluate: function() {
			var string = this.getParameters().evaluate() + "" ;
			return string.toUpperCase();
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new StrToLowerExpression.
 */
SUGAR.expressions.StrToLowerExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.StrToLowerExpression, SUGAR.expressions.StringExpression, {
    className: "StrToLowerExpression",
    evaluate: function() {
			var string = this.getParameters().evaluate() + "";
			return string.toLowerCase();
    }
    ,getParamCount: function() {
        return 1;
    }
});

/**
 * Construct a new ContainsExpression.
 */
SUGAR.expressions.ContainsExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ContainsExpression, SUGAR.expressions.BooleanExpression, {
    className: "ContainsExpression",
    evaluate: function() {
			var params	  = this.getParameters();
			var haystack  = params[0].evaluate() + "";
			var needle	  = params[1].evaluate();

			return ( haystack.indexOf(needle) > -1 ? SUGAR.expressions.Expression.TRUE : SUGAR.expressions.Expression.FALSE );
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return 'string';
    }
});

/**
 * Construct a new ForecastCommitStageExpression.
 */
SUGAR.expressions.ForecastCommitStageExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.ForecastCommitStageExpression, SUGAR.expressions.EnumExpression, {
    className: "ForecastCommitStageExpression",
    evaluate: function() {
			var value = this.getParameters().evaluate();

			// this doesn't support BWC modules, so it should return false if it doesn't have app.
			// we can't use underscore as it's not in BWC mode here
			if (App === undefined) {
		        return '';
			}

			var config = App.metadata.getModule('Forecasts', 'config');

            // if forecast is not set, return an empty string
			if (config.forecast_setup === 0) {
                return '';
			}

			var ranges = config[config.forecast_ranges + '_ranges'],
			    stage = '';

            _.find(ranges, function(_range, _index) {
                if (value >= _range.min && value <= _range.max) {
                    stage = _index;
                    return true;
                }
                return false;
            });

            return stage;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['number'];
                }
});

/**
 * Construct a new DefineStringExpression.
 */
SUGAR.expressions.DefineStringExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.DefineStringExpression, SUGAR.expressions.StringExpression, {
    className: "DefineStringExpression",
    evaluate: function() {
        var value = this.getParameters().evaluate(),
            string;
        if (value instanceof Date) {
            var dateOnly = value.type == "date";
            string = App.date(value).formatUser(dateOnly);
        } else {
            string = value + "";
        }
        return string;
    }
    ,getParamCount: function() {
        return 1;
    }
    ,getParameterTypes: function() {
        return ['generic'];
                }
});

/**
 * Construct a new SugarTranslateExpression.
 */
SUGAR.expressions.SugarTranslateExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.SugarTranslateExpression, SUGAR.expressions.StringExpression, {
    className: "SugarTranslateExpression",
    evaluate: function() {
		  var params = this.getParameters();
		  var module = params[1].evaluate();
		  if (module == "")
		      module = "app_strings";
		  var key = params[0].evaluate();
		  return SUGAR.language.get(module, key);
    }
    ,getParamCount: function() {
        return 2;
    }
});

/**
 * Construct a new CharacterAtExpression.
 */
SUGAR.expressions.CharacterAtExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.CharacterAtExpression, SUGAR.expressions.StringExpression, {
    className: "CharacterAtExpression",
    evaluate: function() {
			var params = this.getParameters();
			var str = params[0].evaluate() + "";
			var idx = params[1].evaluate();
			return str.charAt(idx);
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['string','number'];
                }
});

/**
 * Construct a new OpportunitySalesStageExpression.
 */
SUGAR.expressions.OpportunitySalesStageExpression = function(params, context) {
    this.context = context;
    this.init(params);
}
SUGAR.util.extend(SUGAR.expressions.OpportunitySalesStageExpression, SUGAR.expressions.StringExpression, {
    className: "OpportunitySalesStageExpression",
    evaluate: function() {
            if (App === undefined) {
                return '';
            }

            var params = this.getParameters();
            var relationship = params[0].evaluate();
            var relField = params[1].evaluate();
            var config = App.metadata.getModule('Forecasts', 'config');
            var closedWonStatus = ['Closed Won'];
            var closedLostStatus = ['Closed Lost'];
            var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship);
            var ret = '';
            var latestIndex = 0;
            var totalCt = model.collection.length;
            var closedWonCt = 0;
            var closedLostCt = 0;
            var closedCt = 0;
            var index;

            if (config && config.is_setup) {
                closedWonStatus = config.sales_stage_won;
                closedLostStatus = config.sales_stage_lost;
            }
            
            var salesStages = App.lang.getAppListStrings('sales_stage_dom');
            var salesStageArr = [];

            _.each(closedWonStatus, function(stage) {
                salesStages = _.omit(salesStages, stage);
            }, this);
            _.each(closedLostStatus, function(stage) {
                salesStages = _.omit(salesStages, stage);
            }, this);

            salesStageArr = _.keys(salesStages);

            _.each(model.collection.models, function(model) {
                var salesStage = model.get(relField);

                if (_.contains(closedWonStatus, salesStage)) {
                    closedWonCt++;
                    closedCt++;
                } else if (_.contains(closedLostStatus, salesStage)) {
                    closedLostCt++;
                    closedCt++;
                } else {
                    index = salesStageArr.indexOf(salesStage);
                    if (index >= latestIndex) {
                        latestIndex = index;;
                        ret = salesStage;
                    }
                }
            }, this);
            
            if (closedLostCt === totalCt) {
                // if they're all Closed Lost, return a Closed Lost status
                return _.first(closedLostStatus);
            } else if (closedCt === totalCt && closedWonCt) {
                // all items are closed, and there's at least one Closed Won status
                return _.first(closedWonStatus);
            } else {
                return ret;
            }
    }
    ,getParamCount: function() {
        return 2;
    }
    ,getParameterTypes: function() {
        return ['relate','string'];
                }
});


SUGAR.forms.SetRequiredAction = function(variable, expr, label) {
    if (_.isObject(variable)){
        expr = variable.value;
        label = variable.label;
        variable = variable.target;
    }
    this.variable = variable;
    this.expr = expr;
    this.label    = label;
    this._el_lbl  = document.getElementById(this.label);
    if (this._el_lbl)
        this.msg = this._el_lbl.innerText;
}

/**
 * Triggers this dependency to be re-evaluated again.
 */
SUGAR.util.extend(SUGAR.forms.SetRequiredAction, SUGAR.forms.AbstractAction, {

    /**
     * Triggers the required dependencies.
     */
    exec: function(context) {
        if (typeof(context) == 'undefined')
		    context = this.context;

        this.required = this.evalExpression(this.expr, context);
        if (context.view) {
            //We may get triggered before the view has rendered with the full field list.
            //If that occurs wait for the next render to apply.
            if (_.isEmpty(context.view.fields)) {
                context.view.once('render', function(){this.exec(context);}, this);
                return;
            }

            // If the field is not on the view, nothing to do with it
            let field = context.view.getField(this.variable, context.model);
            if (field) {
                // Track on the field that its requiredness was changed by this action
                field.dependencyStates = field.dependencyStates || {};
                field.dependencyStates.required = SUGAR.expressions.Expression.isTruthy(this.required);

                // Set the actual requiredness of the field if it is not hidden via visibility dependency
                if (field.dependencyStates.visible !== false) {
                    context.setFieldRequired(this.variable, this.required);
                }
            }
        } else {
            this.bwcExec(context, this.required);
        }
    },
     bwcExec : function(context, required) {
        var el = SUGAR.forms.AssignmentHandler.getElement(this.variable);
        if ( typeof(SUGAR.forms.FormValidator) != 'undefined' )
            SUGAR.forms.FormValidator.setRequired(el.form.name, el.name, this.required);
        if (this._el_lbl != null && el != null) {
            var p = this._el_lbl,
                els = YAHOO.util.Dom.getElementsBy( function(e) { return e.className == 'required'; }, "span", p),
                reqSpan = false,
                fName = el.name;

            if ( els != null && els[0] != null)
                reqSpan = els[0];

            if ( (this.required == true  || this.required == 'true')) {
                if (!reqSpan) {
                    var node = document.createElement("span");
                    node.innerHTML = "*";
                    node.className = "required";
                    this._el_lbl.appendChild(node);

                    var i = this.findInValidate(context.formName, fName)
                    if (i > -1)
                        validate[context.formName][i][2] = true;
                    else
                        addToValidate(context.formName, fName, 'text', true, this.msg);
                }
            } else {
                if ( p != null  && reqSpan != false) {
                    reqSpan.parentNode.removeChild(reqSpan);
                }
                var i = this.findInValidate(context.formName, fName)
                if (i > -1)
                    validate[context.formName][i][2] = false;
            }
        }
     },
     findInValidate : function(form, field) {
         if (validate && validate[form]){
             for (var i in validate[form]){
                if (typeof(validate[form][i]) == 'object' && validate[form][i][0] == field)
                    return i;
             }
         }
         return -1;
     }
});
/**
 * A style dependency is an object representation of a style change.
 */
SUGAR.forms.StyleAction = function(target, attrs)
{
    this.target = target;
    this.attrs  = attrs;
}

/**
 * Triggers this dependency to be re-evaluated again.
 */
SUGAR.util.extend(SUGAR.forms.StyleAction, SUGAR.forms.AbstractAction, {

    /**
     * Triggers the style dependencies.
     */
    exec: function(context)
    {

        //If we are running in sidecar, this action will not function
        if(SUGAR.App) return;

        if (typeof(context) == 'undefined')
            context = this.context;
        try {
            // a temp attributes array containing the evaluated version
            // of the original attributes array
            var temp = {};

            // evaluate the attrs, if needed
            for (var i in this.attrs)
            {
                temp[i] = this.evalExpression(this.attrs[i], context);
            }
            context.setStyle(this.target, temp);
        } catch (e) {return;}
    }
});
        var App = App || null;
        SUGAR.forms.SetVisibilityAction = function(target, expr, view)
        {
            this.afterRender = true;
            if (_.isObject(target)) {
                expr = target.value;
                target = target.target
            }
            this.target = target;
            this.expr    = 'cond(' + expr + ', "", "none")';
            this.view = view;

            if (!SUGAR.forms.SetVisibilityAction.initialized) {
                var head = document.getElementsByTagName('head')[0];
                var cssdef = 'span.vis_action_hidden, .vis_action_hidden * { display: none; }'
                var newStyle = document.createElement('style');
                newStyle.setAttribute('type', 'text/css');
                if (newStyle.styleSheet)
                    newStyle.styleSheet.cssText = cssdef;
                else
                    newStyle.innerHTML = cssdef;
                head.appendChild(newStyle);
                SUGAR.forms.SetVisibilityAction.initialized = true;
            }
        }
        
        /**
         * Triggers this dependency to be re-evaluated again.
         */
        SUGAR.util.extend(SUGAR.forms.SetVisibilityAction, SUGAR.forms.AbstractAction, {
        
            /**
             * Triggers the style dependencies.
             */
            exec: function(context) {
                if (typeof(context) == 'undefined')
                    context = this.context;
                try {
                    var target = context && context.getElement && context.getElement(this.target) || null;
                    if (target == null) {
                        return;
                    }
                    var exp = this.evalExpression(this.expr, context);
                    var hide =  exp == 'none' || exp == 'hidden';
                    if (SUGAR.App) {
                        this.sidecarExec(context, target, hide);
                    }
                    else {
                        this.legacyExec(context, target, hide);
                    }
                } catch (e) {
                    if (console && console.log) console.log(e);
                }
            },
            sidecarExec : function(context, target, hide) {
                var inv_class = 'vis_action_hidden';
                var panel_class = 'record-panel-content';
                var element = $(target);
                var wasHidden = element.hasClass(inv_class);
                var row = element.parents('.row-fluid')[0];

                // If the field is not on the view, nothing to do with it
                let field = context.view.getField(this.target, context.model);
                if (field) {
                    // Track on the field that its visibility was changed by this action
                    field.dependencyStates = field.dependencyStates || {};
                    field.dependencyStates.visible = !hide;

                    if (hide) {
                        // Hide the field
                        context.addClass(this.target, inv_class, true);

                        // Store the current requiredness of the field in
                        // dependencyStates, and make the field not required
                        field.dependencyStates.required = field.dependencyStates.required ?? !!field.def.required;
                        context.setFieldRequired(this.target, false);

                        // Disable the field to prevent tabbing into the edit mode of the field
                        context.setFieldDisabled(this.target, true);
                    } else {
                        // Show the field
                        context.removeClass(this.target, inv_class, true);

                        // Enforce any field requiredness set by SetRequiredAction
                        if (!_.isUndefined(field.dependencyStates.required)) {
                            context.setFieldRequired(this.target, field.dependencyStates.required);
                        }

                        // Set field editability appropriately
                        var isEditable = !(field.def.calculated && field.def.enforced) && !field.def.readOnlyProp;
                        context.setFieldDisabled(this.target, !isEditable);

                        // Show the field flash if the field is newly visible
                        if (wasHidden) {
                            SUGAR.forms.FlashField(target, null, this.target);
                        }
                    }
                }

                // Check the row and/or panel that the field is in. If all fields
                // on the row/panel are hidden, hide the whole row/panel
                if (row) {
                    this.checkRowSidecar(row, inv_class);
                    this.checkPanelSidecar(row.parentElement, inv_class, panel_class);
                }
            },
            legacyExec : function(context, target, hide) {
                var Dom = YAHOO.util.Dom,
                    inv_class = 'vis_action_hidden',
                    inputTD = Dom.getAncestorByTagName(target, 'TD'),
                    labelTD = Dom.getPreviousSiblingBy(inputTD, function(e){
                        return e.tagName == 'TD'
                    });
                this.wrapContent(labelTD);
                this.wrapContent(inputTD);
                var wasHidden = Dom.hasClass(labelTD, inv_class);
                if (hide) {
                    Dom.addClass(labelTD, inv_class);
                    Dom.addClass(inputTD, inv_class);
                }
                else {
                    Dom.removeClass(labelTD, inv_class);
                    Dom.removeClass(inputTD, inv_class);
                    if (wasHidden && this.view == 'EditView') {
                        SUGAR.forms.FlashField(target);
                    }
                }
                this.checkRow(Dom.getAncestorByTagName(inputTD, 'TR'), inv_class);
            },
            //we need to wrap plain text nodes in a span in order to hide the contents without hiding the TD itesef
            wrapContent: function(el) {
                if (el && this.containsPlainText(el))
                {
                    var span = document.createElement('SPAN');
                    var nodes = [];
                    for(var i = 0; i < el.childNodes.length ; i++)
                    {
                        nodes[i] = el.childNodes[i];
                    }
                    for(var i = 0 ; i < nodes.length; i++)
                    {
                        span.appendChild(nodes[i]);
                    }
                    el.appendChild(span);
                }
            },
            containsPlainText: function(el) {
                for(var i = 0; i < el.childNodes.length; i++) {
                    var node = el.childNodes[i];
                    if (node.nodeName == '#text' && YAHOO.lang.trim(node.textContent) != '') {
                        return true;
                    }
                }
                return false;
            },
            checkRow: function(el, inv_class) {
                var hide = true;
                for(var i = 0; i < el.children.length; i++) {
                    var node = el.children[i];
                    //For each row, check if the column has the inv_class class attribute, if not, do not hide
                    if (node.tagName.toLowerCase() == 'td' && !YAHOO.util.Dom.hasClass(node, inv_class)) {
                        hide = false;
                        break;
                    }
                }
                el.style.display = hide ? 'none' : '';
            },
            checkRowSidecar: function(el, inv_class) {
                if (!el || el.children.length === 0) {
                    return;
                }

                // All the fields in a row that we'd potentially want to show
                var allFields = [].slice.call(el.children);
                var hideableFields = _.filter(allFields, function(e) {
                    return $(e).hasClass('record-cell');
                });

                // Check if all the elements in the row we're looking at have
                // the hidden class or are filler cells.
                var shouldRowBeHidden = _.chain(hideableFields)
                    .map(element => {
                        let e = $(element);
                        return e.hasClass(inv_class) || e.hasClass('filler-cell');
                    })
                    .reduce(function(a, c) { return a && c; })
                    .value();

                el.style.display = shouldRowBeHidden ? 'none' : '';
            },
            checkPanelSidecar: function(el, inv_class, panel_class) {
                if (!el || !el.children) {
                    return;
                }

                var hide = true;

                // check if each row has its elements hidden
                for (var i = 0; i < el.children.length; i++) {
                    var node = el.children[i];
                    var inv_nodes = $(node).children('.' + inv_class);
                    // visible non-empty nodes (i.e. data-type not empty)
                    var vis_nodes = $(node).children(':not([data-type=""])');
                    hide &= inv_nodes.length == vis_nodes.length;
                }

                if (hide) {
                    $(el).removeClass(panel_class);
                } else {
                    $(el).addClass(panel_class);
                }
            }
        });
        SUGAR.forms.AssignToAction = function(expr) {
            if (_.isObject(expr)) {
                expr = expr.value;
            }
            this.expr = expr;
            this.target = 'assigned_user_name';
            if (_.isUndefined(SUGAR.App)) {
                // Initialize data source only for BWC
                this.dataSource = new YAHOO.util.DataSource('index.php?', {
                    responseType: YAHOO.util.XHRDataSource.TYPE_JSON,
                    responseSchema: {
                        resultsList: 'fields',
                        total: 'totalCount',
                        metaNode: 'fields',
                        metaFields: {total: 'totalCount', fields:'fields'}
                    },
                    connMethodPost: true
                });
            }
        };
        SUGAR.util.extend(SUGAR.forms.AssignToAction, SUGAR.forms.AbstractAction, {
            exec: function(context) {
                if (typeof(context) == 'undefined') {
                    context = this.context;
                }

                this.userName = this.evalExpression(this.expr, context);
                if (context.view) {
                    //We may get triggered before the view has rendered with the full field list.
                    //If that occurs wait for the next render to apply.
                    if (_.isEmpty(context.view.fields)) {
                        context.view.once('render', function(){this.exec(context);}, this);
                        return;
                    }
                    context.setAssignedUserName(this.target, this.userName);
                } else {
                    this.bwcExec(context);
                }
            },
            bwcExec: function(context) {
                if (typeof(context) == 'undefined') {
                    context = this.context;
                }

                var params = SUGAR.util.paramsToUrl({
                    to_pdf: 'true',
                    module: 'Home',
                    action: 'quicksearchQuery',
                    data: YAHOO.lang.JSON.stringify(sqs_objects['EditView_' + this.target]),
                    query: this.userName
                });

                this.sqs = sqs_objects['EditView_' + this.target];
                this.dataSource.sendRequest(params, {
                    success: function(param, resp) {
                        if(resp.results.length > 0) {
                            var match = resp.results[0];
                            for (var i = 0; i < this.sqs.field_list.length; i++) {
                                SUGAR.forms.AssignmentHandler.assign(
                                    this.sqs.populate_list[i],
                                    match[this.sqs.field_list[i]]
                                );
                            }
                        }
                    },
                    scope: this
                });
            },
            targetUrl: 'index.php?module=Home&action=TaxRate&to_pdf=1'
        });
		SUGAR.forms.SetOptionsAction = function(target, keyExpr, labelExpr) {
			this.afterRender = true;
			if (_.isObject(target)){
				labelExpr = target.labels;
				keyExpr = target.keys;
				target = target.target
			}
			this.keyExpr = keyExpr;
			this.labelExpr = labelExpr;
			this.target = target;
		};
				
		SUGAR.util.extend(SUGAR.forms.SetOptionsAction, SUGAR.forms.AbstractAction, {
			exec: function(context) {
				if (typeof(context) == 'undefined')
					context = this.context;

				var keys = this.evalExpression(this.keyExpr, context),
					labels = this.evalExpression(this.labelExpr, context),
					empty,
					selected = '';

				if (context.view)
				{
					var field = context.getField(this.target);
					//Cannot continue if the field does not exist on this view
					if (!field) {
					    return;
					}

                    selected = [].concat(field.model.get(this.target));
                    if (!this.canSetValue(context)) {
                        keys = _.uniq(keys.concat(selected));
                    }

                    empty = (_.size(keys) === 0 || _.size(keys) === 1) && (keys[0] == undefined || keys[0] === '');

					if (_.isString(labels))
						field.items = _.pick(App.lang.getAppListStrings(labels), keys);
					else
						field.items = _.object(keys, labels);

					slContext = context;

					field.model.fields[this.target].options = field.items;

					var visAction = new SUGAR.forms.SetVisibilityAction(this.target, (empty ? 'false' : 'true'), '');
					visAction.setContext(context);
					visAction.exec();

					//Remove from the selected options those options that are no longer available to select
					selected = _.filter(selected, function(key) {
					    return _.contains(keys, key);
					});

					if ((selected.length == 0 || (selected.length == 1 && selected[0] == '')) && field.model.fields[field.name].type != 'multienum') {
					    selected = [(empty ? '' : keys[0])];
					}

                    context.setValue(this.target, selected);
				}
				else {
					var field = context.getElement(this.target);
					if ( field == null )	return null;


					if (keys instanceof Array && field.options != null)
					{
						// get the options of this select
						var options = field.options;
						selected = [];

						for (var i = 0; i < options.length; i++) {
							if (options[i].selected)
								selected = selected.concat(options[i].value);
						}

						// empty the options
						while (options.length > 0) {
							field.remove(options[0]);
						}

						if (typeof(labels) == 'string') //get translated values from Sugar Language
						{
							var fullSet = SUGAR.language.get('app_list_strings', labels);
							labels = [];
							for (var i in keys)
							{
								labels[i] = fullSet[keys[i]];
							}
						}

						var new_opt;
						for (var i in keys) {
							if (labels instanceof Array)
							{
								if (typeof keys[i] == 'string')
								{
									if (typeof labels[i] == 'string') {
										new_opt = options[options.length] = new Option(labels[i], keys[i], keys[i] == selected);
									}
									else
									{
										new_opt = options[options.length] = new Option(keys[i], keys[i], keys[i] == selected);
									}
								}
							}
							else //Use the keys as labels
							{
								if (typeof keys[0] == 'undefined') {
									if (typeof(keys[i]) == 'string') {
										new_opt = options[options.length] = new Option(keys[i], i);
									}
								} else {
									if (typeof(value[i]) == 'string') {
										new_opt = options[options.length] = new Option(keys[i], keys[i]);
									}
								}
							}
							if (_.indexOf(selected, keys[i]) > -1) {
								new_opt.selected = true;
							}

						}

						if(!field.multiple && field.value != selected) {
							SUGAR.forms.AssignmentHandler.assign(this.target, field.value);
						}

						//Hide fields with empty lists

						var empty = (field.multiple && field.options.length == 0)
						 || (!field.multiple && field.options.length <= 1 && field.value == '');
						var visAction = new SUGAR.forms.SetVisibilityAction(this.target, (empty ? 'false' : 'true'), '');
						visAction.setContext(context);
						visAction.exec();

						if ( SUGAR.forms.AssignmentHandler.ANIMATE && !empty)
							SUGAR.forms.FlashField(field);
					}
					//Check if we are on a detailview and just need to hide the field
					else if (keys instanceof Array && (keys.length == 0 || (keys.length == 1 && keys[0] == ''))){
						//Use a normal visibility action to hide the field
						var va = new SUGAR.forms.SetVisibilityAction(this.target, 'false', '');
						va.exec(context);
					}
				}
			}
		});
        SUGAR.forms.ReadOnlyAction = function(target, expr) {
            this.afterRender = true;
            if (_.isObject(target)){
                expr = target.value;
                target = target.target
            }
            this.target = target;
            this.expr = expr;
        }

        SUGAR.util.extend(SUGAR.forms.ReadOnlyAction, SUGAR.forms.AbstractAction, {
            /**
             * Triggers the style dependencies.
             */
            exec: function(context) {
                if (typeof(context) == 'undefined') context = this.context;
                var val = this.evalExpression(this.expr, context),
                    readOnly = val == SUGAR.expressions.Expression.TRUE;
                
                if (context.view) {
                    //We may get triggered before the view has rendered with the full field list.
                    //If that occurs wait for the next render to apply.
                    if (_.isEmpty(context.view.fields)) {
                        context.view.once('render', function(){this.exec(context);}, this);
                        return;
                    }
                    var field = context.view.getField(this.target, context.model);
                    if (field) {
                        field.def.readOnlyProp = readOnly;
                    }
                   

                    context.setFieldDisabled(this.target, readOnly);
                } else {
                    this.bwcExec(context, readOnly);
                }

            },

            bwcExec: function(context, readonly) {
                var el = SUGAR.forms.AssignmentHandler.getElement(this.target);
                if (!el) {
                    return;
                }
                this.setReadOnly(el, readonly);
                this.setDateField(el, readonly);
            },

            setReadOnly: function(el, set)
            {
                var D = YAHOO.util.Dom;
                var property = el.type == 'checkbox' || 'select' ? 'disabled' : 'readonly';
                el[property] = set;
                if (set)
                {
                    var bgColor = _.contains(document.body.classList, 'sugar-dark-theme') ? '#19191D' : '#EEEEEE';
                    D.setStyle(el, 'background-color', bgColor);
                    if (!SUGAR.isIE)
                       D.setStyle(el, 'color', '#22');
                } else
                {
                    D.setStyle(el, 'background-color', '');
                        if (!SUGAR.isIE)
                            D.setStyle(el, 'color', '');
                }
            },

            setDateField: function(el, set)
            {
                var D = YAHOO.util.Dom, id = el.id, trig = D.get(id + '_trigger');
                if(!trig) return;
                var fields = [
                    D.get(id + '_date'),
                    D.get(id + '_minutes'),
                    D.get(id + '_meridiem'),
                    D.get(id + '_hours')];

                for (var i in fields)
                    if (fields[i] && fields[i].id)
                        this.setReadOnly(fields[i], set);

                if (set)
                    D.setStyle(trig, 'display', 'none');
                else
                    D.setStyle(trig, 'display', '');
            }
        });/**
 * Completely hide or show a panel
 */
SUGAR.forms.SetPanelVisibilityAction = function(target, expr)
{
    this.afterRender = true;

    if (_.isObject(target)){
        expr = target.value;
        target = target.target;
    }
    //BWC
    if (_.isString(target) && _.isUndefined(SUGAR.App)) {
       var parents = $('#' + target).parents('div');
       if(parents.length) {
          target = parents.attr('id');
       }
    }

    this.target = target;
    this.expr   = 'cond(' + expr + ', "", "none")';
}


/**
 * Triggers this dependency to be re-evaluated again.
 */
SUGAR.util.extend(SUGAR.forms.SetPanelVisibilityAction, SUGAR.forms.AbstractAction, {
    hideChildren: function() {
        if (typeof(SUGAR.forms.SetPanelVisibilityAction.hiddenFields) == "undefined")
        {
            this.createFieldBin();
        }
        var target = document.getElementById(this.target);
        var field_table = target.getElementsByTagName('table')[0];
        if (field_table != null) 
        {
            field_table.id = this.target + "_tbl";
            SUGAR.forms.SetPanelVisibilityAction.hiddenFields.appendChild(field_table);
        }
    },
    
    showChildren: function() {
        var target = document.getElementById(this.target);
        var field_table = document.getElementById(this.target + "_tbl");
        if (field_table != null)
            target.appendChild(field_table);
    },
    
    createFieldBin: function() {
        var tmpElem = document.createElement('div');
        tmpElem.id = 'panelHiddenFields';
        tmpElem.style.display = 'none';
        document.body.appendChild(tmpElem);
        SUGAR.forms.SetPanelVisibilityAction.hiddenFields = tmpElem;
    },
    
    /**
     * Triggers the style dependencies.
     */
    exec: function(context)
    {
        if (typeof(context) == 'undefined')
            context = this.context;

        if (context.view)
            return this.sidecarExec(context);
        try {
            var visibility = this.evalExpression(this.expr, context);
            var target = document.getElementById(this.target);
            if (target != null) {               
                if (target.style.display != 'none')
                 SUGAR.forms.animation.sizes[this.target] = target.clientHeight;
                       
                if (SUGAR.forms.AssignmentHandler.ANIMATE) {
                    if (visibility == 'none' && target.style.display != 'none') {
                       SUGAR.forms.animation.Collapse(this.target, this.hideChildren, this);
                       return;
                    } 
                    else if (visibility != 'none' && target.style.display == 'none') 
                    {
                        this.showChildren();
                        SUGAR.forms.animation.Expand(this.target);
                        return;
                    }
                }
                
                if (visibility == 'none')
                    this.hideChildren();
                else
                    this.showChildren();
                target.style.display = visibility;
            }
        } catch (e) {if (console && console.log) console.log(e);}
    },
    sidecarExec : function(context) {
        var hide = (this.evalExpression(this.expr, context) === 'none'),
            tab = context.view.$(".tab." + this.target),
            panel = context.view.$("div.record-panel[data-panelname='" + this.target + "']"),
            isActive = tab && tab.hasClass("active");

        //If we can't find a tab, just look for a panel
        if (!tab || !tab.length) {
            //Hide/show a panel (No need to worry about the active tab)
            if (panel.length > 0) {
                if (hide) {
                    panel.hide();
                } else {
                    panel.show();
                }
                this.triggerFieldsVisibility(context, this.target, hide);
            } else {
                //If we got here it means the panel name/id was probably invalid.
                console.log("unable to find panel " + this.target);
            }
        } else {
            //Hide/show tabs
            if (hide) {
                tab.hide();
                //If we are hiding the active tab, show the first visible tab instead.
                if (isActive) {
                    var tabs = context.view.$("li.tab:visible");
                    if (tabs.length > 0 && context.view.setActiveTab) {
                        //setActiveTab currently expects an event. This may change in the future
                        context.view.setActiveTab({currentTarget:tabs[0].children[0]});
                        context.view.handleActiveTab();
                    }
                }
            } else {
                tab.show();
            }
            this.triggerFieldsVisibility(context, this.target, hide);
        }

    },
    triggerFieldsVisibility : function(context, target, hide) {

        _.each(this.getPanelFieldNames(context, target), function(fieldName) {
            var field = context.view.getField(fieldName);
            if (field && !_.isUndefined(fieldName)) {
                if (_.isUndefined(field.wasRequired) && !_.isUndefined(field.def)) {
                    field.wasRequired = field.def.required;
                }
                if (!_.isUndefined(field.name) && field.name === fieldName) {
                    context.setFieldDisabled(fieldName, hide);
                }
                if (field.wasRequired === true) {
                    context.setFieldRequired(fieldName, !hide);
                }
            }
        });

    },
    getPanelFieldNames : function(context, panelName) {
      var panel = _.find(context.view.meta.panels, function(panel) {
        return panel.name === panelName;
      });

      return _.pluck(panel.fields, 'name');
    }
});

SUGAR.forms.animation.sizes = { };

SUGAR.forms.animation.Collapse = function(target, callback, scope)
{
    var t = document.getElementById(target);
    if (t == null) return;
    
    SUGAR.forms.animation.sizes[target] = t.clientHeight;
    t.style.overflow = "hidden";
    
    // Create a new ColorAnim instance
    var collapseAnim = new YAHOO.util.Anim(target, { height: { to: 0 } }, 0.5, YAHOO.util.Easing.easeBoth);
    collapseAnim.onComplete.subscribe(function () {
        t.style.display = 'none';
        callback.call(scope);
    });
    collapseAnim.animate();
};

SUGAR.forms.animation.Expand = function(target)
{
    var t = document.getElementById(target);
    if (t == null) return;
    
    
    t.style.overflow = "hidden";
    t.style.height = "0px";
    t.style.display = "";
    
    var expandAnim = new YAHOO.util.Anim(target, { height: { to: SUGAR.forms.animation.sizes[target]  } },
        0.5, YAHOO.util.Easing.easeBoth);
    
    expandAnim.onComplete.subscribe(function () {
        t.style.height = 'auto';
    });
    
    expandAnim.animate();
};		SUGAR.forms.SetValueAction = function(target, valExpr) {
			if (_.isObject(target)){
			    this.expr = target.value;
			    this.target = target.target;
			    this.errorValue = !_.isUndefined(target.errorValue) ? target.errorValue : null;
			} else {
                this.expr = valExpr;
                this.target = target;
			}
		};
		SUGAR.util.extend(SUGAR.forms.SetValueAction, SUGAR.forms.AbstractAction, {
			exec : function(context)
			{
				if (typeof(context) == 'undefined') {
				    context = this.context;
                }

				try {
				    // set the target for rollup expressions
				    context.target = this.target;

				    var val = this.evalExpression(this.expr, context),
				        cVal = context.getValue(this.target).evaluate();
                    // only set the value if the two numbers are different
                    // get rid of the flash
                    if (!_.isUndefined(val) && val !== cVal && this.canSetValue(context)) {
                        context.setValue(this.target, val);
				    }
				} catch (e) {
				    if (!_.isUndefined(this.errorValue) && !_.isNull(this.errorValue)) {
				        context.setValue(this.target, this.errorValue);
				    }
			    }
	       }
		});/**
 * The function to object map that is used by the Parser
 * to parse expressions into objects.
 */
SUGAR.FunctionMap = {
	'year'	:	SUGAR.expressions.YearExpression,	'daysUntil'	:	SUGAR.expressions.DaysUntilExpression,	'now'	:	SUGAR.expressions.NowExpression,	'hoursUntil'	:	SUGAR.expressions.HoursUntilExpression,	'today'	:	SUGAR.expressions.TodayExpression,	'date'	:	SUGAR.expressions.DefineDateExpression,	'addDays'	:	SUGAR.expressions.AddDaysExpression,	'rollupConditionalMinDate'	:	SUGAR.expressions.MinDateConditionalRelatedExpression,	'dayofmonth'	:	SUGAR.expressions.DayOfMonthExpression,	'maxRelatedDate'	:	SUGAR.expressions.MaxRelatedDateExpression,	'monthofyear'	:	SUGAR.expressions.MonthOfYearExpression,	'dayofweek'	:	SUGAR.expressions.DayOfWeekExpression,	'timestamp'	:	SUGAR.expressions.TimestampExpression,	'forecastOnlySalesStages'	:	SUGAR.expressions.ForecastOnlySalesStageExpression,	'forecastSalesStages'	:	SUGAR.expressions.ForecastSalesStageExpression,	'getDropdownValueSet'	:	SUGAR.expressions.SugarTranslatedDropDownExpression,	'getTransDD'	:	SUGAR.expressions.SugarTranslatedDropDownExpression,	'createList'	:	SUGAR.expressions.DefineEnumExpression,	'enum'	:	SUGAR.expressions.DefineEnumExpression,	'getListWhere'	:	SUGAR.expressions.SugarListWhereExpression,	'getDropdownKeySet'	:	SUGAR.expressions.SugarDropDownExpression,	'getDD'	:	SUGAR.expressions.SugarDropDownExpression,	'forecastIncludedCommitStages'	:	SUGAR.expressions.ForecastIncludedCommitStagesExpression,	'hourOfDay'	:	SUGAR.expressions.HourOfDayExpression,	'time'	:	SUGAR.expressions.DefineTimeExpression,	'subtract'	:	SUGAR.expressions.SubtractExpression,	'currencySubtract'	:	SUGAR.expressions.SubtractExpression,	'sub'	:	SUGAR.expressions.SubtractExpression,	'divide'	:	SUGAR.expressions.DivideExpression,	'currencyDivide'	:	SUGAR.expressions.DivideExpression,	'div'	:	SUGAR.expressions.DivideExpression,	'log'	:	SUGAR.expressions.LogExpression,	'average'	:	SUGAR.expressions.AverageExpression,	'avg'	:	SUGAR.expressions.AverageExpression,	'median'	:	SUGAR.expressions.MedianExpression,	'rollupAve'	:	SUGAR.expressions.AverageRelatedExpression,	'rollupAvg'	:	SUGAR.expressions.AverageRelatedExpression,	'floor'	:	SUGAR.expressions.FloorExpression,	'min'	:	SUGAR.expressions.MinimumExpression,	'abs'	:	SUGAR.expressions.AbsoluteValueExpression,	'rollupMin'	:	SUGAR.expressions.MinRelatedExpression,	'round'	:	SUGAR.expressions.RoundExpression,	'add'	:	SUGAR.expressions.AddExpression,	'currencyAdd'	:	SUGAR.expressions.AddExpression,	'pow'	:	SUGAR.expressions.PowerExpression,	'negate'	:	SUGAR.expressions.NegateExpression,	'strlen'	:	SUGAR.expressions.StringLengthExpression,	'sentimentScoreToStr'	:	SUGAR.expressions.SentimentScoreToStringExpression,	'number'	:	SUGAR.expressions.ValueOfExpression,	'countConditional'	:	SUGAR.expressions.CountConditionalRelatedExpression,	'count'	:	SUGAR.expressions.CountRelatedExpression,	'rollupConditionalSum'	:	SUGAR.expressions.SumConditionalRelatedExpression,	'stddev'	:	SUGAR.expressions.StandardDeviationExpression,	'max'	:	SUGAR.expressions.MaximumExpression,	'indexOf'	:	SUGAR.expressions.IndexOfExpression,	'ceil'	:	SUGAR.expressions.CeilingExpression,	'ceiling'	:	SUGAR.expressions.CeilingExpression,	'prorateValue'	:	SUGAR.expressions.ProrateValueExpression,	'rollupMax'	:	SUGAR.expressions.MaxRelatedExpression,	'ln'	:	SUGAR.expressions.NaturalLogExpression,	'rollupSum'	:	SUGAR.expressions.SumRelatedExpression,	'rollupCurrencySum'	:	SUGAR.expressions.SumRelatedExpression,	'multiply'	:	SUGAR.expressions.MultiplyExpression,	'currencyMultiply'	:	SUGAR.expressions.MultiplyExpression,	'mul'	:	SUGAR.expressions.MultiplyExpression,	'isValidPhone'	:	SUGAR.expressions.IsValidPhoneExpression,	'isValidDBName'	:	SUGAR.expressions.IsValidDBNameExpression,	'not'	:	SUGAR.expressions.NotExpression,	'isForecastClosed'	:	SUGAR.expressions.IsForecastClosedExpression,	'isBefore'	:	SUGAR.expressions.isBeforeExpression,	'isValidTime'	:	SUGAR.expressions.IsValidTimeExpression,	'isAlphaNumeric'	:	SUGAR.expressions.IsAlphaNumericExpression,	'greaterThan'	:	SUGAR.expressions.GreaterThanExpression,	'equal'	:	SUGAR.expressions.EqualExpression,	'isOwner'	:	SUGAR.expressions.IsOwnerExpression,	'isForecastClosedLost'	:	SUGAR.expressions.IsForecastClosedLostExpression,	'isWithinRange'	:	SUGAR.expressions.IsInRangeExpression,	'isRequiredCollection'	:	SUGAR.expressions.IsRequiredCollectionExpression,	'isAfter'	:	SUGAR.expressions.isAfterExpression,	'isAssigned'	:	SUGAR.expressions.IsAssignedExpression,	'isValidDate'	:	SUGAR.expressions.IsValidDateExpression,	'isForecastClosedWon'	:	SUGAR.expressions.IsForecastClosedWonExpression,	'isAlpha'	:	SUGAR.expressions.IsAlphaExpression,	'isNumeric'	:	SUGAR.expressions.IsNumericExpression,	'or'	:	SUGAR.expressions.OrExpression,	'isValidEmail'	:	SUGAR.expressions.IsValidEmailExpression,	'doBothExist'	:	SUGAR.expressions.BinaryDependencyExpression,	'and'	:	SUGAR.expressions.AndExpression,	'isInList'	:	SUGAR.expressions.IsInEnumExpression,	'isInEnum'	:	SUGAR.expressions.IsInEnumExpression,	'related'	:	SUGAR.expressions.RelatedFieldExpression,	'sugarField'	:	SUGAR.expressions.SugarFieldExpression,	'currentUserField'	:	SUGAR.expressions.CurrentUserFieldExpression,	'currencyRate'	:	SUGAR.expressions.CurrencyRateExpression,	'ifElse'	:	SUGAR.expressions.ConditionExpression,	'cond'	:	SUGAR.expressions.ConditionExpression,	'valueAt'	:	SUGAR.expressions.IndexValueExpression,	'subStr'	:	SUGAR.expressions.SubStrExpression,	'concat'	:	SUGAR.expressions.ConcatenateExpression,	'getDropdownValue'	:	SUGAR.expressions.SugarDropDownValueExpression,	'getDDValue'	:	SUGAR.expressions.SugarDropDownValueExpression,	'strReplace'	:	SUGAR.expressions.StrReplaceExpression,	'formatName'	:	SUGAR.expressions.FormatedNameExpression,	'strToUpper'	:	SUGAR.expressions.StrToUpperExpression,	'strToLower'	:	SUGAR.expressions.StrToLowerExpression,	'contains'	:	SUGAR.expressions.ContainsExpression,	'forecastCommitStage'	:	SUGAR.expressions.ForecastCommitStageExpression,	'toString'	:	SUGAR.expressions.DefineStringExpression,	'string'	:	SUGAR.expressions.DefineStringExpression,	'translateLabel'	:	SUGAR.expressions.SugarTranslateExpression,	'translate'	:	SUGAR.expressions.SugarTranslateExpression,	'charAt'	:	SUGAR.expressions.CharacterAtExpression,	'opportunitySalesStage'	:	SUGAR.expressions.OpportunitySalesStageExpression};
/**
 * The function to object map that is used by the Parser
 * to parse expressions into objects.
 */
SUGAR.NumericConstants = {
	'pi'	:	3.1415926535898,	'e'	:	2.718281828459};
