//goto https://www.facebook.com/HayDayOfficial
// find a post that many has commented on
// go to bottom of list
// run the code below...
// 



// url type 1: 
// <a class="UFICommentActorName _5f0v" data-hovercard="/ajax/hovercard/hovercard.php?id=100000614934353&amp;extragetparams=%7B%22hc_location%22%3A%22ufi%22%7D" data-ft="{&quot;tn&quot;:&quot;;&quot;}" dir="ltr" href="https://www.facebook.com/profile.php?id=100000614934353&amp;fref=ufi" data-reactid=".1c.1:3:1:$comment723624701055433_723709477713622:0.0.$right.0.$left.0.0.$author.0" aria-owns="js_25" aria-haspopup="true" id="js_26" tabindex="0">Ate Ling</a>
// https://www.facebook.com/profile.php?id=100000614934353
// url type 2:
// UFICommentActorName
// <a class="UFICommentActorName" data-hovercard="/ajax/hovercard/hovercard.php?id=100000943029542&amp;extragetparams=%7B%22hc_location%22%3A%22ufi%22%7D" data-ft="{&quot;tn&quot;:&quot;;&quot;}" dir="ltr" href="https://www.facebook.com/fe.nabanes?fref=ufi" data-reactid=".1c.1:3:1:$comment723624701055433_723664314384805:0.0.$right.0.$left.0.0.$author.0" aria-owns="js_53" aria-haspopup="true" id="js_54" tabindex="0">Fe Nabanes</a>
// https://www.facebook.com/fe.nabanes?fref=ufi

(function(){
var AllComments=document.querySelectorAll(".UFICommentActorName");
var ret={data:[]};
var tmp=null,current=null;
var i=0;
var alreadyExists=function(uid){
var i=0;
for(i=0;i<ret["data"].length;++i){
if(ret["data"][i]["uid"]==uid){return true;}
}
return false;
};
for(;i<AllComments.length;++i){
current=AllComments.item(i);
tmp={};
tmp["uid"]=/id\=(\d+)/gi.exec(current.getAttribute("data-hovercard").toString())[1];
if(alreadyExists(tmp["uid"])){continue;};
tmp["name"]=current.textContent;
tmp["pic_square"]=current.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previousSibling.getElementsByTagName("img")[0].src;
//^wonder how long that code will work...
tmp["first_name"]=i.toString()+" "+tmp["name"].split(" ")[0];//<<that code is not perfect, as first_name can be stuff like "first_name": "Kevin R." ...
ret["data"].push(tmp);
}


return JSON.stringify(ret);

})();
