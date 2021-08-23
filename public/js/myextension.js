// Content for 'my-awesome-extension.js'
var mouseStart = new THREE.Vector3(0,0,-10);
          
var mouseEnd = new THREE.Vector3(0,0,-10);


function MyAwesomeExtension(viewer, options) {
  Autodesk.Viewing.Extension.call(this, viewer, options);
  
  
   // Preserve "this" reference when methods are invoked by event handlers.
 
}

MyAwesomeExtension.prototype = Object.create(Autodesk.Viewing.Extension.prototype);
MyAwesomeExtension.prototype.constructor = MyAwesomeExtension;

MyAwesomeExtension.prototype.load = function() {
    console.log('MyAwesomeExtension is loaded!');
    
    return true;
};

MyAwesomeExtension.prototype.unload = function() {
  alert('MyAwesomeExtension is now unloaded!');
  return true;
};

MyAwesomeExtension.prototype.onToolbarCreated = function(){
    this._group = this.viewer.toolbar.getControl('MyAwesomeExtensionToolbar');
    
        if (!this._group) {
            this._group = new Autodesk.Viewing.UI.ControlGroup('MyAwesomeExtensionToolbar');
            this.viewer.toolbar.addControl(this._group);
        }

        // Add a new button to the toolbar group
        this._button = new Autodesk.Viewing.UI.Button('myAwesomeExtensionButton');
        this._button.onClick = (ev) => {
            
            //prepareBoundingBox();
            viewer.setSelectionColor(new THREE.Color(0xFF0000), Autodesk.Viewing.SelectionMode.LEAF_OBJECT); // red color

            
            // alert("Successfully selected ashiba");
            // var instanceTree = viewer.model.getData().instanceTree;
            // var dbIds = Object.keys(instanceTree.nodeAccess.dbIdToIndex);
            //filterAshibaData(dbIds);
            
           //console.log(JSON.stringify(allDbIdsStr, null, 4));
        };
        this._button.setToolTip('Ashiba Selection');
        this._button.addClass('myAwesomeExtensionIcon');
        this._group.addControl(this._button);
}

function prepareBoundingBox(){
    var model = viewer.model;
            const instanceTree = viewer.model.getData().instanceTree;
            const rootId = instanceTree.getRootId();
            
            var boundingSphere = model.getBoundingBox().getBoundingSphere();
    
            var fragList = model.getFragmentList();
            //boxes array 
            var boxes = fragList.fragments.boxes;
            //map from frag to dbid
            var fragid2dbid = fragList.fragments.fragId2dbId; 
            
            var boundingBoxInfo = [];
            var index = 0;
            for(var i=0;i<fragid2dbid.length;i++){
              index = i * 6;
              var thisBox = new THREE.Box3(new THREE.Vector3(boxes[index],boxes[index+1],boxes[index+2]),
                                           new THREE.Vector3(boxes[index+3],boxes[index+4],boxes[index+5]));
              
              
              boundingBoxInfo.push({bbox:thisBox,dbId:fragid2dbid[i]});
            }
            
            console.log("My Result :" + JSON.stringify(boundingBoxInfo,null,4));
            
              var materialLine = new THREE.LineBasicMaterial({
              color: new THREE.Color(0xFF00FF),
              linewidth: 3,
              opacity: .6
            }); 
}

function filterAshibaData(dbIds){
    for(var dbId in dbIds){
        viewer.model.getProperties(dbId,onSuccessCallback, onErrorCallBack);
    }
}

function onSuccessCallback(doc){
        
     var familyName = doc.name;
     if(familyName.includes("ST8_支柱2700")){
         console.log(doc.name);
     }
        
}

function onErrorCallBack(){
    
}
