'use strict';
var isEmpty = function(obj) {
  if (obj instanceof Date) return false;
  if (obj == null) return true;
  if (obj.length > 0)    return false;
  if (obj.length === 0)  return true;
  if (typeof obj !== "object") return true;
  for (var key in obj) {
    if (obj[key] !== null || obj[key] !== undefined) return false;
  }

  return true;
},
isNull = function (v) {
  if (typeof(v) == 'object') {
    return isEmpty(v);
  } else {
    return (
        v === undefined
        || v === null
        || (typeof(v) == 'string' && v == '')
    );
  }
},
isImageFile = function(file) {
  if (file.type) {
    return /^image\/\w+$/.test(file.type);
  } else {
    return /\.(jpg|jpeg|png|gif)$/.test(file);
  }
},
getCropDate = function(date) {
  if (date !== null && date instanceof Date) {
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
  } else if (date !== null && typeof(date) == 'number') {
    date = new Date(date);
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
  } else {
    return null
  }
},
toDataUrl = function(src, callback, outputFormat) {
  var img = new Image();
  img.crossOrigin = 'Anonymous';
  img.onload = function() {
    var canvas = document.createElement('CANVAS');
    var ctx = canvas.getContext('2d');
    var dataURL;
    canvas.height = this.height;
    canvas.width = this.width;
    ctx.drawImage(this, 0, 0);
    dataURL = canvas.toDataURL(outputFormat);
    callback(dataURL);
  };
  img.src = src;
  if (img.complete || img.complete === undefined) {
    img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
    img.src = src;
  }
};