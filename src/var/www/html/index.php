<html>
 <head>
  <link rel="stylesheet" href="static/bootstrap.min.css">
  <script src="static/jquery-3.1.0.min.js"></script>
  <script src="static/bootstrap.min.js"></script>
 </head>
 <body> 
  <style>
  .btn { padding-top: 20px; padding-bottom: 20px; }
  </style>
  <div class="container">
   <h1 class="text-center">Target Camera Prototype</h1>
   <div class="row">
    <div class="col-xs-4">
     <input id="btn-prev" class="btn btn-default btn-block" type="button" value="Prev">
    </div>
    <div class="col-xs-4">
     <input id="btn-compare" class="btn btn-default btn-block" type="button" value="Compare">
    </div>
    <div class="col-xs-4">
     <input id="btn-next" class="btn btn-default btn-block" type="button" value="Next">
    </div>
   </div>
   <div class="row">
    <div class="col-xs-12">
     <input id="btn-capture" class="btn btn-primary btn-block" type="button" value="Capture" >
     <input id="btn-capture-loading" class="btn btn-info btn-block disabled" style="display: none;" disabled type="button" value="Please wait" >
   </div>

   <div id="img-con">
    <h3 class="text-center">Image #<span id="badge"></span></h3>
    <div class="row">
     <div class="col-xs-12">
      <img class="img-responsive center-block" id="img"/>
     </div>
    </div>
   </div>

   <div id="img2-con" style="display: none">
    <h3 class="text-center">Image #<span id="badge2"></span></h3>
    <div class="row">
     <div class="col-xs-12">
      <img class="img-responsive center-block" id="img2"/>
     </div>
    </div>
   </div>

  </div>
  <script>

function paddy(n,padding) {
 var pad_char = '0';
 var pad = new Array(1 + padding).join(pad_char);
 return (pad + n).slice(-pad.length);
}

var g = {};
g.compareMode = false;
g.compareStopCounter = 0;
g.onNewData = function(data) {
  this.last = data;
  g.setCurrentData(data);
};
g.setCurrentData = function(data) {
  var compareContinue = g.compareMode;
  g.compareStop();

  g.current = data;
  g.prev = g.getById(data.id-1);
  $("#badge").text(data.id);
  if(data.id > 0) {
    $("#img").attr("src", data.src);
  }

  if(compareContinue) {
    g.compareStart();
  }
};
g.move = function(id) {
  var newData = g.getById(id);
  if(newData != null)
  {
   g.setCurrentData(newData);
  }
}
g.getById = function(id) {
 if(id <= 0) return null;
 if(id > this.last.id) return null;
 return {
  id: id,
  src: "img/i" + paddy(id, 5) + ".jpg"
 };
};
g.loading = function(isLoading) {
  $("#btn-capture").toggle(!isLoading);
  $("#btn-capture-loading").toggle(isLoading);
};
g.compareSpeed = 800;
g.compareStep = function(step, stopCounter) {
  if(g.compareStopCounter > stopCounter) return;
  $("#img-con").toggle(step == 1);
  $("#img2-con").toggle(step == 0);
  step = (step + 1) % 2;
  setTimeout(function() { g.compareStep(step, stopCounter); }, g.compareSpeed);
};
g.compareStart = function() {
  g.compareMode = true;
  $("#btn-compare").addClass("btn-warning").removeClass("btn-default");
  $("#img2").attr("src", g.prev.src);
  $("#badge2").text(g.prev.id);
  setTimeout(function() { g.compareStep(0, g.compareStopCounter); }, g.compareSpeed);
};
g.compareStop = function () {
  g.compareMode = false;
  g.compareStopCounter = g.compareStopCounter + 1;
  $("#btn-compare").addClass("btn-default").removeClass("btn-warning");
  $("#img2-con").hide();
  $("#img-con").show();
};

$(function() {

  $.ajax({
   url: "action.php", 
   success: function(data) { g.onNewData(data); },
   cache: false
  });

  $("#btn-capture").click(function() {
    g.loading(true);
    $.ajax({
     url: "action.php?new", 
     success: function(data) { g.onNewData(data); g.loading(false); },
     cache: false
    });
  });

  $("#btn-compare").click(function() {
    if(g.compareMode)
    {
     g.compareStop();
    } else {
     g.compareStart();
    }
  });

  $("#btn-prev").click(function() { g.move(g.current.id - 1); });
  $("#btn-next").click(function() { g.move(g.current.id + 1); });

});
  </script>
 </body>
</html>
