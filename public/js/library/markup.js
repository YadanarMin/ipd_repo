// Content for 'my-awesome-extension.js'

function Animation(viewer, options) {
  Autodesk.Viewing.Extension.call(this, viewer, options);
}

Animation.prototype = Object.create(Autodesk.Viewing.Extension.prototype);
Animation.prototype.constructor = Animation;

Animation.prototype.load = function() {
  var viewer = this.viewer;
  
  
};

Animation.prototype.unload = function() {
  alert('MyAwesomeExtension is now unloaded!');
  return true;
};



Autodesk.Viewing.theExtensionManager.registerExtension('Animation', Animation);