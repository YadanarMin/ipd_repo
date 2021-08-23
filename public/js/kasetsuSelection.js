//To add custom extension to 重仮設
function KasetsuSelection(viewer, options) {

  Autodesk.Viewing.Extension.call(this, viewer, options);

  //Forge Viewer
  var viewer = this.viewer; 
  //bounding sphere of this model
  var boundingSphere = null;   
  //container DIV of the viewer
  var container = viewer.canvas.parentElement;   
  //start point of select window
  var mouseStart = new THREE.Vector3(0,0,-10);
  //end point of select window
  var mouseEnd = new THREE.Vector3(0,0,-10); 
  //is selecting window running
  var running = false; 
  //rectangle lines of select window
  var lineGeom = null;
  var rectGroup = null;
  //material for rectangle lines of select window
  var materialLine = null; 
  
  var boundingBoxInfo = [];

  //when extension is loaded
  this.load = function() {
    console.log('KasetsuSelection is loaded!'); 
    viewer.impl.invalidate(true);  
    return true;
  };

  //when extension is unloaded 
  this.unload = function() {
    console.log('KasetsuSelection is now unloaded!');
    //unbind keyup event
    $(document).unbind('keyup', this.onKeyUp);
    return true;
  }; 
  
  this.onToolbarCreated = function(){
     this._group = this.viewer.toolbar.getControl('KasetsuSelectionToolbar');
    
        if (!this._group) {
            this._group = new Autodesk.Viewing.UI.ControlGroup('KasetsuSelectionToolbar');
            this.viewer.toolbar.addControl(this._group);
        }

        // Add a new button to the toolbar group
        this._button = new Autodesk.Viewing.UI.Button('kasetsuSelectionButton');
        
        this._button.onClick = (ev) => {
           
            $(document).bind('keyup', onKeyUp);
            $(document).bind('keydown', onKeyDown);
            viewer.navigation.setIsLocked(true) ; 

            //start to monitor mouse down
            container.addEventListener('mousedown',onMouseDown); 
            //get current camera
            var canvas = viewer.canvas;
            var canvasWidth = canvas.clientWidth;
            var canvasHeight = canvas.clientHeight;
        
            var camera = new THREE.OrthographicCamera(
              0,canvasWidth,0,canvasHeight,1,1000);
        
            //create overlay scene for selection window
            viewer.impl.createOverlayScene(
              "selectionWindowOverlay",
              materialLine,
              materialLine,
              camera);
              
        };
        
        this._button.setToolTip('Kasetsu Selection');
        this._button.addClass('customizeExtension');
        this._group.addControl(this._button);
  };

 
  let selection;
  
  //when key down on shift key
  function onKeyDown(evt){
    
    if(evt.keyCode == 16){
      selection = viewer.getSelection();
      console.log("Selection :" + selection) ;
      //container.addEventListener("mousedown",onMouseDown());
    }
  }
  
  //when key up
  function onKeyUp(evt) {  
    console.log('onKeyUp:' + evt.keyCode); 
    
    //when key 'esc' is pressed
    if(evt.keyCode == 27){
      
       //unlock current navigation
       viewer.navigation.setIsLocked(false) ; 
      
       //remove mouse events
      container.removeEventListener('mousedown',onMouseDown);
      container.removeEventListener('mouseup',onMouseUp); 
      container.removeEventListener('mousemove',onMouseMove);  

      running = false;

      //remove the Overlay Scene
      viewer.impl.removeOverlayScene(
        "selectionWindowOverlay");
    }
    
    //true
    return false ;
  } 

  function onMouseMove(evt) {  
    
    console.log("MouseMove");
    if(running){ 
      //calculate the offset with viewer container position, for Three.js geometry
      const viewer_pos = viewer.container.getBoundingClientRect();
      //get mouse points
      mouseEnd.x = evt.clientX - viewer_pos.x;
      mouseEnd.y =  evt.clientY - viewer_pos.y;


      //update rectange lines
      lineGeom.vertices[1].x = mouseStart.x;
      lineGeom.vertices[1].y = mouseEnd .y;
      lineGeom.vertices[2] =   mouseEnd.clone();
      lineGeom.vertices[3].x = mouseEnd.x;
      lineGeom.vertices[3].y = mouseStart.y;
      lineGeom.vertices[4] = lineGeom.vertices[0];  

      lineGeom.verticesNeedUpdate = true;
      viewer.impl.invalidate(false, false, true); 
    }
  }

  function onMouseUp(evt) { 
      console.log("MouseUp");
      if(evt.shiftKey){
        if(!selection.includes(viewer.getSelection())){
          selection.push(viewer.getSelection());
          let filteredArray = selection.filter(value => viewer.getSelection().includes(value));
          viewer.select(filteredArray); //5
        }
        
      }else{
      if(running){
       //calculate the offset with viewer container position, for Three.js geometry
          const viewer_pos = viewer.container.getBoundingClientRect();

       //get mouse points 
      
          mouseEnd.x = evt.clientX - viewer_pos.x;
          mouseEnd.y =  evt.clientY - viewer_pos.y;
          
        //remove the overlay of one time rectangle
         viewer.impl.removeOverlay("selectionWindowOverlay", rectGroup); 
          running = false;

  //     //remove mouse event
          container.removeEventListener('mouseup',onMouseUp); 
          container.removeEventListener('mousemove',onMouseMove);  

  //     //get box within the area of select window, or partially intersected. 
  //     //now we need to offset the screenpoint back without viewer container position.
         
          var allId = [];
          var filterId = [];
          let ashibaIdList = [];
            if( mouseStart.x == mouseEnd.x){
              return;
            
            }
            allId = compute({clientX:mouseStart.x + viewer_pos.x ,
                           clientY:mouseStart.y + viewer_pos.y},
                          {clientX:mouseEnd.x + viewer_pos.x,
                          clientY:mouseEnd.y + viewer_pos.y}, false);  // true:  partially intersected.  false: inside the area only
          
            
            console.log("Result:" + JSON.stringify(allId,null,4));
            
            for(var i =0; i<allId.length;i++){
              if(filterId.includes(allId[i])){
                continue;
              }else{
                filterId.push(allId[i]);
              }
            }
            
            console.log("Filter" + JSON.stringify(filterId,null,4));
            
            if(allId.length == 0){
              return;
            }{
              viewer.model.getBulkProperties2(
              allId,
              {
                propFilter: ["name"],
                ignoreHidden: true
              },
              function(e){
                let familyNameList = [];
                let dbIdList = [];
                console.log("Obj Properties" + JSON.stringify(e,null,4));
                for(var i in e){
                  let familyName = e[i]["name"];
                  var n = familyName.indexOf("[");
                  let fname = familyName.slice(0,n);
                  if(fname.startsWith("MO") 
                    && fname != "MO_H鋼_杭 "
                    && fname != "MO_H鋼_棚杭 "
                    && fname != "MO_H鋼_配列_広幅 "
                    && fname != "MO_H鋼_配列_細幅 "
                    && fname != "MO_BK-50 "
                    && fname != "MO_BK-60 "
                    && fname != "MO_BK-75 "
                    && fname != "MO_BK-100 "){
                    dbIdList.push(e[i]["dbId"]);
                  }else{
                    
                  }
                }
                console.log("Obj DB" + JSON.stringify(dbIdList,null,4));
                viewer.select(dbIdList);
                
      
               }
                
              );
              
            }
            
            viewer.clearSelection();
            allId = [];
         
     }
  }
  }
   
 
  function onMouseDown(evt) { 
    if(evt.shiftKey){
      //selection = viewer.getSelection();
      container.addEventListener('mouseup',onMouseUp);
    }
    else{
    console.log("MouseDown");
    viewer.clearSelection(); 
    //calculate the offset with viewer container position, for Three.js geometry
    const viewer_pos = viewer.container.getBoundingClientRect(); 
    //get mouse points  
    mouseStart.x = evt.clientX - viewer_pos.x;
    mouseStart.y =  evt.clientY - viewer_pos.y;
   
    
    //Test
    materialLine = new THREE.LineBasicMaterial({
	    color: 0x0000ff,
      linewidth: 1,
      opacity: .6
    });
    //Test
    
    running = true; 

    //build the rectangle lines of select window
    if(rectGroup === null) {
          lineGeom = new THREE.Geometry();
          console.log("Hi There");
          lineGeom.vertices.push(
          mouseStart.clone(),
          mouseStart.clone(),
          mouseStart.clone(),
          mouseStart.clone(),
          mouseStart.clone());
       
          // add geom to group
          var line_mesh = new THREE.Line(lineGeom, materialLine); 

          rectGroup = new THREE.Group();
          rectGroup.add(line_mesh); 
          
    }
    else{
        lineGeom.vertices[0] = mouseStart.clone();
        lineGeom.vertices[1] = mouseStart.clone();
        lineGeom.vertices[2] = mouseStart.clone();
        lineGeom.vertices[3] = mouseStart.clone();
        lineGeom.vertices[4] = mouseStart.clone();  
     
        lineGeom.verticesNeedUpdate = true;
    } 
   
    viewer.impl.addOverlay("selectionWindowOverlay", rectGroup); 
    viewer.impl.invalidate(false, false, true); 

    //start to mornitor the mouse events
    container.addEventListener('mouseup',onMouseUp); 
    container.addEventListener('mousemove',onMouseMove); 
  } 
  }

  //prepare the range of select window and filter out those objects
  function compute (pointer1, pointer2,partialSelect) {

    // build 4 rays to project the 4 corners
    // of the selection window

    var xMin = Math.min(pointer1.clientX, pointer2.clientX)
    var xMax = Math.max(pointer1.clientX, pointer2.clientX)

    var yMin = Math.min(pointer1.clientY, pointer2.clientY)
    var yMax = Math.max(pointer1.clientY, pointer2.clientY)

    var ray1 = pointerToRay({
      clientX: xMin,
      clientY: yMin
    })

    var ray2 = pointerToRay({
      clientX: xMax,
      clientY: yMin
    })

    var ray3 = pointerToRay({
      clientX: xMax,
      clientY: yMax
    })

    var ray4 = pointerToRay({
      clientX: xMin,
      clientY: yMax
    })

    
    // first we compute the top of the pyramid
    var top = new THREE.Vector3(0,0,0)

    top.add (ray1.origin)
    top.add (ray2.origin)
    top.add (ray3.origin)
    top.add (ray4.origin)

    top.multiplyScalar(0.25)

    // we use the bounding sphere to determine
    // the height of the pyramid
    var {center, radius} = viewer.model.getBoundingBox().getBoundingSphere();

    
    // compute distance from pyramid top to center
    // of bounding sphere

    var dist = new THREE.Vector3(
      top.x - center.x,
      top.y - center.y,
      top.z - center.z)

    // compute height of the pyramid:
    // to make sure we go far enough,
    // we add the radius of the bounding sphere

    var height = radius + dist.length()
   

    // compute the length of the side edges

    var angle = ray1.direction.angleTo(
      ray2.direction)
    
    var length = height / Math.cos(angle * 0.5)
   
    // compute bottom vertices

    var v1 = new THREE.Vector3(
      ray1.origin.x + ray1.direction.x * length,
      ray1.origin.y + ray1.direction.y * length,
      ray1.origin.z + ray1.direction.z * length)

    var v2 = new THREE.Vector3(
      ray2.origin.x + ray2.direction.x * length,
      ray2.origin.y + ray2.direction.y * length,
      ray2.origin.z + ray2.direction.z * length)

    var v3 = new THREE.Vector3(
      ray3.origin.x + ray3.direction.x * length,
      ray3.origin.y + ray3.direction.y * length,
      ray3.origin.z + ray3.direction.z * length)

    var v4 = new THREE.Vector3(
      ray4.origin.x + ray4.direction.x * length,
      ray4.origin.y + ray4.direction.y * length,
      ray4.origin.z + ray4.direction.z * length)

    // create planes

    var plane1 = new THREE.Plane()
    var plane2 = new THREE.Plane()
    var plane3 = new THREE.Plane()
    var plane4 = new THREE.Plane()
    var plane5 = new THREE.Plane()

    plane1.setFromCoplanarPoints(top, v1, v2)
    plane2.setFromCoplanarPoints(top, v2, v3)
    plane3.setFromCoplanarPoints(top, v3, v4)
    plane4.setFromCoplanarPoints(top, v4, v1)
    plane5.setFromCoplanarPoints( v3, v2, v1)

    var planes = [
      plane1,
      plane2,
      plane3,
      plane4,
      plane5
    ]

    var vertices = [
      v1, v2, v3, v4, top
    ]

    // filter all bounding boxes to determine
    // if inside, outside or intersect

    var result = filterBoundingBoxes(
      planes, vertices, partialSelect)

    // all inside bboxes need to be part of the selection

      var dbIdsInside = result.inside.map((bboxInfo) => {

        return bboxInfo.dbId
      })
    
    

    // if partialSelect = true
    // we need to return the intersect bboxes

    if (partialSelect) {

      var dbIdsIntersect = result.intersect.map((bboxInfo) => {

        return bboxInfo.dbId
      });


      return [...dbIdsInside, ...dbIdsIntersect]
    }
    
    console.log("Inside Call " + JSON.stringify(dbIdsInside,null,4));
    return dbIdsInside;
    }

//     //rays of the corners of select window
    function pointerToRay (pointer) {

      var camera = viewer.navigation.getCamera();
      var pointerVector = new THREE.Vector3();
      var rayCaster = new THREE.Raycaster();
      var pointerDir = new THREE.Vector3();
      var domElement = viewer.canvas;
  
      var rect = domElement.getBoundingClientRect()
  
      var x =  ((pointer.clientX - rect.left) / rect.width) * 2 - 1
      var y = -((pointer.clientY - rect.top) / rect.height) * 2 + 1
      
      //console.log("Camera " + camera.projectionMatrix );
      if (camera.isPerspective) {
        
        pointerVector.set(x, y, 0.5)
  
        pointerVector.unproject(camera)
  
        rayCaster.set(camera.position,
          pointerVector.sub(
            camera.position).normalize())
  
      } else {
  
        pointerVector.set(x, y, -15)
  
        pointerVector.unproject(camera)
  
        pointerDir.set(0, 0, -1)
  
        rayCaster.set(pointerVector,
          pointerDir.transformDirection(
            camera.matrixWorld))
      }
      //console.log("Raycaster" + JSON.stringify(rayCaster.ray,null,4));
  
      return rayCaster.ray;
    } 

//     //filter out those objects in the range of select window
    function filterBoundingBoxes (planes, vertices, partialSelect) { 
      
      //Test
        //fragments list array
      var fragList = viewer.model.getFragmentList();
      //boxes array 
      var boxes = fragList.fragments.boxes;
      //map from frag to dbid
      var fragid2dbid = fragList.fragments.fragId2dbId; 
      console.log("Frag" + fragid2dbid);
      //build _boundingBoxInfo by the data of Viewer directly
      //might probably be a bit slow with large model..
      
      var index = 0;
      for(var step=0;step<fragid2dbid.length;step++){
        index = step * 6;
        var thisBox = new THREE.Box3(new THREE.Vector3(boxes[index],boxes[index+1],boxes[index+2]),
                                     new THREE.Vector3(boxes[index+3],boxes[index+4],boxes[index+5]));
        
        
        boundingBoxInfo.push({bbox:thisBox,dbId:fragid2dbid[step]});
      }
      
      //Test
      console.log("BoundingBoxInfo " + JSON.stringify(boundingBoxInfo,null,4));
      var intersect = []
      var outside = []
      var inside = []

      var triangles = [
        {a:vertices[0],b:vertices[1],c:vertices[2]},
        {a:vertices[0],b:vertices[2],c:vertices[3]},
        {a:vertices[1],b:vertices[0],c:vertices[4]},
        {a:vertices[2],b:vertices[1],c:vertices[4]},
        {a:vertices[3],b:vertices[2],c:vertices[4]},
        {a:vertices[0],b:vertices[3],c:vertices[4]}  
      ]  
  
      for (let bboxInfo of boundingBoxInfo) {
  
        
        // if bounding box inside, then we can be sure
        // the mesh is inside too
  
        if (containsBox (planes, bboxInfo.bbox)) 
        { 
          inside.push(bboxInfo) 
        
        } else if (partialSelect) { 
           
          //reconstructed by using AABBCollision lib.
          if(boxIntersectVertex(bboxInfo.bbox,triangles))
              intersect.push(bboxInfo)
          else 
            outside.push(bboxInfo)
          
        } else { 
          outside.push(bboxInfo)
          

        }
      }
      
      console.log("Inside " + JSON.stringify(inside,null,4));
      console.log("Outside" + JSON.stringify(outside,null,4));
      return {
        intersect,
        outside,
        inside
      }
    } 

    
//     //get those boxes which are included in the
//     //range of select window
    function containsBox (planes, box) {

      var {min, max} = box
  
      var vertices = [
        new THREE.Vector3(min.x, min.y, min.z),
        new THREE.Vector3(min.x, min.y, max.z),
        new THREE.Vector3(min.x, max.y, max.z),
        new THREE.Vector3(max.x, max.y, max.z),
        new THREE.Vector3(max.x, max.y, min.z),
        new THREE.Vector3(max.x, min.y, min.z),
        new THREE.Vector3(min.x, max.y, min.z),
        new THREE.Vector3(max.x, min.y, max.z)
      ]
  
      for (let vertex of vertices) {
  
        for (let plane of planes) {
  
          if (plane.distanceToPoint(vertex) < 0) {
  
            return false
          }
        }
      }
  
      return true
    }

//     //get those boxes which are initersected with the
//     //range of select window (triangles)
    function boxIntersectVertex (box, triangles) {  
      for(index in triangles){
        var t = triangles[index];
        if(collision.isIntersectionTriangleAABB(t.a,t.b,t.c,box))
          return true;
      } 
      return false; 
    }  
}