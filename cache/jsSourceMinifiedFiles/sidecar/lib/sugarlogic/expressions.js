if(typeof(SUGAR)=='undefined')SUGAR={};if(typeof(SUGAR.util)=='undefined')SUGAR.util={};if(typeof(SUGAR.expressions)=='undefined')SUGAR.expressions={};(function(){SUGAR.util.extend=SUGAR.util.extend||(SUGAR.App?SUGAR.App.utils.extendFrom:null);var SEE=SUGAR.expressions.Expression=function(context){this.context=context;};SEE.INFINITY=-1;SEE.STRING_TYPE="string";SEE.NUMERIC_TYPE="number";SEE.DATE_TYPE="date";SEE.TIME_TYPE="time";SEE.BOOLEAN_TYPE="boolean";SEE.ENUM_TYPE="enum";SEE.RELATE_TYPE="relate";SEE.GENERIC_TYPE="generic";SEE.TRUE="true";SEE.FALSE="false";SEE.isTruthy=function(v){return _.isString(v)?(v=="1"||v==SEE.TRUE):!!v;};SUGAR.expressions.NumericConstants={pi:3.14159265,e:2.718281828459045}
SEE.prototype.init=function(params){if(params instanceof Array&&params.length==1){this.params=params[0];}
else{this.params=params;}
this.validateParameters();};SEE.prototype.getParameters=function(){if(!this.params){return this.params;}
var params,param,result=[],types=this.getParameterTypes(),oneParam=this.getParamCount()==1,type,i;if(oneParam||(!_.isArray(this.params)&&_.isObject(this.params))){params=[this.params];}else{params=this.params;}
if(!(types instanceof Array)){type=types;types=[];for(i=0;i<params.length;i++){types.push(type);}}
var map=this.constructor.TYPE_CAST_MAP;for(i=0;i<params.length;i++){param=params[i];if(param instanceof SEE){type=SEP.prototype.getType(param);if(type!=types[i]&&types[i]in map){param=new SUGAR.expressions[map[types[i]]](param,this.context);}}
result.push(param);}
if(oneParam){result=result.shift();}
return result;};SEE.prototype.validateParameters=function(){var params=this.params;var count=this.getParamCount();var types=this.getParameterTypes();if(typeof(count)!='number'){throw(this.getClass()+": Number of paramters required must be a number");}
if(typeof(types)!='string'&&!(types instanceof Array)){throw(this.getClass()+": Parameter types must be valid and match the parameter count");}
if(types instanceof Array&&count!=SEE.INFINITY&&count!=types.length){throw(this.getClass()+": Parameter types must be valid and match the parameter count");}
if(typeof(types)=='string'){if(SEE.TYPE_MAP[types]==null){throw(this.getClass()+": Invalid type requirement '"+types+"'");}}else{for(var i=0;i<types.length;i++){if(typeof(SEE.TYPE_MAP[types[i]])=='undefined'){throw(this.getClass()+": Invalid type requirement '"+types[i]+"'");}}}
if(count==0&&typeof(params)=='undefined'){return;}
if(count==1&&this.isProperType(params,types)){return;}
if(count>1&&!(params instanceof Array)){throw(this.getClass()+": Requires exactly "+count+" parameter(s)");}
if(count==1&&params instanceof Array){throw(this.getClass()+": Requires exactly 1 parameter");}
if(count!=SEE.INFINITY&&params instanceof Array&&params.length!=count){throw(this.getClass()+": Requires exactly "+count+" parameter(s)");}
if(typeof(types)=='string'){if(!(params instanceof Array)){if(this.isProperType(params,types)){return;}
throw(this.getClass()+": Parameter must be of type '"+types+"'");}
for(var i=0;i<params.length;i++){if(!this.isProperType(params[i],types)){throw(this.getClass()+": All parameters must be of type '"+types+"'");}}}
else{if(!(params instanceof Array)){if(this.isProperType(params,types[0])){return;}
throw(this.getClass()+": Parameter must be of type '"+types[0]+"'");}
for(var i=0;i<types.length;i++){if(!this.isProperType(params[i],types[i])){throw(this.getClass()+": The parameter at index "+i+" must be of type "+types[i]);}}}};SEE.prototype.getParamCount=function(){return SEE.INFINITY;};SEE.prototype.isProperType=function(variable,type){var see=SEE;if(type instanceof Array){return false;}
var c=see.TYPE_MAP[type];if(typeof(c)=='undefined'||c==null||c==''){return false;}
if(variable instanceof c||variable instanceof see.TYPE_MAP.generic||type==see.GENERIC_TYPE)
return true;if(variable instanceof see){variable=variable.evaluate();}
switch(type){case see.STRING_TYPE:return(typeof(variable)=='string'||typeof(variable)=='number'||variable instanceof see.TYPE_MAP[see.NUMERIC_TYPE]);break;case see.NUMERIC_TYPE:return(typeof(variable)=='number'||SUGAR.expressions.isNumeric(variable));break;case see.BOOLEAN_TYPE:return variable==see.TRUE||variable==see.FALSE||variable===1||variable===0||variable==='1'||variable==='0'||variable==='';break;case see.DATE_TYPE:case see.TIME_TYPE:if(variable===null||variable===''||(typeof(variable)=='string'&&SUGAR.util.DateUtils.guessFormat(variable)))
return true;break;case see.RELATE_TYPE:return true;break;}
return false;};SEE.prototype.evaluate=function(){};SEE.prototype.getClass=function(exp){for(var i in SUGAR.FunctionMap){if(typeof SUGAR.FunctionMap[i]=="function"&&SUGAR.FunctionMap[i].prototype&&this instanceof SUGAR.FunctionMap[i])
return i;}
return false;};SEE.prototype.getParameterTypes=function(){};SUGAR.expressions.GenericExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.GenericExpression,SEE,{getParameterTypes:function(){return SEE.GENERIC_TYPE;}});SUGAR.expressions.NumericExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.NumericExpression,SEE,{getParameterTypes:function(){return SEE.NUMERIC_TYPE;}});SUGAR.expressions.StringExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.StringExpression,SEE,{getParameterTypes:function(){return SEE.STRING_TYPE;}});SUGAR.expressions.BooleanExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.BooleanExpression,SEE,{getParameterTypes:function(){return SEE.BOOLEAN_TYPE;}});SUGAR.expressions.EnumExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.EnumExpression,SEE,{getParameterTypes:function(){return SEE.ENUM_TYPE;}});SUGAR.expressions.DateExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.DateExpression,SEE,{getParameterTypes:function(){return SEE.DATE_TYPE;}});SUGAR.expressions.TimeExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.TimeExpression,SEE,{getParameterTypes:function(){return SEE.TIME_TYPE;}});SUGAR.expressions.RelateExpression=function(params){};SUGAR.util.extend(SUGAR.expressions.RelateExpression,SEE,{getParameterTypes:function(){return SEE.GENERIC_TYPE;}});SEE.TYPE_MAP={"number":SUGAR.expressions.NumericExpression,"string":SUGAR.expressions.StringExpression,"date":SUGAR.expressions.DateExpression,"time":SUGAR.expressions.TimeExpression,"boolean":SUGAR.expressions.BooleanExpression,"enum":SUGAR.expressions.EnumExpression,"relate":SUGAR.expressions.RelateExpression,"generic":SUGAR.expressions.GenericExpression};SEE.TYPE_CAST_MAP={"number":"ValueOfExpression","string":"DefineStringExpression"};SUGAR.expressions.ConstantExpression=function(params){this.init(params);}
SUGAR.util.extend(SUGAR.expressions.ConstantExpression,SUGAR.expressions.NumericExpression,{evaluate:function(){return this.getParameters();},getParamCount:function(){return 1;}});SUGAR.expressions.StringLiteralExpression=function(params){this.init(params);}
SUGAR.util.extend(SUGAR.expressions.StringLiteralExpression,SUGAR.expressions.StringExpression,{evaluate:function(){return this.getParameters();},getParamCount:function(){return 1;}});SUGAR.expressions.FalseExpression=function(params){this.init(params);}
SUGAR.util.extend(SUGAR.expressions.FalseExpression,SUGAR.expressions.BooleanExpression,{evaluate:function(){return SEE.FALSE;},getParamCount:function(){return 0;}});SUGAR.expressions.TrueExpression=function(params){this.init(params);}
SUGAR.util.extend(SUGAR.expressions.TrueExpression,SUGAR.expressions.BooleanExpression,{evaluate:function(){return SEE.TRUE;},getParamCount:function(){return 0;}});var SEP=SUGAR.expressions.ExpressionParser=function(){};SEP.prototype.getFieldsFromExpression=function(expression){return _.map(expression.match(/\$(\w+)/g),function(match){return match.replace(/\$/g,'');});}
SEP.prototype._generateRange=function(prefix,start,end){var str="";var i=parseInt(start);if(typeof(end)=='undefined')
while(this.getElement(prefix+''+i)!=null)
str+='$'+prefix+''+(i++)+',';else
for(;i<=end;i++){var t=prefix+''+i;if(this.getElement(t)!=null)
str+='$'+t+',';}
return str.substring(0,str.length-1);}
SEP.prototype._valueReplace=function(val){if(!(/^\$.*$/).test(val)){return val;}
return this.getValue(val.substring(1));}
SEP.prototype._performRangeReplace=function(expression){var isInQuotes=false;var prev;var inRange;for(var i=0;;i++){if(i==expression.length)break;var ch=expression.charAt(i);if(ch=='"'&&prev!='\\')isInQuotes=!isInQuotes;if(!isInQuotes&&ch=='%'){inRange=true;var loc_start=expression.indexOf('[',i+1);var loc_comma=expression.indexOf(',',loc_start);var loc_end=expression.indexOf(']',loc_start);if(loc_start<0||loc_end<0)throw("Invalid range syntax");var prefix=expression.substring(i+1,loc_start);var start,end;if(loc_comma>-1&&loc_comma<loc_end){start=expression.substring(loc_start+1,loc_comma);end=expression.substring(loc_comma+1,loc_end);}else{start=expression.substring(loc_start+1,loc_end);}
if(loc_comma>-1&&loc_comma<loc_end)end=expression.substring(loc_comma+1,loc_end);var result=this._generateRange(prefix,this._valueReplace(start),this._valueReplace(end));if(typeof(end)=='undefined')
expression=expression.replace('%'+prefix+'['+start+']',result);else
expression=expression.replace('%'+prefix+'['+start+','+end+']',result);i=i+result.length-1;}
prev=ch;}
return expression;}
SEP.prototype.validate=function(expr){if(typeof(expr)!='string')throw"ExpressionParser requires a string expression.";var fixed=this.toConstant(expr);if(fixed!=null&&typeof(fixed)!='undefined')
return true;if((/^[\w\-]+\(.*\)$/).exec(expr)==null){throw("Syntax Error (Expression Format Incorrect '"+expr+"' )");}
if(expr.indexOf('(')<0)
throw("Syntax Error (No opening paranthesis found)");return true;}
SEP.prototype.tokenize=function(expr){var fixed=this.toConstant(expr);if(fixed!=null&&typeof(fixed)!='undefined'){return{type:"constant",returnType:this.getType(fixed),value:fixed.evaluate()}}
if(/^[$]\w+$/.test(expr)){return{type:"variable",name:expr.trim().substr(1)}}
if(/^[$]\w+\.\w+$/.test(expr)){expr=expr.trim();return{type:"variable",name:expr.substr(1,expr.indexOf('.')-1),relate:expr.substr(expr.indexOf('.')+1)}}
var open_paren_loc=expr.indexOf('(');if(open_paren_loc<1)
throw(expr+": Syntax Error, no open parentheses found");if(expr.charAt(expr.length-1)!=')')
throw(expr+": Syntax Error, no close parentheses found");var func=expr.substring(0,open_paren_loc);var params=expr.substring(open_paren_loc+1,expr.length-1);var level=0;var length=params.length;var argument="";var args=new Array();var currChar=null;var lastCharRead=null;var justReadString=false;var justReadComma=false;var isInQuotes=false;var isPrevCharBK=false;for(var i=0;i<=length;i++){lastCharRead=currChar;justReadComma=false;if(i==length){argument=argument.trim();if(argument!="")
args[args.length]=this.tokenize(argument);break;}
isPrevCharBK=(lastCharRead=='\\');currChar=params.charAt(i);if(isInQuotes&&currChar!='"'&&!isPrevCharBK){argument+=currChar;continue;}
if(currChar=='"'&&!isPrevCharBK&&level==0){if(isInQuotes){var end_reg=params.indexOf(",",i);if(end_reg<0)end_reg=params.length-1;var start_reg=(i<length-1?i+1:length-1);var temp=params.substring(start_reg,end_reg);if((/^\s*$/).exec(temp)==null)
throw(func+": Syntax Error (Improperly Terminated String '"+temp+"')"+(start_reg)+" "+end_reg);}
isInQuotes=!isInQuotes;}
if(currChar=='('){level++;}else if(currChar==')'){level--;}
else if(currChar==','&&level==0){argument=argument.trim();if(argument=="")
throw("Syntax Error: Unexpected ','");args[args.length]=this.tokenize(argument);argument="";justReadComma=true;continue;}
argument+=currChar;}
if(isInQuotes)throw("Syntax Error (Unterminated String Literal)");if(level!=0)throw("Syntax Error (Incorrectly Matched Parantheses)");if(justReadComma)throw("Syntax Error (No parameter after comma near <b>"+func+"</b>)");return{type:"function",name:func.trim(),args:args}}
SEP.prototype.getType=function(variable){for(var type in SEE.TYPE_MAP){if(variable instanceof SEE.TYPE_MAP[type]){return type;}}
return false;};SEP.prototype.evaluate=function(expr,context){if(typeof(expr)!='string')throw"ExpressionParser requires a string expression.";expr=expr.replace(/^\s+|\s+$|\n/g,"");var fixed=this.toConstant(expr);if(fixed!=null&&typeof(fixed)!='undefined')
return fixed;if(expr.match(/^\$\w+$/)){if(typeof(context)=="undefined")
throw("Syntax Error: variable "+expr+" without context");return context.getValue(expr.substring(1));}
if(expr.match(/^\$\w+\.\w+$/)){return context.getRelatedValue(expr.substr(1,expr.indexOf('.')-1),expr.substr(expr.indexOf('.')+1));}
if((/^[\w\-]+\(.*\)$/).exec(expr)==null){throw("Syntax Error (Expression Format Incorrect '"+expr+"' )");}
var open_paren_loc=expr.indexOf('(');if(open_paren_loc<0)
throw("Syntax Error (No opening paranthesis found)");var func=expr.substring(0,open_paren_loc);if(SUGAR.FunctionMap[func]==null)
throw(func+": No such function defined");var params=expr.substring(open_paren_loc+1,expr.length-1);var level=0;var length=params.length;var argument="";var args=new Array();var currChar=null;var lastCharRead=null;var justReadString=false;var isInQuotes=false;var isPrevCharBK=false;var isInVar=false;if(length>0){for(var i=0;i<=length;i++){lastCharRead=currChar;if(i==length){args[args.length]=this.evaluate(argument,context);break;}
isPrevCharBK=(lastCharRead=='\\');currChar=params.charAt(i);if(isInQuotes&&currChar!='"'&&!isPrevCharBK){argument+=currChar;continue;}
if(isInVar&&(currChar==" "||currChar==","))
isInVar=false;if(currChar=='"'&&!isPrevCharBK&&level==0){if(isInQuotes){var end_reg=params.indexOf(",",i);if(end_reg<0)end_reg=params.length-1;var start_reg=(i<length-1?i+1:length-1);var temp=params.substring(start_reg,end_reg);if((/^\s*$/).exec(temp)==null)
throw(func+": Syntax Error (Improperly Terminated String '"+temp+"')"+(start_reg)+" "+end_reg);}
isInQuotes=!isInQuotes;}
if(currChar=='$'&&!isPrevCharBK&&!isInQuotes&&level==0){if(isInVar)
throw(func+": Syntax Error (unexpeted '$' in  '"+argument+"')");else{isInVar=true;}}
if(currChar=='('){level++;}else if(currChar==')'){level--;}
else if(currChar==','&&level==0){args[args.length]=this.evaluate(argument,context);argument="";continue;}
argument+=currChar;}}
if(isInQuotes)throw("Syntax Error (Unterminated String Literal)");if(level!=0)throw("Syntax Error (Incorrectly Matched Parantheses)");return new SUGAR.FunctionMap[func](args,context);}
SEP.prototype.toConstant=function(expr){if(isFinite(expr)&&!isNaN(parseFloat(expr))){return new SUGAR.expressions.ConstantExpression(parseFloat(expr));}
var fixed=SUGAR.expressions.NumericConstants[expr];if(fixed!=null&&typeof(fixed)!='undefined')
return new SUGAR.expressions.ConstantExpression(parseFloat(fixed));if((/^"[\s\S]*"$/).exec(expr)!=null){expr=expr.substring(1,expr.length-1);return new SUGAR.expressions.StringLiteralExpression(expr);}
if(expr==''){return new SUGAR.expressions.StringLiteralExpression(expr);}
if(expr=="true"){return new SUGAR.expressions.TrueExpression();}else if(expr=="false"){return new SUGAR.expressions.FalseExpression();}
if((/^(0[0-9]|1[0-2])\/([0-2][0-9]|3[0-1])\/[0-3][0-9]{3,3}$/).exec(expr)!=null){var month=parseFloat(expr.substring(0,2));var day=parseFloat(expr.substring(3,2));var year=parseFloat(expr.substring(6,4));return new SUGAR.expressions.DateExpression([month,day,year]);}
if((/^([0-1][0-9]|2[0-4]):[0-5][0-9]:[0-5][0-9]$/).exec(expr)!=null){var hour=parseFloat(expr.substring(0,2));var minute=parseFloat(expr.substring(3,2));var second=parseFloat(expr.substring(6,2));return new SUGAR.expressions.TimeExpression([hour,minute,second]);}
return null;};SEP.prototype.getRelatedFieldsFromFormula=function(expr,actionTarget){var fields=[],relateFunctions=['related','count','rollupSum','rollupMax','rollupMin','rollupAve','rollupAvg','rollupCurrencySum','rollupConditionalSum','countConditional','maxRelatedDate','rollupConditionalMinDate'];var recurseTokens=function(t,actionTarget){if(t.type=="variable"&&t.relate){fields.push({type:"related",link:t.name,relate:t.relate});}else if(t.type=="function"&&relateFunctions.indexOf(t.name)!=-1){switch(t.name){case"related":if(t.args[1].type=="constant")
fields.push({type:"related",link:t.args[0].name,relate:t.args[1].value});break;case'rollupConditionalSum':var field={type:'rollupConditionalSum',link:t.args[0].name,relate:t.args[1].value,condition_field:t.args[2].value};if(t.args[3].type=='function'){var condVals=[];for(var i=0;i<t.args[3].args.length;i++){condVals.push('"'+t.args[3].args[i].value+'"');}
field.condition_expr=t.args[3].name+'('+condVals.join(',')+')';}else{field.condition_expr=t.args[3].value;}
fields.push(field);break;case'rollupConditionalMinDate':var field={type:'rollupConditionalMinDate',link:t.args[0].name,relate:t.args[1].value};var parseConditions=function(conditions){if(conditions.type=='function'){var conditionList=[];for(var i=0;i<conditions.args.length;i++){conditionList.push(conditions.args[i].value);}
return conditionList;}else{return conditions.value;}}
field.conditionFields=parseConditions(t.args[2]);field.conditionValues=parseConditions(t.args[3]);fields.push(field);break;case'countConditional':var field={target:actionTarget,type:'countConditional',link:t.args[0].name,condition_field:t.args[1].value};if(t.args[2].type=='function'){var condVals=[];for(var i=0;i<t.args[2].args.length;i++){condVals.push('"'+t.args[2].args[i].value+'"');}
field.condition_expr=t.args[2].name+'('+condVals.join(',')+')';}else{field.condition_expr=t.args[2].value;}
fields.push(field);break;case"count":fields.push({type:"count",link:t.args[0].name});break;default:if(t.args[1].type=="constant"){fields.push({type:t.name,link:t.args[0].name,relate:t.args[1].value});}}}else if(t.type=="function"){for(var i=0;i<t.args.length;i++){recurseTokens(t.args[i],actionTarget);}}}
try{var t=this.tokenize(expr);recurseTokens(t,actionTarget);}
catch(e){}
return fields;}
var SEC=SUGAR.expressions.ExpressionContext=function(){}
SEC.prototype.getValue=function(varname){return"";}
SEC.prototype.setValue=function(varname,value){return"";}
SEC.prototype.addListener=function(varname,callback,scope){return"";}
SEC.prototype.getRelatedValue=function(linkField,relField){return new SUGAR.RelatedFieldExpression([new SUGAR.expressions.StringLiteralExpression(linkField),new SUGAR.expressions.StringLiteralExpression(relField)]);}
SUGAR.expressions.isNumeric=function(str){if(typeof(str)!='number'&&typeof(str)!='string'){return false;}
str=SUGAR.expressions.unFormatNumber(str);return(isFinite(str)&&!isNaN(parseFloat(str)));};SUGAR.expressions.unFormatNumber=function(num){if(typeof num=='string'){var SE=SUGAR.expressions;var ts=",",ds=".";if(SE.userPrefs){ts=SE.userPrefs.num_grp_sep;ds=SE.userPrefs.dec_sep;};num=SE.replaceAll(num,ts,"");num=SE.replaceAll(num,ds,".");}
return num;};SUGAR.expressions.replaceAll=function(haystack,needle,rpl){if(needle==rpl||haystack==""||needle=="")return haystack;var str=haystack;while(str.indexOf(needle)>-1){str=str.replace(needle,rpl);}
return str;};SUGAR.util.DateUtils={parse:function(date,oldFormat){if(date instanceof Date)
return date;if(oldFormat=="user"){if(SUGAR.expressions.userPrefs&&SUGAR.expressions.userPrefs.datef){oldFormat=SUGAR.expressions.userPrefs.datef+" "+SUGAR.expressions.userPrefs.timef;}else{oldFormat=SUGAR.util.DateUtils.guessFormat(date);}}
if(oldFormat==null||oldFormat==""){oldFormat=SUGAR.util.DateUtils.guessFormat(date);}
if(oldFormat==false){if(/^\d+$/.test(date))
return new Date(date);return false;}
let dateParts={Y:1970,m:0,d:1,H:0,i:0};var part="";var dateRemain=date.trim();oldFormat=oldFormat.trim()+" ";for(var j=0;j<oldFormat.length;j++){var c=oldFormat.charAt(j);if(c==':'||c=='/'||c=='-'||c=='.'||c==" "||c=='a'||c=="A"||c=="T"){var i=dateRemain.indexOf(c);if(i==-1)i=dateRemain.length;var v=dateRemain.substring(0,i);dateRemain=dateRemain.substring(i+1);switch(part){case'm':if(!(v>0&&v<13))return false;dateParts.m=v-1;break;case'd':if(!(v>0&&v<32))return false;dateParts.d=v;break;case'Y':if(!(v>0))return false;dateParts.Y=v;break;case'h':var timeformat=oldFormat.substring(oldFormat.length-4);if(timeformat.toLowerCase()=="i a "||timeformat.toLowerCase()==c+"ia "){if(dateRemain.substring(dateRemain.length-2).toLowerCase()=='pm'){v=v*1;if(v<12){v+=12;}}}
case'H':dateParts.H=v;break;case'i':v=v.substring(0,2);dateParts.i=v;break;}
part="";}else{part=c;}}
return new Date(dateParts.Y,dateParts.m,dateParts.d,dateParts.H,dateParts.i);},guessFormat:function(date){if(typeof date!="string")
return false;var time="";var dateTimeSep;if(date.indexOf(" ")!=-1){dateTimeSep=" ";}else if(date.indexOf("T")!=-1){dateTimeSep="T";}
if(dateTimeSep){time=date.substring(date.indexOf(dateTimeSep)+1,date.length);date=date.substring(0,date.indexOf(dateTimeSep));}
var dateSep="/";if(date.indexOf("/")!=-1){}
else if(date.indexOf("-")!=-1){dateSep="-";}
else if(date.indexOf(".")!=-1){dateSep=".";}
else{return false;}
var dateParts=date.split(dateSep);var dateFormat="";var jsDate=new Date("Jan 1, 1970 00:00:00");if(dateParts[0].length==4){dateFormat="Y"+dateSep+"m"+dateSep+"d";}
else if(dateParts[2].length==4){dateFormat="m"+dateSep+"d"+dateSep+"Y";}
else{return false;}
if(time!=""){var timeFormat="";var timeSep=":";if(time.indexOf(".")==2){timeSep=".";}
if(time.indexOf(" ")!=-1){var timeParts=time.split(" ");if(timeParts[1]=="am"||timeParts[1]=="pm"){return dateFormat+dateTimeSep+"h"+timeSep+"i a";}else if(timeParts[1]=="AM"||timeParts[1]=="PM"){return dateFormat+dateTimeSep+"h"+timeSep+"i A";}}
else{var timeEnd=time.substring(time.length-2,time.length);if(timeEnd=="AM"||timeEnd=="PM"){return dateFormat+dateTimeSep+"h"+timeSep+"iA";}
if(timeEnd=="am"||timeEnd=="pm"){return dateFormat+dateTimeSep+"h"+timeSep+"iA";}
return dateFormat+dateTimeSep+"H"+timeSep+"i";}}
return dateFormat;},formatDate:function(date,useTime,format){if(!format&&SUGAR.expressions.userPrefs.datef&&SUGAR.expressions.userPrefs.timef){if(useTime)
format=SUGAR.expressions.userPrefs.datef+" "+SUGAR.expressions.userPrefs.timef;else
format=SUGAR.expressions.userPrefs.datef;}
var out="";for(var i=0;i<format.length;i++){var c=format.charAt(i);switch(c){case'm':var m=date.getMonth()+1;out+=m<10?"0"+m:m;break;case'd':var d=date.getDate();out+=d<10?"0"+d:d;break;case'Y':out+=date.getFullYear();break;case'H':case'h':var h=date.getHours();if(c=="h")h=h>12?h-12:h;out+=h<10?"0"+h:h;break;case'i':var m=date.getMinutes();out+=m<10?"0"+m:m;break;case'a':if(date.getHours()<12)
out+="am";else
out+="pm";break;case'A':if(date.getHours()<12)
out+="AM";else
out+="PM";break;default:out+=c;}}
return out;},roundTime:function(date){var min=date.getMinutes();if(min<1){min=0;}
else if(min<16){min=15;}
else if(min<31){min=30;}
else if(min<46){min=45;}
else{min=0;date.setHours(date.getHours()+1)}
date.setMinutes(min);return date;},getUserTime:function(){var date=new Date();if(SUGAR.expressions.userPrefs&&typeof(SUGAR.expressions.userPrefs.gmt_offset)!='undefined'){var offset=SUGAR.expressions.userPrefs.gmt_offset;date.setMinutes(date.getMinutes()+(date.getTimezoneOffset()+offset));}
return date;}};})();