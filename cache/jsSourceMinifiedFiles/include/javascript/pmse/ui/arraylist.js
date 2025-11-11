var PMSE=PMSE||{};PMSE.ArrayList=function(){var elements=[],size=0,index,i;return{id:Math.random(),get:function(index){return elements[index];},insert:function(item){elements[size]=item;size+=1;return this;},insertAt:function(item,index){elements.splice(index,0,item);size=elements.length;return this;},remove:function(item){index=this.indexOf(item);if(index===-1){return false;}
size-=1;elements.splice(index,1);return true;},getSize:function(){return size;},isEmpty:function(){return size===0;},indexOf:function(item){for(i=0;i<size;i+=1){if(item.id===elements[i].id){return i;}}
return-1;},find:function(attribute,value){var i,current;for(i=0;i<elements.length;i+=1){current=elements[i];if(current[attribute]===value){return current;}}
return undefined;},contains:function(item){if(this.indexOf(item)!==-1){return true;}
return false;},sort:function(compFunction){var returnValue=false;if(compFunction){elements.sort(compFunction);returnValue=true;}
return returnValue;},asArray:function(){return elements;},getFirst:function(){return elements[0];},getLast:function(){return elements[size-1];},popLast:function(){var lastElement;size-=1;lastElement=elements[size];elements.splice(size,1);return lastElement;},getDimensionLimit:function(){var result=[100000,-1,-1,100000],objects=[undefined,undefined,undefined,undefined];for(i=0;i<size;i+=1){if(result[0]>elements[i].y){result[0]=elements[i].y;objects[0]=elements[i];}
if(result[1]<elements[i].x+elements[i].width){result[1]=elements[i].x+elements[i].width;objects[1]=elements[i];}
if(result[2]<elements[i].y+elements[i].height){result[2]=elements[i].y+elements[i].height;objects[2]=elements[i];}
if(result[3]>elements[i].x){result[3]=elements[i].x;objects[3]=elements[i];}}
return result;},clear:function(){if(size!==0){elements=[];size=0;}
return this;},getCanvas:function(){return(this.getSize()>0)?this.get(0).getCanvas():undefined;}};};if(typeof exports!=='undefined'){module.exports=PMSE.ArrayList;}