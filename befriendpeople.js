// todo http://stackoverflow.com/a/10906320
// go to https://www.facebook.com/HayDayOfficial , and find a post that many people likes,
// then find the @list of people who likes this@ url, something like
// https://www.facebook.com/browse/likes?id=722924617792108&actorid=297050457046195
// then scroll to the bottom of the page..
// then run this code
(function ClickAllAddFriendsButtons($max/*=20*/){
var LikeButtons=document.querySelectorAll('.fbProfileBrowserListContainer')[0].querySelectorAll('.FriendRequestAdd');
var LikeButton=null;
var i=0;
$max=($max==undefined? 20:$max);
for(i=0;i<LikeButtons.length;++i)
{
LikeButtons.item(i).click();
//console.log('clicked!');
//break;

  if($i>=$max){
    break;
  };
}
console.log('clicked '+i+' times!');

})();
// then refresh the page, and do again! repeat!
//todo: do refresh & repeat automatically? xhr? iframe? 
// todo: test the claims here http://www.howtofacebooktipstricks.com/2014/01/How-Send-Request-Facebook-Blocking.html ,
// and if true, implement and automate?
