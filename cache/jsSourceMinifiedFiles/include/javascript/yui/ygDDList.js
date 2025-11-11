function ygDDList(id,sGroup){if(id){this.init(id,sGroup);this.initFrame();}
var s=this.getDragEl().style;s.borderColor="transparent";s.backgroundColor="#f6f5e5";s.opacity=0.76;s.filter="alpha(opacity=76)";}
ygDDList.prototype=new YAHOO.util.DDProxy();ygDDList.prototype.borderDiv=null;ygDDList.prototype.originalDisplayProperties=Array();ygDDList.prototype.dashletID=null;ygDDList.prototype.needsReloadAfterDrop=false;ygDDList.prototype.startDrag=function(x,y){var dragEl=this.getDragEl();var clickEl=this.getEl();this.needsReloadAfterDrop=false;var chartContainer=YAHOO.util.Dom.getElementsByClassName('chartContainer','div',clickEl);if(chartContainer.length!=0){var cee_canvas=YAHOO.util.Dom.get(this.dashletID+'-canvas');if(typeof cee_canvas!='undefined'&&cee_canvas){var canvas_objects=YAHOO.util.Dom.getElementsBy(function(el){return true;},'OBJECT',cee_canvas);if(canvas_objects.length!=0){this.needsReloadAfterDrop=true;}}
chartContainer.innerHTML='';}
dragEl.innerHTML=clickEl.innerHTML;dragElObjects=dragEl.getElementsByTagName('object');dragEl.className=clickEl.className;dragEl.style.color=clickEl.style.color;dragEl.style.border="1px solid #aaa";clickElRegion=YAHOO.util.Dom.getRegion(clickEl);this.borderDiv=document.createElement('div');this.borderDiv.style.height=(clickElRegion.bottom-clickElRegion.top)+'px';this.borderDiv.style.border='2px dashed #cccccc';for(i in clickEl.childNodes){if(typeof clickEl.childNodes[i].style!='undefined'){this.originalDisplayProperties[i]=clickEl.childNodes[i].style.display;clickEl.childNodes[i].style.display='none';}}
clickEl.appendChild(this.borderDiv);};ygDDList.prototype.endDrag=function(e){var clickEl=this.getEl();clickEl.removeChild(this.borderDiv);for(i in clickEl.childNodes){if(typeof clickEl.childNodes[i].style!='undefined'){clickEl.childNodes[i].style.display=this.originalDisplayProperties[i];}}
if(this.clickHeight)
clickEl.style.height=this.clickHeight;else
clickEl.style.height='';if(this.clickBorder)
clickEl.style.border=this.clickBorder;else
clickEl.style.border='';dragEl=this.getDragEl();dragEl.innerHTML='';this.afterEndDrag(e);};ygDDList.prototype.afterEndDrag=function(e){}
ygDDList.prototype.onDrag=function(e,id){};ygDDList.prototype.onDragOver=function(e,id){var el;if("string"==typeof id){el=YAHOO.util.DDM.getElement(id);}else{el=YAHOO.util.DDM.getBestMatch(id).getEl();}
dragEl=this.getDragEl();elRegion=YAHOO.util.Dom.getRegion(el);var mid=YAHOO.util.DDM.getPosY(el)+(Math.floor((elRegion.bottom-elRegion.top)/ 2));if(YAHOO.util.DDM.getPosY(dragEl)<mid){var el2=this.getEl();var p=el.parentNode;p.insertBefore(el2,el);}
if(YAHOO.util.DDM.getPosY(dragEl)>=mid){var el2=this.getEl();var p=el.parentNode;p.insertBefore(el2,el.nextSibling);}};ygDDList.prototype.onDragEnter=function(e,id){};ygDDList.prototype.onDragOut=function(e,id){}
function ygDDListBoundary(id,sGroup){if(id){this.init(id,sGroup);this.isBoundary=true;}}
ygDDListBoundary.prototype=new YAHOO.util.DDTarget();