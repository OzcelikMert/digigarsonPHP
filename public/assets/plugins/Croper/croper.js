
let crop = {
    initialize: function(viewport_width, viewport_height, boundary_width, boundary_height , input_file_element, input_crop_button, result_image, result_hidden, crop_element,show_zoomer = true, resize = false, orientation = true, quality = 0.5, format = "webp"){
        let self = this;
        self.settings.viewport_width = viewport_width
        self.settings.viewport_height = viewport_height;
        self.settings.boundary_width = boundary_width;
        self.settings.boundary_height = boundary_height;
        self.settings.orientation = orientation
        self.settings.input_file_element = input_file_element;
        self.settings.input_crop_button = input_crop_button;
        self.settings.result_image = result_image;
        self.settings.result_hidden = result_hidden;
        self.settings.show_zoomer = show_zoomer;
        self.settings.resize = resize;
        self.settings.quality = quality;
        self.settings.format = format;
        self.settings.crop_element = crop_element;
        self.set_element();
        self.set_events();
        console.log(crop.settings);
    },
    set_element: function (){
        let self = this;

        self.settings.upload_crop = $(self.settings.crop_element).croppie({
            viewport: { width: self.settings.viewport_width, height: self.settings.viewport_height },
            boundary: { width: self.settings.boundary_width, height: self.settings.boundary_height },
            showZoomer:         self.settings.show_zoomer,
            enableResize:       self.settings.resize,
            enableOrientation:  self.settings.orientation
        });

    },
    read_file: function (input){
        let self = this;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                //$('.upload-demo').addClass('ready');
                self.settings.upload_crop.croppie('bind', {
                    url: e.target.result
                }).then(function(){
                    console.log('Crop Success');
                });
            }
            reader.readAsDataURL(input.files[0]);
        }else {
            alert("not support format");
        }
    },
    settings : {
        upload_crop: null,
        viewport_width : 0, viewport_height : 0,
        boundary_width : 0, boundary_height : 0,
        show_zoomer : true,
        resize : false,
        orientation : true,
        crop_element : "#crop_element",
        input_file_element : "#upload_file",
        input_crop_button : "#upload_crop",
        result_image : "#upload_result_image",
        result_hidden : "#upload_result_hidden",
        quality :"0.5",
        format : "webp"
    },
    set_events: function () {
        let self = this;

        $(self.settings.input_file_element).on('change', function () { self.read_file(this); });

        $(self.settings.input_crop_button).on('click', function (e) {
            (self.settings.upload_crop).croppie('result', {
                type: 'base64',
                size: 'viewport',
                format: self.settings.format,
                quality: `${self.settings.quality}`,
            }).then(function (result) {
                console.log(result)
                $(self.settings.result_image).attr("src",result)
                $(self.settings.result_hidden).val(result);
                console.log(result)
            });
        });
    }
}
