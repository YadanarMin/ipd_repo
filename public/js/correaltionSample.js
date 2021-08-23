/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    displayTypeD3();
});

function DisplayCorrelation(){
    var templateType = $("input[name='templateType']:checked").val();
    
    if(templateType == '1'){
        // alert("ライブラリ：D3.js");
        displayTypeD3();
    }else if(templateType == '2'){
        // alert("ライブラリ：sigma.js");
        displayTypeSigma();
    }else if(templateType == '3'){
        // alert("ライブラリ：vis.js");
        displayTypeVis();
    }else{
        alert("ライブラリを選択してください。");
    }
    
}

function displayTypeD3(){
    var appendStr = "";
    
    appendStr += "<svg width='1600' height='800' style='background-color:#fff'></svg>";
    
    $("#correlation_fields svg").remove();
    $("#correlation_fields div").remove();
    $("#correlation_fields").append(appendStr);
    

    //*****************************************************************
    // var svg = d3.select("svg"),
    //     width = +svg.attr("width"),
    //     height = +svg.attr("height");
    // console.log(svg);
    // var color = d3.scaleOrdinal(d3.schemeCategory20);
    
    // var simulation = d3.forceSimulation()
    //     .force("link", d3.forceLink().id(function(d) { return d.id; }))
    //     .force("charge", d3.forceManyBody())
    //     .force("center", d3.forceCenter(width / 2, height / 2));
    
    // d3.json("./../app/Exports/miserables.json", function(error, graph) {
    //   if (error) throw error;
    
    //   var link = svg.append("g")
    //       .attr("class", "links")
    //     .selectAll("line")
    //     .data(graph.links)
    //     .enter().append("line")
    //       .attr("stroke-width", function(d) { return Math.sqrt(d.value); });
    
    //   var node = svg.append("g")
    //       .attr("class", "nodes")
    //     .selectAll("circle")
    //     .data(graph.nodes)
    //     .enter().append("circle")
    //       .attr("r", 5)
    //       .attr("fill", function(d) { return color(d.group); })
    //       .call(d3.drag()
    //           .on("start", dragstarted)
    //           .on("drag", dragged)
    //           .on("end", dragended));
    
    //   node.append("title")
    //       .text(function(d) { return d.id; });
    
    //   simulation
    //       .nodes(graph.nodes)
    //       .on("tick", ticked);
    
    //   simulation.force("link")
    //       .links(graph.links);
    
    //   function ticked() {
    //     link
    //         .attr("x1", function(d) { return d.source.x; })
    //         .attr("y1", function(d) { return d.source.y; })
    //         .attr("x2", function(d) { return d.target.x; })
    //         .attr("y2", function(d) { return d.target.y; });
    
    //     node
    //         .attr("cx", function(d) { return d.x; })
    //         .attr("cy", function(d) { return d.y; });
    //   }
    // });
    
    // function dragstarted(d) {
    //   if (!d3.event.active) simulation.alphaTarget(0.3).restart();
    //   d.fx = d.x;
    //   d.fy = d.y;
    // }
    
    // function dragged(d) {
    //   d.fx = d3.event.x;
    //   d.fy = d3.event.y;
    // }
    
    // function dragended(d) {
    //   if (!d3.event.active) simulation.alphaTarget(0);
    //   d.fx = null;
    //   d.fy = null;
    // }

    //***************************************************************** 
    // var svg = d3.select("svg"),
    //     width = +svg.attr("width"),
    //     height = +svg.attr("height");
    
    // var color = d3.scaleOrdinal(d3.schemeCategory20);
    
    // var simulation = d3.forceSimulation()
    //     .force("link", d3.forceLink().id(function(d) { return d.id; }))
    //     .force("charge", d3.forceManyBody())
    //     .force("center", d3.forceCenter(width / 2, height / 2));
    
    // //"svg"にZoomイベントを設定
    // var zoom = d3.zoom()
    //   .scaleExtent([1/4,4])
    //   .on('zoom', SVGzoomed);
    
    // svg.call(zoom);
    
    // //"svg"上に"g"をappendしてdragイベントを設定
    // var g = svg.append("g")
    //   .call(d3.drag()
    //   .on('drag',SVGdragged))
    
    // function SVGzoomed() {
    //   g.attr("transform", d3.event.transform);
    // }
    
    // function SVGdragged(d) {
    //   d3.select(this).attr('cx', d.x = d3.event.x).attr('cy', d.y = d3.event.y);
    // };
    
    // d3.json("./../app/Exports/miserables.json", function(error, graph) {
    //   if (error) throw error;
    
    //   var link = g.append("g")  //svg⇒gに
    //       .attr("class", "links")
    //     .selectAll("line")
    //     .data(graph.links)
    //     .enter().append("line")
    //       .attr("stroke","#999")  //輪郭線の色指定追加
    //       .attr("stroke-width", function(d) { return Math.sqrt(d.value); });
    
    //   var node = g.append("g")  //svg⇒gに
    //       .attr("class", "nodes")
    //     .selectAll("circle")
    //     .data(graph.nodes)
    //     .enter().append("circle")
    //       .attr("r", 5)
    //       .attr("fill", function(d) { return color(d.group); })
    //       .call(d3.drag()
    //           .on("start", dragstarted)
    //           .on("drag", dragged)
    //           .on("end", dragended));
    
    //   node.append("title")
    //       .text(function(d) { return d.id; });
    
    //   simulation
    //       .nodes(graph.nodes)
    //       .on("tick", ticked);
    
    //   simulation.force("link")
    //       .links(graph.links);
    
    //   function ticked() {
    //     link
    //         .attr("x1", function(d) { return d.source.x; })
    //         .attr("y1", function(d) { return d.source.y; })
    //         .attr("x2", function(d) { return d.target.x; })
    //         .attr("y2", function(d) { return d.target.y; });
    
    //     node
    //         .attr("cx", function(d) { return d.x; })
    //         .attr("cy", function(d) { return d.y; });
    //   }
    // });
    
    // function dragstarted(d) {
    //   if (!d3.event.active) simulation.alphaTarget(0.3).restart();
    //   d.fx = d.x;
    //   d.fy = d.y;
    // }
    
    // function dragged(d) {
    //   d.fx = d3.event.x;
    //   d.fy = d3.event.y;
    // }
    
    // function dragended(d) {
    //   if (!d3.event.active) simulation.alphaTarget(0);
    //   d.fx = null;
    //   d.fy = null;
    // }
    
    
    //*****************************************************************
    var sidedata = d3.select("#side_data");
    
    //カーソルを合わせたときに表示する情報領域
    var datatip = d3.select("#datatip");
    
    var svg = d3.select("svg"),
        width = +svg.attr("width"),
        height = +svg.attr("height");
    
    var color = d3.scaleOrdinal(d3.schemeCategory20);
    
    var simulation = d3.forceSimulation()
          .velocityDecay(0.3)                                                     //摩擦
        .force('charge', d3.forceManyBody())                                      //詳細設定は後で
        .force('link', d3.forceLink().id(function(d) { return d.id; }))          //詳細設定は後で
        .force('colllision',d3.forceCollide(40))                                 //nodeの衝突半径：Nodeの最大値と同じとする
        .force('positioningX',d3.forceX())                                        //詳細設定は後で
        .force('positioningY',d3.forceY())                                        //詳細設定は後で
        .force('center', d3.forceCenter(width / 2, height / 2));                  //重力の中心
    
    //使用するnode図形計上形状定義(中心座標は(0,0))
    var Defs = svg.append("defs")
    //Circle
    var figCircle = Defs.append('circle')
          .attr("id","circle")
          .attr('r', 20),   //5⇒20
        lenCircle = figCircle.node().getTotalLength(),
        spCircle = figCircle.node().getPointAtLength(0);
    //Rect
    var figRect = Defs.append('rect')
          .attr("id","rect")
          .attr('width', 40)
          .attr('height', 30)
          .attr('rx', 7)  //角を丸める
          .attr('ry', 7)  //角を丸める
          .attr('x', -(40/2))  //circleと中心を合わせる
          .attr('y', -(30/2)),  //circleと中心を合わせる
        lenRect = figRect.node().getTotalLength(),
        spRect  = figRect.node().getPointAtLength(0);
    
    //Ellipse
    var figEllipse = Defs.append('ellipse')
          .attr("id","ellipse")
          .attr('rx', 30)
          .attr('ry', 20),
        lenEllipse = figEllipse.node().getTotalLength(),
        spEllipse  = figEllipse.node().getPointAtLength(0);
    
    // hexagon ※pointsは反時計回りで定義すると他の図形と記述の順番の整合が取れる
    var figHexagon = Defs.append('polygon')
          .attr("id","hexagon")
          .attr('points', "0,20 -17.3,10 -17.3,-10 0,-20 17.3,-10 17.3,10"),
        lenHexagon = figHexagon.node().getTotalLength(),
        spHexagon  = figHexagon.node().getPointAtLength(0);
    
    //"svg"にZoomイベントを設定
    var zoom = d3.zoom()
      .scaleExtent([1/4,4])
      .on('zoom', SVGzoomed);
    
    svg.call(zoom);
    
    //"svg"上に"g"をappendしてdragイベントを設定
    var g = svg.append("g")
      .call(d3.drag()
      .on('drag',SVGdragged))
    
    function SVGzoomed() {
      g.attr("transform", d3.event.transform);
    }
    
    function SVGdragged(d) {
      d3.select(this).attr('cx', d.x = d3.event.x).attr('cy', d.y = d3.event.y);
        };
    
    //Jsonfileのデータを保持
    var jsondata;
    
    d3.json("./../app/Exports/cccdata.json", function(error, graph) {  //miserables2.jsonに変更
      if (error) throw error;
    
    //jsondataに値を格納
     jsondata = graph;
    
    //Linksの定義
      var links = g.append("g")  //svg⇒gに
          .attr("class", "links")
        .selectAll("g")
        .data(graph.links)
        .enter()
        .append("g")
          .attr("class", "linkArrow")
          .attr("fill", "#999")
          .attr("stroke","#999")  //輪郭線の色指定追加
          .on('mouseover', function(d){
                d3.select(this).attr('stroke', 'red'); //カーソルが合ったら赤に
                datatip.style("left", d3.event.pageX + 20 + "px")
                        .style("top", d3.event.pageY + 20 + "px")
                        .style("z-index", 0)
                        .style("opacity", 1)
                        .style('background-image',function(){
                          if (typeof d.source.image === "undefined" ){
                            if (typeof d.target.image === "undefined" ){
                              return  'url("image/unknown.png"), url("image/unknown.png")'
                            }
                            else {
                              return  'url("image/unknown.png"), ' + 'url("'+ d.target.image + '")'
                            }
                          }
                          else {
                            if (typeof d.target.image === "undefined" ){
                              return 'url("'+ d.source.image + '"), ' + 'url("image/unknown.png")'
                            }
                            else {
                              return 'url("'+ d.source.image + '"), url("'+ d.target.image + '")'
                            }
                          }
                        });
    
                datatip.select("h2")
                        .style("border-bottom", "2px solid " +color(d.source.group))
                        .style("margin-right", "50px")
                        .text("value:" + d.value);
    
                datatip.select("p")
                        .text(d.source.id + " to " + d.target.id);
            })
          .on('mousemove', function(){
                datatip.style("left", d3.event.pageX + 20 + "px")
                        .style("top", d3.event.pageY + 20 + "px")
             })
          .on('mouseout', function(){
                d3.select(this).attr('stroke', "#999"); //カーソルが外れたら元の色に
                datatip.style("z-index", -1)
                      .style("opacity", 0)
            })
          .call(d3.drag()             //無いとエラーになる。。
              .on('start', dragstarted)
              .on('drag', dragged)
              .on('end', dragended));
    
    //Markerの定義
      var marker = links.append("marker")
          .attr("id", function(d) { return "mkr" + d.source+d.target })
          .attr("viewBox", "0 0 20 20")
          .attr("markerWidth", 7)
          .attr("markerHeight", 7)
          .attr("refX", 19)
          .attr("refY", 10)
          .attr("orient", "auto-start-reverse")
    
      marker.append("path")
          .attr("d", "M0.5,0.75L18.88,10L0.5,19.25z")
          .attr("fill","#ddd")          //背景色と同じ
          .attr("stroke-width", 1)
    
    //linkの定義
      var link = links.append("path")    //line⇒Path
    //      .attr("stroke","#999")  //輪郭線の色指定追加
          .attr("marker-start",function(d) { return "url(#mkr" +  d.source+d.target  + ")"})
          .attr("marker-end",function(d) { return "url(#mkr" + d.source+d.target  + ")"})
          .attr("fill","none")    //塗りつぶしなしを追加
          .attr("stroke-width", function(d) { return Math.sqrt(d.value); })
          .attr("stroke-dashoffset", 0)
    
    // nodeの定義
      var node = g.append('g')
          .attr('class', 'nodes')
        .selectAll('g')
        .data(graph.nodes)
        .enter()
        .append('g')
        .on('mousedown', set_sidedata)
        .call(d3.drag()
            .on('start', dragstarted)
            .on('drag', dragged)
            .on('end', dragended));
    
    // node 図形の定義
      node.append("use")
        .attr("xlink:href",function(d) {return "#"+ nodeTypeID(d.group)})        //図形判定
        .attr('stroke', '#ccc')
        .attr('fill', function(d) { return color(d.group); })
        .style('stroke-width', '2')  //線の太さ
        .style('stroke-dasharray',function(d) {return stroke_dasharrayCD(d)})  //破線判定
        .on('mouseover', function(d){
              d3.select(this).attr('fill', 'red'); //カーソルが合ったら赤に
    
              datatip.style("left", d3.event.pageX + 20 + "px")
                      .style("top", d3.event.pageY + 20 + "px")
                      .style("z-index", 0)
                      .style("opacity", 1)
                      .style('background-image', function() {if (typeof d.image === "undefined" ) {return 'url("image/unknown.png")' } else { return 'url("'+ d.image + '")'}})
    
              datatip.select("h2")
                      .style("border-bottom", "2px solid " +color(d.group))
                      .style("margin-right", "0px")
                      .text(d.id);
    
              datatip.select("p")
                      .text("グループID:" + d.group );
          })
        .on('mousemove', function(){
              datatip.style("left", d3.event.pageX + 20 + "px")
                      .style("top", d3.event.pageY + 20 + "px")
          })
        .on('mouseout', function(){
              d3.select(this).attr('fill', function(d) { return color(d.group); })  //カーソルが外れたら元の色に
    
              datatip.style("z-index", -1)
                     .style("opacity", 0)
          })
    //node textの定義
      node.append('text')
        .attr('text-anchor', 'middle')
        .attr('fill', 'black')
        .style('pointer-events', 'none')
        .attr('font-size', function(d) {return '10px'; }  )
        .attr('font-weight', function(d) { return 'bold'; }  )
        .text(function(d) { return d.id; });
    
      //node imageの定義
        node.append('image')
          .attr('xlink:href', function(d) {if (typeof d.image === "undefined") {return  "image/unknown.png" } else { return d.image }})
          .attr('width',30)
          .attr('x',-15)
          .attr('y',-40)
    
    /*表示がうっとおしいので削除
      node.append("title")
          .text(function(d) { return "node id:" + d.id; });
    */
      simulation
          .nodes(graph.nodes)
          .on("tick", ticked);
    
      simulation.force("link")
          .distance(250) //Link長
          .links(graph.links);
    
      simulation.force('charge')
          .strength(function(d) {return -500})              //node間の力
    
      simulation.force('positioningX')                      //X方向の中心に向けた引力
          .strength(0.04)
    
      simulation.force('positioningY')                      //Y方向の中心に向けた引力
          .strength(0.04)
    
      function ticked() {
    
        link
            .attr("d", linkArc)
    
    /*
            .attr("x1", function(d) { return d.source.x; })
            .attr("y1", function(d) { return d.source.y; })
            .attr("x2", function(d) { return d.target.x; })
            .attr("y2", function(d) { return d.target.y; });
    */
        node
            .attr("cx", function(d) { return d.x; })
            .attr("cy", function(d) { return d.y; })
            .attr('transform', function(d) {return 'translate(' + d.x + ',' + d.y + ')'}) //nodesの要素が連動して動くように設定
      }
    });
    
    //Sidedataに情報をセット
    function set_sidedata(d){
    //画像表示
      sidedata.style("z-index", 0)
              .style("opacity", 1)
              .style('background-image', function() {if (typeof d.image === "undefined" ) {return 'url("image/unknown.png")' } else { return 'url("'+ d.image + '")'}})
    
    //node名表示
      sidedata.select("h3")
              .text(d.id)
    
    //nodeのメモ表示
      sidedata.select("#data_memo")
              .attr("srcdoc",function(){
                if (typeof d.memo === "undefined" ){
                  return "<p style='font-size: 11px'></p>"
                }
                else {
                  return "<p style='font-size: 11px'>" +d.memo.replace(/\n/g,"<br>") +"</p>"
                }
              })
    
    //nodeにつながるLink表示
      sidedata.select("#data_relation")
              .attr("srcdoc",function(){
                var r_value ="",
                    r_target =jsondata.links.filter(function(item,index){if(item.source.id == d.id ) return true}),
                    r_source =jsondata.links.filter(function(item,index){if(item.target.id == d.id ) return true});
                for (var key in r_target) {
                  r_value = r_value + "to: " + r_target[key].target.id + "<br>"
                }
                for (var key in r_source) {
                  r_value = r_value + "from: " + r_source[key].source.id + "<br>"
                }
                return "<p style='font-size: 11px'>" + r_value +"</p>"
              })
      }
    
    function OnClickSearch(){
      var search_val = document.getElementById("txt_search").value
      if (search_val == ""){
        window.alert("検索文字列を入力して下さい")
      }
      else{
        var search_data = jsondata.nodes.filter(function(d){if (d.id == search_val) return true})[0]
        if  (typeof search_data === "undefined") {
          window.alert("無効な検索文字列です");
          document.getElementById("txt_search").value = "";
        }
        else {
    　  //対象先にズームイン
          svg.transition()
            .duration(750)
            .call(zoom.transform, transform(search_data));
        //sidedataにデータを表示
          set_sidedata(search_data);
    
        }
      }
    }
    
    function transform(d) {
      return d3.zoomIdentity
          .translate(width / 2, height / 2)     //一旦中央に
          .scale(4)                             //とりあえず4倍でZoomIn
          .translate(-d.x, -d.y);               //指定nodeの座標
    }
    
    //破線判定
    function stroke_dasharrayCD(d){
        var arr = [2,4,6,7,9,10,11,12,0]
        if (arr.indexOf(d.group) >= 0) {
          return "3 2"  //3:2の破線
        }
        else {
          return "none"  //破線なし
        }
    }
    
    //図形判定
    function nodeTypeID(d){
      var arrRect = [3,4]
      var arrEllipse = [5,6,7]
      var arrHexagon = [9,10,11,12,0]
    
      if(arrRect.indexOf(d) >= 0){
        //Rect
        return "rect"
      }
      else if(arrEllipse.indexOf(d) >= 0){
        //Ellipse
        return "ellipse"
      }
      else if(arrHexagon.indexOf(d) >= 0){
        //Hexagon
        return "hexagon"
      }
      else{
        //Circle
        return "circle"
      }
    }
    
    function linkArc(d) {
      var dx = d.target.x - d.source.x,
          dy = d.target.y - d.source.y,
          dr = Math.sqrt(dx * dx + dy * dy),
          srcPos = getIntersectionPos(d.source , d.target),
          tgtPos = getIntersectionPos(d.target , d.source);
          return "M" + srcPos.x + "," + srcPos.y + "A" + dr + "," + dr + " 0 0,1 " + tgtPos.x + "," + tgtPos.y;
    }
    
    //三点の座標から線の比率を返す
    function calcLoengthRate(pos1,pos2,pos3){
      var v21 = {x:pos1.x - pos2.x , y:pos1.y - pos2.y},
          v23 = {x:pos3.x - pos2.x , y:pos3.y - pos2.y},
          x   =  v21.x * v23.x + v21.y * v23.y,    //v21・v23
          y   =  v21.x * v23.y - v21.y * v23.x,    //v21×v23
          theta = Math.atan2(y,x); //角度（radian）
      if ( theta > 0){
        return theta / (2 * Math.PI);
      }
      else {
        return  1 + theta / (2 * Math.PI);
      };
    }
    
    //node図形とlinkの交点座標を取得(d1側の座標)
    function getIntersectionPos(d1,d2){
      var nodeID = nodeTypeID(d1.group);
      switch (nodeID) {
        case "rect":
          var pos1 = {x:d1.x + spRect.x , y:d1.y + spRect.y},
              pos2 = {x:d1.x , y:d1.y},
              pos3 = {x:d2.x , y:d2.y},
              rate = calcLoengthRate(pos1,pos2,pos3),
              dpos =  figRect.node().getPointAtLength(lenRect * rate);
          return {x:d1.x + dpos.x, y:d1.y + dpos.y}
          break;
        case "ellipse":
          var pos1 = {x:d1.x + spEllipse.x , y:d1.y + spEllipse.y},
              pos2 = {x:d1.x , y:d1.y},
              pos3 = {x:d2.x , y:d2.y},
              rate = calcLoengthRate(pos1,pos2,pos3),
              dpos =  figEllipse.node().getPointAtLength(lenEllipse * rate);
          return {x:d1.x + dpos.x, y:d1.y + dpos.y}
          break;
        case "hexagon":
          var pos1 = {x:d1.x + spHexagon.x , y:d1.y + spHexagon.y},
              pos2 = {x:d1.x , y:d1.y},
              pos3 = {x:d2.x , y:d2.y},
              rate = calcLoengthRate(pos1,pos2,pos3),
              dpos =  figHexagon.node().getPointAtLength(lenHexagon * rate);
          return {x:d1.x + dpos.x, y:d1.y + dpos.y}
          break;
        default:
          var pos1 = {x:d1.x + spCircle.x , y:d1.y + spCircle.y},
              pos2 = {x:d1.x , y:d1.y},
              pos3 = {x:d2.x , y:d2.y},
              rate = calcLoengthRate(pos1,pos2,pos3),
              dpos =  figCircle.node().getPointAtLength(lenCircle * rate);
          return {x:d1.x + dpos.x, y:d1.y + dpos.y}
      }
    }
    
    function dragstarted(d) {
      if (!d3.event.active) simulation.alphaTarget(0.3).restart();
      d.fx = d.x;
      d.fy = d.y;
    }
    
    function dragged(d) {
      d.fx = d3.event.x;
      d.fy = d3.event.y;
    }
    
    function dragended(d) {
      if (!d3.event.active) simulation.alphaTarget(0);
      d.fx = null;
      d.fy = null;
    }
}


