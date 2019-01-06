jQuery(function() {
    var $ = jQuery;
    $.fn.webupload_fileinput = function (config) {
        $('body').append(renderModal());
        var _modal = $('#' + config['modal_id']),
            chooseObject; // 点击选择图片的按钮
        
        _modal.on("shown.bs.modal", init);

        function init () {
            var $wrap = $('#uploader'),
                // 图片容器
                $queue = $('<ul class="filelist"></ul>').appendTo( $wrap.find('.queueList') ),
                // 状态栏，包括进度和控制按钮
                $statusBar = $wrap.find('.statusBar'),
                // 文件总体选择信息。
                $info = $statusBar.find('.info'),
                // 上传按钮
                $upload = $wrap.find('.uploadBtn'),
                // 没选择文件之前的内容。
                $placeHolder = $wrap.find('.placeholder'),
                // 总体进度条
                $progress = $statusBar.find('.progress').hide(),
                // 添加的文件数量
                fileCount = 0,
                // 添加的文件总大小
                fileSize = 0,
                // 优化retina, 在retina下这个值是2
                ratio = window.devicePixelRatio || 1,
                // 缩略图大小
                thumbnailWidth = 110 * ratio,
                thumbnailHeight = 110 * ratio,
                // 可能有pedding, ready, uploading, confirm, done.
                state = 'pedding',
                // 所有文件的进度信息，key为file id
                percentages = {},
                supportTransition = (function(){
                    var s = document.createElement('p').style,
                        r = 'transition' in s ||
                              'WebkitTransition' in s ||
                              'MozTransition' in s ||
                              'msTransition' in s ||
                              'OTransition' in s;
                    s = null;
                    return r;
                })(),
                uploadedFiles = [], // 成功上传的图片信息
                k = 0,
                $r = $('<li class="fileinput-button js-add-image" id="filePicker2" style="display:none;"> <a href="javascript:;" class="fileinput-button-icon">+</a></li>').appendTo($wrap.find('.filelist')),
                // WebUploader实例
                uploader;
            if ( !WebUploader.Uploader.support() ) {
                alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
                throw new Error( 'WebUploader does not support the browser you are using.' );
            }
            if (config.compress == undefined) {
                config.compress = {};
            }

            // 实例化
            uploader = WebUploader.create({
                pick: {
                    id: '#filePicker',
                    label: tips.webuploader.clickSelectImage,
                    multiple: config.pick.multiple
                },
                dnd: '#uploader .queueList',
                paste: document.body,
                accept: config.accept,
                swf: './webuploader/Uploader.swf',
                server: config.server,
                formData: config.formData,
                disableGlobalDnd: config.disableGlobalDnd,
                chunked: config.chunked,
                fileNumLimit: config.pick.multiple ? config.fileNumLimit : 1,
                fileSizeLimit: config.fileSizeLimit,
                fileSingleSizeLimit: config.fileSingleSizeLimit,
                compress: {
                    width: config.compress.width,
                    height: config.compress.height,
                    quality: config.compress.quality,
                    allowMagnify: config.compress.allowMagnify,
                    crop: config.compress.crop,
                    preserveHeaders: config.compress.preserveHeaders,
                    noCompressIfLarger: config.compress.noCompressIfLarger,
                    compressSize: config.compress.compressSize
                }
            });

            // 添加“添加文件”的按钮，
            uploader.addButton({
                id: '#filePicker2',
                label: '+',
                multiple: config.pick.multiple
            });

            // 当有文件添加进来时执行，负责view的创建
            function addFile( file ) {
                var $li = $( '<li id="' + file.id + '">' +
                        '<p class="title">' + file.name + '</p>' +
                        '<p class="imgWrap"></p>'+
                        '</li>' ),
                    $btns = $('<div class="file-panel">' +
                        '<span class="cancel">删除</span>' +
                        '<span class="rotateRight">向右旋转</span>' +
                        '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
                    $prgress = $li.find('p.progress span'),
                    $wrap = $li.find( 'p.imgWrap' ),
                    $info = $('<p class="error"></p>'),
                    showError = function( code ) {
                        switch( code ) {
                            case 'exceed_size':
                                text = '文件大小超出';
                                break;
                            case 'interrupt':
                                text = '上传暂停';
                                break;
                            default:
                                text = '上传失败，请重试';
                                break;
                        }
                        $info.text( text ).appendTo( $li );
                    };
                if ( file.getStatus() === 'invalid' ) {
                    showError( file.statusText );
                } else {
                    // @todo lazyload
                    $wrap.text( '预览中' );
                    uploader.makeThumb( file, function( error, src ) {
                        if ( error ) {
                            $wrap.text( '不能预览' );
                            return;
                        }
                        var img = $('<img src="'+src+'">');
                        $wrap.empty().append( img );
                    }, thumbnailWidth, thumbnailHeight );
                    percentages[ file.id ] = [ file.size, 0 ];
                    file.rotation = 0;
                }
                file.on('statuschange', function( cur, prev ) {

                    if ( prev === 'progress' ) {
                        $prgress.hide().width(0);
                    } else if ( prev === 'queued' ) {
                        $li.off( 'mouseenter mouseleave' );
                        $btns.remove();
                    }
                    // 成功
                    if ( cur === 'error' || cur === 'invalid' ) {
                        showError( file.statusText );
                        percentages[ file.id ][ 1 ] = 1;
                    } else if ( cur === 'interrupt' ) {
                        showError( 'interrupt' );
                    } else if ( cur === 'queued' ) {
                        percentages[ file.id ][ 1 ] = 0;
                    } else if ( cur === 'progress' ) {
                        $info.remove();
                        $prgress.css('display', 'block');
                    } else if ( cur === 'complete' ) {
                        $li.append( '<span class="success"></span>' );
                    }
                    $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
                });
                $li.on( 'mouseenter', function() {
                    $btns.stop().animate({height: 30});
                });
                $li.on( 'mouseleave', function() {
                    $btns.stop().animate({height: 0});
                });
                $btns.on( 'click', 'span', function() {
                    var index = $(this).index(),
                        deg;
                    switch ( index ) {
                        case 0:
                            uploader.removeFile( file );
                            return;
                        case 1:
                            file.rotation += 90;
                            break;
                        case 2:
                            file.rotation -= 90;
                            break;
                    }
                    if ( supportTransition ) {
                        deg = 'rotate(' + file.rotation + 'deg)';
                        $wrap.css({
                            '-webkit-transform': deg,
                            '-mos-transform': deg,
                            '-o-transform': deg,
                            'transform': deg
                        });
                    } else {
                        $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                    }
                });
                config.pick.multiple && $r.find(".fileinput-button").show(), $li.insertBefore($('#filePicker2'));
                // $li.appendTo( $queue );
            }
            // 负责view的销毁
            function removeFile( file ) {
                var $li = $('#'+file.id);
                delete percentages[ file.id ];
                updateTotalProgress();
                $li.off().find('.file-panel').off().end().remove();
            }
            function resetUploader() {
                uploadedFiles = [];
                k = 0;
            }
            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;
                $.each( percentages, function( k, v ) {
                    total += v[ 0 ];
                    loaded += v[ 0 ] * v[ 1 ];
                } );
                percent = total ? loaded / total : 0;
                spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
                spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
                updateStatus();
            }
            function updateStatus() {
                var text = '', stats;
                if ( state === 'ready' ) {
                    text = '选中' + fileCount + '张图片，共' +
                    WebUploader.formatSize( fileSize ) + '。';
                } else if ( state === 'confirm' ) {
                    stats = uploader.getStats();
                    if ( stats.uploadFailNum ) {
                        text = '已成功上传' + stats.successNum+ '张图片，'+
                        stats.uploadFailNum + '张图片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                    }

                } else {
                    stats = uploader.getStats();
                    text = '共' + fileCount + '张（' +
                            WebUploader.formatSize( fileSize )  +
                            '），已上传' + stats.successNum + '张';
                    if ( stats.uploadFailNum ) {
                        text += '，失败' + stats.uploadFailNum + '张';
                    }
                }
                $info.html( text );
            }
            function setState( val ) {
                var file, stats;
                if ( val === state ) {
                    return;
                }
                $upload.removeClass( 'state-' + state );
                $upload.addClass( 'state-' + val );
                state = val;
                switch ( state ) {
                    case 'pedding':
                        $placeHolder.removeClass( 'element-invisible' );
                        $queue.parent().removeClass('filled');
                        $queue.hide();
                        $statusBar.addClass( 'element-invisible' );
                        uploader.refresh();
                        $r.hide();
                        break;
                    case 'ready':
                        $placeHolder.addClass( 'element-invisible' );
                        $( '#filePicker2' ).removeClass( 'element-invisible');
                        $queue.parent().addClass('filled');
                        $queue.show();
                        $statusBar.removeClass('element-invisible');
                        uploader.refresh();
                        config.pick.multiple && $r.show();
                        break;
                    case 'uploading':
                        $( '#filePicker2' ).addClass( 'element-invisible' );
                        $progress.show();
                        $upload.text( tips.webuploader.pauseUploading );
                        break;
                    case 'paused':
                        $progress.show();
                        $upload.text( tips.webuploader.continueUploading );
                        break;
                    case 'confirm':
                        $progress.hide();
                        $upload.text( tips.webuploader.startUploading ).addClass( 'disabled' );
                        stats = uploader.getStats();
                        if ( stats.successNum && !stats.uploadFailNum ) {
                            setState( 'finish' );
                            return;
                        }
                        break;
                    case 'finish':
                        stats = uploader.getStats();
                        if ( stats.successNum ) {
                            if (uploadedFiles.length > 0) {
                                if (!config.pick.multiple) {
                                    uploadedFiles = uploadedFiles[0];
                                    if (uploadedFiles.code == 0) {
                                        var btn = chooseObject,
                                            ipt = btn.parent().prev(),
                                            val = ipt.val(),
                                            img = ipt.parent().next().children();
                                        if(img.length > 0){
                                            img.get(0).src = uploadedFiles.url;
                                        }
                                        ipt.val(uploadedFiles.attachment);
                                    } else {
                                        alert(uploadedFiles.msg);
                                    }
                                } else {
                                    var name = chooseObject.parent().children().eq(0).children("input[type=hidden]").attr('name');
                                    var multierr = false;
                                    $.each(uploadedFiles, function(idx, url) {
                                        if (url.code == 0) {
                                            chooseObject.parent().children(".multi-img-details").append('<div class="multi-item"><img src="'+url.url+'" class="img-responsive img-thumbnail cus-img"><input type="hidden" name="'+name+'[]" value="'+url.attachment+'"><em class="close delMultiImage" title="删除这张图片">×</em></div>');
                                        } else {
                                            if (!multierr) {
                                                multierr = true;
                                                alert(url.msg);
                                            } else {
                                                console.log(url.msg);
                                            }
                                        }
                                    });
                                }
                                
                                _modal.modal('hide');

                                resetUploader();
                            }
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                        break;
                }
                updateStatus();
            }
            uploader.onUploadProgress = function( file, percentage ) {
                var $li = $('#'+file.id),
                    $percent = $li.find('.progress span');
                $percent.css( 'width', percentage * 100 + '%' );
                percentages[ file.id ][ 1 ] = percentage;
                updateTotalProgress();
            };
            uploader.onFileQueued = function( file ) {
                fileCount++;
                fileSize += file.size;
                if ( fileCount === 1 ) {
                    $placeHolder.addClass( 'element-invisible' );
                    $statusBar.show();
                }
                addFile( file );
                setState( 'ready' );
                updateTotalProgress();
            };
            uploader.onFileDequeued = function( file ) {
                fileCount--;
                fileSize -= file.size;
                if ( !fileCount ) {
                    setState( 'pedding' );
                }
                removeFile( file );
                updateTotalProgress();
            };
            uploader.on( 'all', function( type ) {
                var stats;
                switch( type ) {
                    case 'uploadFinished':
                        setState( 'confirm' );
                        break;
                    case 'startUpload':
                        setState( 'uploading' );
                        break;
                    case 'stopUpload':
                        setState( 'paused' );
                        break;
                }
            });
            uploader.onError = function( code ) {
                var msg = code;
                switch (code) {
                    case 'Q_EXCEED_NUM_LIMIT':
                        msg = '添加的文件数量超出 fileNumLimit 的设置';
                        break;
                    case 'Q_EXCEED_SIZE_LIMIT':
                        msg = '添加的文件总大小超出了 fileSizeLimit 的设置';
                        break;
                    case 'Q_TYPE_DENIED':
                        msg = '添加的文件类型错误';
                        break;
                    case 'P_DUPLICATE':
                        msg = '添加的文件重复了';
                        break;
                }
                alert( 'Error: ' + msg );
            };
            uploader.onUploadSuccess = function (b, c) {
                return (k++, uploadedFiles.push(c))
            }
            $upload.on('click', function() {
                if ( $(this).hasClass( 'disabled' ) ) {
                    return false;
                }
                if ( state === 'ready' ) {
                    uploader.upload();
                } else if ( state === 'paused' ) {
                    uploader.upload();
                } else if ( state === 'uploading' ) {
                    uploader.stop();
                }
            });
            $info.on( 'click', '.retry', function() {
                uploader.retry();
            } );
            $info.on( 'click', '.ignore', function() {
                alert( 'todo' );
            } );
            $upload.addClass( 'state-' + state );
            updateTotalProgress();

            $('#filePicker2').mouseenter(function(){
                uploader.refresh();
            });;
        }

        function buildModalBody () {
            return '<div role="tabpanel" class="tab-pane upload active" id="upload">' +
                        '<div id="uploader" class="uploader">' +
                            '<div class="queueList">' +
                                '<div id="dndArea" class="placeholder">' +
                                    '<div id="filePicker"></div>' +
                                        '<p id="">' + tips.webuploader.dragHere + '</p>' +
                               ' </div>' +
                            '</div>' +
                            '<div class="statusBar">' +
                                '<div class="infowrap">' +
                                    '<div class="progress" style="display: none;">' +
                                        '<span class="text">0%</span>' +
                                        '<span class="percentage" style="width: 0%;"></span>' +
                                    '</div>' +
                                    '<div class="info">共0张（0B），已上传0张</div>' +
                                    '<div class="accept"></div>' +
                                '</div>' +
                                '<div class="btns">' +
                                    '<div class="uploadBtn btn btn-primary state-pedding" style="margin-top: 4px;">' + tips.webuploader.confirmUse + '</div>' +
                                    '<div class="modal-button-upload" style="float: right; margin-left: 5px;">' +
                                        '<button type="button" class="btn btn-default" data-dismiss="modal">' + tips.cancel + '</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
        }

        function renderModal () {
            var modal_id = config['modal_id'];
            if ($('#' + modal_id).length == 0) {
                return '<div id="' + config['modal_id'] + '" class="fade modal modal-c" role="dialog" tabindex="-1">' +
                        '<div class="modal-dialog cus-size">' +
                            '<div class="modal-content">' +
                                '<div class="modal-header">' +
                                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                                    '<h4 class="modal-title">' + tips.webuploader.uploadImage + '</h4>' +
                                '</div>' +
                                '<div class="modal-body">' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
            } else {
                return false;
            }
        }
        // =====================================================================================
        $('.' + config['modal_id']).on('click', function () {
            chooseObject = $(this);
            _modal.modal('show');
            _modal.find('.modal-body').html('');
            _modal.find('.modal-body').html(buildModalBody());
        });
        $(document).on('click', '.delImage', function () {
            var _this = $(this);
            _this.prev().attr("src", config.defaultImage);
            _this.parent().prev().find("input").val("");
        });
        $(document).on('click', '.delMultiImage', function () {
            $(this).parent().remove();
        });
        // 解决多modal下滚动以及filePicker失效问题
        $(document).on('hidden.bs.modal', '.modal', function () {
            if($('.modal:visible').length) {
                $(document.body).addClass('modal-open');
            }
            $('.modal-c').find('.modal-body').html('');
        });
    };
});
