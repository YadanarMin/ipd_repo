@extends('layouts.baselayout')
@section('title', 'CCC - Correlation sample')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/script.js"></script>
<script type="text/javascript" src="../public/js/correaltionSample.js"></script>

<script src="https://d3js.org/d3.v4.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/1.2.0/sigma.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/1.2.0/plugins/sigma.parsers.json.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/1.2.0/plugins/sigma.renderers.edgeLabels.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">

<style>
/*#datatip{
  padding: 10px 20px;
  font-family: Verdana, arial;
  width: 240px;
  height: auto;
  position: absolute;
  border: 1px solid black;
  background-color: white;
  border-radius: 5px;
  opacity: 0.0;
  left: 0;
  top: 0;
  background-position: 10px 7px, 220px 7px;
  background-size: 50px auto, 50px auto;
  background-repeat: no-repeat , no-repeat;
}

#datatip h2 {
  font-size: 18px;
  padding-bottom: 5px;
  padding-left: 5px;
  margin-left: 50px;
}

#datatip p {
  font-size: 14px;
}

#sidebar {
  width: 250px;
  float: left;
}

#side_setting{
  padding: 5px 10px;
  font-family: Verdana, arial;
  width: auto;
  height: 140px;
  border: 1px solid black;
  background-color: white;
  border-radius: 5px;
  left: 0;
  top: 0;
}

#side_setting h2{
  font-size: 18px;
  padding-bottom: 5px;
  border-bottom: 2px solid gray
}

#side_search{
  padding: 5px 10px;
  font-family: Verdana, arial;
  width: auto;
  height: 100px;
  border: 1px solid black;
  background-color: white;
  border-radius: 5px;
  left: 0;
  top: 0;
}

#side_search h2{
  font-size: 18px;
  padding-bottom: 5px;
  border-bottom: 2px solid gray
}

#side_data{
  padding: 5px 10px;
  font-family: Verdana, arial;
  width: auto;
  height: 540px;
  border: 1px solid black;
  background-color: white;
  border-radius: 5px;
  background-position: 160px 7px;
  background-size: 70px auto;
  background-repeat: no-repeat;
  left: 0;
  top: 0;
  opacity: 0.0;
}

#side_data h2{
  font-size: 18px;
  padding-bottom: 5px;
  border-bottom: 2px solid gray;
  margin-right: 100px;
}

#side_data h3 {
  font-size: 14px;
}*/

/*
.links line {
  stroke: #999;
  stroke-opacity: 0.6;
}
.nodes circle {
  stroke: #fff;
  stroke-width: 1.5px;
}
*/
#container {
    max-width: 960px;
    height: 480px;
    margin: auto;
}
#network {
  width:  400px;
  height: 400px;
  border: 1px solid #000;
}
</style>
@endsection

@section('content')
<div class="main-content">

    <div style="display:flex;">
        <h3>相関図</h3>
        <!--<input style="margin:20px 0 10px 10px;" type="button" name="btnSaveTemplate" class="btn btn-info" id="btnSaveTemplate" value="表示" onclick="DisplayCorrelation()"/>-->
    </div>

    <!--<ul id="template-type">-->
    <!--    <label class="custom-ul-label">ライブラリを選択</label>-->
    <!--    <li><input type="radio" name="templateType" value="1">&nbsp;&nbsp;D3.js</li>-->
    <!--    <li><input type="radio" name="templateType" value="2">&nbsp;&nbsp;sigma.js</li>-->
    <!--    <li><input type="radio" name="templateType" value="3">&nbsp;&nbsp;vis.js</li>-->
    <!--</ul>-->

    <div id="correlation_fields" style="height:67vh;width:130vh;border: 1px black;">
        
        <!--<svg width="1400" height="600" style="background-color:#fff"></svg>-->
        
        <!-- カーソルを合わせたときに表示する情報領域-->
        <!--<div id="datatip">-->
        <!--  <h2></h2>-->
        <!--  <p></p>-->
        <!--</div>-->
        
        <!-- サイドバー設定-->
        <!--<div id="sidebar">-->
        
        <!--  <section id= "side_setting">-->
        <!--    <h2>Setting</h2>-->
        <!--    <input id="rng_zoom" type="range" min="-2" max="2" value="0" step="0.1">-->
        <!--    Zoom: <span id="r_zoom_text">100</span>-->
        <!--    <input id="rng_link" type="range" min="0" max="31" value="0" step="1">-->
        <!--    Link: <span id="r_link_text">0</span>-->
        <!--    <br>-->
        <!--    Group  : <input id="num_group" type="number" min="0" max="12" value="" step="1">-->
        <!--    <input type="submit" value="set" id="btn_group">-->
        <!--  </section>-->
        
        <!--  <section id= "side_search">-->
        <!--    <h2>Search</h2>-->
        <!--    <input type="text" name="group_id" id="txt_search">-->
        <!--    <input type="submit" value="検索" id="btn_search" onclick="OnClickSearch();">-->
        <!--  </section>-->
        
        <!--  <section id= "side_data">-->
        <!--    <h2>Data</h2>-->
        <!--    <h3></h3>-->
        <!--    <iframe id="data_memo" seamless height=300 width=220 sandbox="allow-same-origin"></iframe>-->
        <!--    <iframe id="data_relation" seamless height=140 width=220 sandbox="allow-same-origin"></iframe>-->
        <!--  </section>-->
        <!--</div>-->
    </div>
 
</div>
@endsection