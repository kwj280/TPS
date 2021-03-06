
/*
Function: Get Arg from URI
Source: http://www.bloggingdeveloper.com/post/JavaScript-QueryString-ParseGet-QueryString-with-Client-Side-JavaScript.aspx
*/
function getQuerystring(key, default_)
{
  if (default_==null) default_=""; 
  key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
  var qs = regex.exec(window.location.href);
  if(qs == null)
    return default_;
  else
    return qs[1];
}

function startClock(target)
{
var today=new Date();
var h=today.getHours();
var m=today.getMinutes();
var s=today.getSeconds();
// add a zero in front of numbers<10
m=checkTime(m);
s=checkTime(s);
document.getElementById('txt').innerHTML=h+":"+m+":"+s;
t=setTimeout(function(){startTime()},500);
}


function startTimer(target)
{
    var initialTime = Date.now();

    function checkTime(){
      var timeDifference = Date.now() - initialTime;
      var formatted = convertTime(timeDifference);
      document.getElementById(target).innerHTML = '' + formatted;
    }

    function convertTime(miliseconds) {
      var totalSeconds = Math.floor(miliseconds/1000);
      var minutes = Math.floor(totalSeconds/60);
      var seconds = totalSeconds - minutes * 60;
      return checkTimeFormat(minutes) + ':' + checkTimeFormat(seconds);
    }
    window.setInterval(checkTime, 500);
}

function checkTimeFormat(i)
{
if (i<10)
  {
  i="0" + i;
  }
return i;
}

function GetEAS(target,basedir,station){
    //atom = typeof atom !== 'undefined' ? atom : "http://www.emergencyalert.alberta.ca/aeapublic/feed.atom";
    atom = basedir + "TPSBIN/XML/Emergency.php";
    //target = typeof target !== 'undefined' ? target : "EAS";
    $.ajax({
        url: atom,
        data: {
            "station":station,
            "format":"html",
        },
    })
    .done(function (data) {
        var tgt_EAS = '#'.concat(target);
        $(tgt_EAS).html(data)
        return true;
    })
    .fail(function (textStatus) {
        $(tgt_EAS).innerHTML("<span class='ui-state-error'>Could not load EAS data (Got " + textStatus + ")");
        return false;
    });
}