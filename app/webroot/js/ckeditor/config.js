/****************************************
 ***** For more information contact ******
 *********** kkhatti@gmail.com ***********
 ****************************************/

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#DADADA';
    //Developed by Kamlesh - Dilse
//	config.toolbar_Custom_mini =
//	[
//		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
//		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
//		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
//		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'Undo', 'Redo' ] },
//		'/',
//		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor' ] },
//		{ name: 'tools', items: [ 'Maximize' ] }	
//		
//	];
//
//	//Developed by Kamlesh - Dilse
//	config.toolbar_Custom_medium =
//	[
//	
//		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', 'Preview', 'Print' ] },
//		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
//		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'Undo', 'Redo' ] },
//		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },		
//		'/',
//		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor' ] },
//		{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule'] },
//		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] }
//
//		
//	];
//
//	config.removeButtons = 'BidiRtl,BidiLtr,Outdent,CreateDiv,Iframe,PageBreak,SpecialChar,Blockquote,Indent,Radio,Scayt,Select,RemoveFormat,Strike,Subscript,Superscript,About,TextField,PasteText,PasteFromWord,Checkbox,Maximize,ShowBlocks,HiddenField,ImageButton,Textarea,Button,Form,SelectAll,Replace,Find,Save,Templates,NewPage,Link,Unlink,Anchor,Flash,Table,RemoveFormat,Source,Print,Undo,Redo,HorizontalRule';
//        config.removeDialogTabs = 'image:advanced';
//
// 
//	var xbasepath = '/js/ckfinder/';
//	
//	config.filebrowserBrowseUrl 		= 	xbasepath + 'browse.php?type=files';
//	config.filebrowserImageBrowseUrl 	= 	xbasepath + 'browse.php?type=images';
//	config.filebrowserFlashBrowseUrl	= 	xbasepath + 'browse.php?type=flash';
//	config.filebrowserUploadUrl 		= 	xbasepath + 'upload.php?type=files';
//	config.filebrowserImageUploadUrl 	= 	xbasepath + 'upload.php?type=images';
//	config.filebrowserFlashUploadUrl 	= 	xbasepath + 'upload.php?type=flash';
//	config.allowedContent = true;
//	config.ignoreEmptyParagraph = false;
//	config.extraAllowedContent = 'ul ol li';
    config.toolbar_Basic =
            [
                ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'About']
            ];
    config.toolbar_Custom = [
        ['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', '-', 'Outdent', 'Indent', '-', 'NumberedList', 'BulletedList'],
        '/',
        ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
        ['Link', 'Smiley', 'TextColor', 'BGColor']
    ];
};
