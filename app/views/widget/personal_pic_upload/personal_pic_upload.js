define(['port', 'jquery', 'jquery-ui', 'jquery.Jcrop', 'jquery.uploadify'], function(port){
	console.log("personal pic loaded");
	$(".shadow").css({
		visibility : "visible"
	})
	// Create variables (in this scope) to hold the API and image size
	var jcrop_api,
		boundx,
		boundy,

	// Grab some information about the preview pane
		$preview = $('#preview-pane'),
		$pcnt = $('#preview-pane .preview-container'),
		$pimg = $('#preview-pane .preview-container img'),

		xsize = $pcnt.width(),
		ysize = $pcnt.height();

	$('#target').Jcrop({
		onChange: updatePreview,
		onSelect: updatePreview,
		aspectRatio: xsize / ysize,
		boxWidth : 250,
		boxHeight: 140
	},function(){
		// Use the API to get the real image size
		var bounds = this.getBounds();
		boundx = bounds[0];
		boundy = bounds[1];
		// Store the API in the jcrop_api variable
		jcrop_api = this;

		// Move the preview into the jcrop container for css positioning
		$preview.appendTo(jcrop_api.ui.holder);
	});

	function updatePreview(c)
	{
		if (parseInt(c.w) > 0)
		{
			var rx = xsize / c.w;
			var ry = ysize / c.h;

			$pimg.css({
				width: Math.round(rx * boundx) + 'px',
				height: Math.round(ry * boundy) + 'px',
				marginLeft: '-' + Math.round(rx * c.x) + 'px',
				marginTop: '-' + Math.round(ry * c.y) + 'px'
			});
		}
		$('#x1').val(c.x);
		$('#y1').val(c.y);
		$('#x2').val(c.x2);
		$('#y2').val(c.y2);
		$('#width').val(c.w);
		$('#height').val(c.h);
	};




	var cuterHeight = $(".jcrop-holder").height();

	if(cuterHeight < 250){
		$(".jcrop-holder").css({
			"top" : (250 - cuterHeight) / 2
		});
		$("#preview-pane").css({
			"top" : - ((250 - cuterHeight) / 4)
		})
	}


});

