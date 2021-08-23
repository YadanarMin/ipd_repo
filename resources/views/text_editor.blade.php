@extends('layouts.baselayout')
@section('title', 'CCC - Hashtag Search')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/iPD/public/js/select2/select2.min.js"></script>
<link rel="stylesheet" href="/iPD/public/css/select2.min.css">
<link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>

<style>

.main-content{
	margin: 0 auto;
	margin-bottom : 3%;
	width:80%;
	display : center;
}


</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    <div id="toolbar">
      <button class="ql-bold">Bold</button>
      <button class="ql-italic">Italic</button>
      <button class="ql-underline">Underline</button>
      <button class="ql-strike">Strike</button>
      <select class="ql-color"></select>
      <select class="ql-background"></select>
    </div>

    <!-- Create the editor container -->
    <div id="editor" contenteditable="false">
      <p>Hello World!</p>
    </div>
    




</div>
<script>

      for(var i=0;i<2;i++){
        var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
      
        [{ 'color': [] }, { 'background': [] }],         // dropdown with defaults from theme
                                    
      ];
      var editor = new Quill('#editor', {
        modules: { 
            toolbar: toolbarOptions },
        formats : 'inline',
        theme: 'snow'
      });
      }
      editor.remove();
  
      
      
  
    
</script>
@endsection