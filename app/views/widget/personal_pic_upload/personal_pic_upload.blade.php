<div class="content_header">
    <div class="ui-helper-clearfix">
        <h2>个人中心-{{$personal['user_name']}}</h2>
    </div>
</div>
<div class="content_inner shadow"  style="visibility: hidden">
       <button id="upload_btn">上传</button>

   {{--{{Form::open(array("method" => "post", "action" => $personal['submit_cut']))}}--}}
    <div class="ui-helper-clearfix">
       <div class="image_crop_container">
            <img class="hide" src="{{$personal['user_image']}}" id="target" alt=""/>

               <input type="hidden" name="x1" id="x1" />
               <input type="hidden" name="y1" id="y1" />
               <input type="hidden" name="x2" id="x2" />
               <input type="hidden" name="y2" id="y2" />
               <input type="hidden" name="width" id="width" />
               <input type="hidden" name="height" id="height" />
       </div>


       <div class="image_right_container">
            <div id="preview-pane">
                <div class="preview-container">
                    <img src="{{$personal['user_image']}}" class="jcrop-preview" alt="Preview" />
                </div>
            </div>
       </div>
    </div>
    <div class="button_container">
         <input type="submit" class="btn btn-yellow" value="保存"/>
         <a href="{{$personal['return_back']}}" class="btn ">取消</a>
    </div>
    {{--{{ Form::close(); }}--}}
</div>

@section("css")
    @parent
    {{HTML::style("/css/widget/personal_pic_upload/personal_pic_upload.css")}}
    {{HTML::style("/css/widget/personal_pic_upload/jquery.Jcrop.css")}}
@stop