function displayTypeSigma(){
    var appendStr = "";
    
    appendStr += "<div id='container' width='1400' height='600'></div>";
    
    $("#correlation_fields svg").remove();
    $("#correlation_fields div").remove();
    $("#correlation_fields").append(appendStr);


    // sigma.parsers.json("./../app/Exports/miserables.json", {
    //     container: 'container',
    //     settings: {
    //       defaultNodeColor: '#ec5148'
    //     }
    // });

    sigma.parsers.json('https://raw.githubusercontent.com/ytyaru/schema.org.Upload.201702171103/master/res/2017/0217/graph.jsonld', {
        container: 'container',
        settings: {
          defaultNodeColor: '#ec5148'
        }
    });
}

function displayTypeVis(){
    var appendStr = "";
    
    appendStr += "<div id='network' width='1400' height='600'></div>";
    
    $("#correlation_fields svg").remove();
    $("#correlation_fields div").remove();
    $("#correlation_fields").append(appendStr);
    
    // var nodes = new vis.DataSet([
    //     {id: 1, label: 'A'},
    //     {id: 2, label: 'B'},
    //     {id: 3, label: 'C'},
    //     {id: 4, label: 'D'},
    //     {id: 5, label: 'E'},
    //     {id: 6, label: 'F'},
    //     {id: 7, label: 'G'},
    //     {id: 8, label: 'H'},
    // ]);
    // var nodes = new vis.DataSet([
    //     {id: 1, label: 'A', group: 1},
    //     {id: 2, label: 'B', group: 1},
    //     {id: 3, label: 'C', group: 1},
    //     {id: 4, label: 'D', group: 1},
    //     {id: 5, label: 'E', group: 2},
    //     {id: 6, label: 'F', group: 2},
    //     {id: 7, label: 'G', group: 2},
    //     {id: 8, label: 'H', group: 2},
    // ]);
    // var nodes = new vis.DataSet([
    //     {id: 1, label: 'A', group: 1, value: 20, scaling: { label: { enabled: true} }},
    //     {id: 2, label: 'B', group: 1},
    //     {id: 3, label: 'C', group: 1},
    //     {id: 4, label: 'D', group: 1},
    //     {id: 5, label: 'E', group: 2},
    //     {id: 6, label: 'F', group: 2},
    //     {id: 7, label: 'G', group: 2},
    //     {id: 8, label: 'H', group: 2},
    // ]);
    var nodes = new vis.DataSet([
        {id: 1, label: 'A', group: 1, value: 20, scaling: { label: { enabled: true} }},
        {id: 2, label: 'B', group: 1},
        {id: 3, label: 'C', group: 1},
        {id: 4, label: 'D', group: 1},
        {id: 5, label: 'E', group: 2, mass: 2},
        {id: 6, label: 'F', group: 2},
        {id: 7, label: 'G', group: 2},
        {id: 8, label: 'H', group: 2},
    ]);
    // var edges = new vis.DataSet([
    //     {from: 1, to: 2, arrows: 'to'},
    //     {from: 1, to: 3, arrows: 'to'},
    //     {from: 3, to: 4, arrows: 'to'},
    //     {from: 6, to: 1, arrows: 'to'},
    //     {from: 7, to: 8, arrows: 'to'},
    //     {from: 8, to: 7, arrows: 'to'},
    // ]);
    var edges = new vis.DataSet([
        {from: 1, to: 2, arrows: 'to', width: 1},
        {from: 1, to: 3, arrows: 'to', width: 5},
        {from: 3, to: 4, arrows: 'to'},
        {from: 6, to: 1, arrows: 'to'},
        {from: 7, to: 8, arrows: 'to'},
        {from: 8, to: 7, arrows: 'to'},
    ]);
    var container = document.getElementById('network');
    var data = {
        nodes: nodes,
        edges: edges
    };
    var options = {
    };
    var network = new vis.Network(container, data, options);

    network.on("click", function(params) {
        if (params.nodes.length == 1) {
            var nodeId = params.nodes[0];
            var node = nodes.get(nodeId);
            console.log(node.label + 'がクリックされました');
        }
    });
}
