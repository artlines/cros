var editor;
var dialog;
var imageElement;
var linkElement;
var tableStatus = null;
var needCancelLoad = false;
var funcAddUploadSlot = function () {
    createUploadSlot();
    var upload_info = moveUploadSlotToQuery();

    insertSlotImage(upload_info.id);

    // добавление задания в очередь
    upload_uuids.push(upload_info);
};

var funcDummyElementCreate = function (evt) {
    dialog.getContentElement('tab2', 'uploadslot').getInputElement().setAttributes({
        accept: CKEDITOR.nagupload.accept
    });

    console.log(editor);

    imageElement = editor.document.createElement('img');
    imageElement.setAttributes(
        {
            src: '/images/null.gif',
            _cke_saved_src: '/images/null.gif',
            style: 'background:url(/images/loader.gif) no-repeat scroll 50% 50% #8588F5',
            id: 'img_dummy'

        }
    );

    linkElement = editor.document.createElement('a',
        {
            attributes: {
                id: 'link_dummy'
            }
        });
    linkElement.append(imageElement, false);
    editor.insertElement(linkElement);

    funcDummyElementUpdate();
};

var funcDummyElementUpdate = function (evt) {
    if (Param = dialog.getContentElement('tab2', 'simg_size_w'))
        W = Param.getValue();
    if (Param = dialog.getContentElement('tab2', 'simg_size_h'))
        H = Param.getValue();

    if (W == 0)
        W = 100;
    if (H == 0)
        H = W;
    imageElement.setAttributes({
        width: W,
        height: H
    });
};

function insertSlotImage(uuid) {
    imageElement.setAttributes({
        id: 'img_' + uuid,
    });
    linkElement.setAttributes({
        id: 'link_' + uuid,
    });


    funcDummyElementCreate();
}

function dump(obj) {
    var out = "";
    if (obj && typeof(obj) == "object") {
        for (var i in obj) {
            out += i + ": __";
        }
    }
    else {
        out = obj;
    }
    alert(out);
}

var interval = window.setInterval(function () {
    fetch_all();
}, 500);
var upload_uuids = [];
var uploading_uuid = "";
var arIFrames = document.getElementsByTagName('iFrame');
var iframe_obj = arIFrames[arIFrames.length - 1];
var framedocument = iframe_obj.contentWindow.document;


function naguploaded(obj) {
    elStatus = document.getElementById('status_' + obj.id);
    framedocument = editor.document.$;
    elImg = framedocument.getElementById('img_' + obj.id);
    elLink = framedocument.getElementById('link_' + obj.id);
    if (obj.status == 'success') {
        FileTextSize = Math.round(obj.size / 100) / 10 + ' Кб';
        if (elImg) {
            if (obj.type == 'image') {
                elImg.src = obj.spath;
                elImg.setAttribute('width', obj.swidth);
                elImg.setAttribute('height', obj.sheight);
                elImg.removeAttribute('style');
                elImg.removeAttribute('_cke_saved_src');
                elImg.removeAttribute('id');
            }
            else {
                elImg.parentNode.removeChild(elImg);
                elLink.innerHTML = 'Файл ' + FileTextSize;
            }
        }
        if (elLink) {
            elLink.setAttribute('href', obj.path);
            elLink.removeAttribute('id');
        }
        if (elStatus) {
            elStatus.innerHTML = 'Готово';
        }
        if (el = document.getElementById('filesize_' + obj.id)) {
            el.innerHTML = FileTextSize;
        }
    }
    else if (obj.status == 'faild') {
        if (elStatus)
            elStatus.innerHTML = 'Ошибка';
    }
    else {
        if (elStatus)
            elStatus.innerHTML = 'Ошибка на стороне сервера';
        newNode = document.createElement('b');
        newNode.innerHTML = '[Ошибка загрузки файла]';
        elLink.parentNode.insertBefore(newNode, elLink);
        elLink.parentNode.removeChild(elLink);
    }
    needCancelLoad = false;
    del = document.getElementById('file_' + obj.id);
    if (del)
        del.parentNode.removeChild(del);
    uploading_uuid = '';
}

function img_notuploaded(id) {
    framedocument.getElementById('img_' + id).setAttribute('style', 'url("/images/cancel.png") no-repeat scroll 50% 50% #E5CCCC');
    del = document.getElementById('file_' + id);
    del.parentNode.removeChild(del);
    uploading_uuid = '';
}

function getRandomId() {
    id = "";
    for (i = 0; i < 32; i++) {
        id += Math.floor(Math.random() * 16).toString(16);
    }
    return id;
}
function createUploadSlot() {
    newslot = editor.document.createElement('input');
    newslot.setAttributes({
        accept: CKEDITOR.nagupload.accept
    });
    newslot.$.addEventListener('change', funcAddUploadSlot);

    arProps = ['id', 'aria-labelledby', 'type', 'name', 'size'];
    for (var iP in arProps) {
        prop = arProps[iP];
        newslot.setAttribute(prop, dialog.getContentElement('tab2', 'uploadslot').getInputElement().getAttribute(prop));
    }
    dialog.getContentElement('tab2', 'uploadslot').getInputElement().$.parentElement.appendChild(newslot.$);
}


function moveUploadSlotToQuery() {
    uuid = getRandomId();
    dialog.getContentElement('tab2', 'uploadslot').getInputElement().setAttributes(
        {
            id: 'file_' + uuid,
            name: 'file_' + uuid,
            'aria-labelledby': ''
        }
    );

    if (!tableStatus) {
        label = editor.document.createElement('label');
        label.setAttributes({class: "forinput"});
        label.$.innerHTML = "Загружаемые файлы";
        document.getElementById(editor.name + 'nagupload').appendChild(label.$);
        tableStatus = editor.document.createElement('table');
        tableStatus.setAttributes({style: "padding : 10px"});
        document.getElementById('nagupload').appendChild(tableStatus.$);

        tr = editor.document.createElement('tr');
        tableStatus.append(tr);

        td = editor.document.createElement('td');
        td.$.innerHTML = "";
        tr.append(td);

        td = editor.document.createElement('td');
        td.$.innerHTML = "Имя файла";
        tr.append(td);

        td = editor.document.createElement('td');
        td.$.innerHTML = "Статус";
        tr.append(td);


    }
    tr = editor.document.createElement('tr');
    tableStatus.append(tr);

    td = editor.document.createElement('td');
    td.setAttributes({id: 'filesize_' + uuid});
    td.$.innerHTML = "Загрузка";
    tr.append(td);

    td = editor.document.createElement('td');
    td.$.innerHTML = dialog.getContentElement('tab2', 'uploadslot').getValue().replace(/^.*[\\\/]/, '');
    tr.append(td);

    td = editor.document.createElement('td');
    td.setAttributes({id: 'status_' + uuid});
    td.$.innerHTML = "Ожидание";
    tr.append(td);


    document.getElementById('upload_queue').appendChild(
        dialog.getContentElement('tab2', 'uploadslot').getInputElement().$
    );
    if (Param = dialog.getContentElement('tab2', 'img_size_w'))
        size_w = Param.getValue();
    if (Param = dialog.getContentElement('tab2', 'img_size_h'))
        size_h = Param.getValue();
    if (Param = dialog.getContentElement('tab2', 'simg_size_w'))
        size_sw = Param.getValue();
    if (Param = dialog.getContentElement('tab2', 'simg_size_h'))
        size_sh = Param.getValue();

    do_small = 1;

    return {
        id: uuid,
        size_w: size_w,
        size_h: size_h,
        size_sw: size_sw,
        size_sh: size_sh,
        do_small: do_small,
    };
}

function addUpload() {
    /* generate random progress-id */
    uuid = "";
    for (i = 0; i < 32; i++) {
        uuid += Math.floor(Math.random() * 16).toString(16);
    }
    // помещение файла в очередь
    slot = document.getElementById('uploadselector');
    slot.setAttribute('id', 'file_' + uuid);
    slot.setAttribute('name', 'file_' + uuid);
    document.getElementById('upload_queue').appendChild(
        slot
    );

    newslot = document.createElement("input");
    newslot.setAttribute('id', 'uploadselector');
    newslot.setAttribute('name', 'uploadselector');
    newslot.setAttribute('type', 'file');
    newslot.setAttribute('size', '60');
    newslot.setAttribute('onchange', "if( getElementById('multy_add')) addUpload()");
    document.getElementById('uploadselectorcontainer').appendChild(
        newslot
    );

    // Добавление временного изображения
    var size_w = parseInt(document.getElementById('img_size_w').value);
    var size_h = parseInt(document.getElementById('img_size_h').value);
    var size_sw = parseInt(document.getElementById('simg_size_w').value);
    var size_sh = parseInt(document.getElementById('simg_size_h').value);
    var do_small = document.getElementById('do_small_img').checked ? 1 : 0;
    var size_sw2 = ( !size_sw ) ? 300 : size_sw;
    var size_sh2 = ( !size_sh ) ? 300 : size_sh;
    var img = editor.document.createElement('img',
        {
            attributes: {
                src: '/images/null.gif',
                _cke_saved_src: '/images/null.gif',
                style: 'background:url(/images/loader.gif) no-repeat scroll 50% 50% #E5EEF5',
                width: size_sw2,
                height: size_sh2,
                id: 'img_' + uuid
            }
        });
    var alink = editor.document.createElement('a',
        {
            attributes: {
                href: '#',
                target: '_blank',
                id: 'link_' + uuid
            }
        });
    alink.append(img, false);
    editor.insertElement(alink);
    // добавление задания в очередь
    upload_uuids.push({
        id: uuid,
        size_w: size_w,
        size_h: size_h,
        size_sw: size_sw,
        size_sh: size_sh,
        do_small: do_small,
        id: uuid,
    });


    return;

}
function doCancelCurrentUpload() {
    top.naguploaded({
        id: uploading_uuid.id,
        status: 'servererror'
    });
}

function doCancelLoad() {
    if (needCancelLoad && uploading_uuid) {
        doCancelCurrentUpload();
    }
    else {
    }
}

function iframe_load_handler() {
    window.setTimeout(function () {
        needCancelLoad = true;
        doCancelLoad();
    }, 1000);
}

function fetch_all() {
    if (uploading_uuid == '' && upload_uuids.length) {
        // Запустить следующий файл
        uploading_uuid = upload_uuids.pop();
        fform = document.getElementById('uploadform');
        fform.appendChild(
            document.getElementById('file_' + uploading_uuid.id)
        );
        fform.action = "http://shop.nag.ru/doupload/article/img?X-Progress-ID=" + uploading_uuid.id
            + '&w=' + uploading_uuid.size_w
            + '&h=' + uploading_uuid.size_h
            + '&sw=' + uploading_uuid.size_sw
            + '&sh=' + uploading_uuid.size_sh
            + '&sd=' + uploading_uuid.do_small
            + '&newsid=' + 1
        ;
//uploadiframe
        iframe_obj = document.getElementById('uploadiframe');
        if (iframe_obj.addEventListener)
            iframe_obj.addEventListener('load', iframe_load_handler, false);
        else if (iframe_obj.attachEvent)
            iframe_obj.attachEvent('onload', iframe_load_handler);
        else
            iframe_obj.onload = iframe_load_handler;


        fform.submit();
        fform.domain = 'nag.ru';

    }

    if (uploading_uuid)
        fetch();
}

function fetch() {
    req = new XMLHttpRequest();
    req.open("GET", "http://shop.nag.ru/progress", 1);
    req.setRequestHeader("X-Progress-ID", uploading_uuid.id);
    req.onreadystatechange = function () {
        if (req.readyState == 4) {
            if (req.status == 200) {
                var upload = eval(req.responseText);


                /* change the width if the inner progress-bar */
                if (upload.state == 'uploading') {
                    bar = document.getElementById('progressbar');
                    w = upload.received / upload.size * 100;
                    bar.style.width = w + '%';
                    percent = Math.round(upload.received / upload.size * 1000) / 10 + '%';
                    document.getElementById('tp').innerHTML = upload.state + ': ' + percent;
                    document.getElementById('status_' + uploading_uuid.id).innerHTML = percent;
                    document.getElementById('filesize_' + uploading_uuid.id).innerHTML = Math.round(upload.size / 100) / 10 + ' Кб';

                }
                /* we are done, stop the interval */
                if (upload.state == 'done') {
                    // alert('TODO: Вставить файл и закрыть окно');
                }
                if (upload.state == 'error') {
                    //alert('error');
                    doCancelCurrentUpload();
                }
            }
        }
    };
    req.send(null);
}

imgsb = [
    [0, 0],
    [640, 480],
    [800, 600],
    [1024, 768],
    [1280, 1024]
];
imgss = [
    [400, 0],
    [0, 400],
    [300, 0],
    [0, 300],
    [0, 200]
];
function set_big_sizes(w, h) {
    document.getElementById('img_size_w').value = w;
    document.getElementById('img_size_h').value = h;
}
function set_small_sizes(w, h) {
    document.getElementById('simg_size_w').value = w;
    document.getElementById('simg_size_h').value = h;
}
pre_setups_big = '';
for (i = 0; i < imgsb.length; i++) {
    pre_setups_big += '<span onClick="set_big_sizes(' + imgsb[i][0] + ',' + imgsb[i][1] + ')" class="cke_dialog_ui_button">' + imgsb[i][0] + '*' + imgsb[i][1] + '</span>';
}
pre_setups_small = '';
for (i = 0; i < imgss.length; i++) {
    pre_setups_small += '<span onClick="set_small_sizes(' + imgss[i][0] + ',' + imgss[i][1] + ')" class="cke_dialog_ui_button">' + imgss[i][0] + '*' + imgss[i][1] + '</span>';
}

CKEDITOR.dialog.add('naguploadimage', function (editor) {
    top.editor = editor;

    var labelId = CKEDITOR.tools.getNextId() + '_upload_emtions_label';
    var lang = {
        options: 'options'
    };
    var html =
        [
            '<div>' +
            '<span id="' + labelId + '" class="cke_voice_label">' + lang.options + '</span>',
            '<table role="listbox" aria-labelledby="' + labelId + '" style="width:100%;height:100%" cellspacing="2" cellpadding="2"',
            CKEDITOR.env.ie && CKEDITOR.env.quirks ? ' style="position:absolute;"' : '',
            '><tbody>'
        ];

    html.push('<tr>');

    html.push('</tr>');

    html.push('</tbody></table></div>');

    CKEDITOR.nagupload = {accept: 'image/jpeg,image/png,image/gif'};
    if (editor.config.nagupload && editor.config.nagupload == 'allfiles') {
        CKEDITOR.nagupload.accept = '';
    }
    var onOk = function () {
        if (document.getElementById('uploadselector').value != '') {
            addUpload();
        } else {
            alert('Не выбран файл для загрузки');
            return false;
        }
        return true;
    };


    function funcDummyElementRemove() {
        imageElement.remove();
        linkElement.remove();
    };

    var onClickSetPredefined = function () {
        if (Param = dialog.getContentElement('tab2', 'img_size_w'))
            Param.setValue(this.w);
        if (Param = this.getDialog().getContentElement('tab2', 'img_size_h'))
            Param.setValue(this.h);
    };
    var onClickSetPredefinedS = function () {
        if (Param = this.getDialog().getContentElement('tab2', 'simg_size_w'))
            Param.setValue(this.w);
        if (Param = this.getDialog().getContentElement('tab2', 'simg_size_h'))
            Param.setValue(this.h);
    };

    var fileSelector = [

        {
            id: 'tab2',
            label: 'В разработке',
            elements: [
                {
                    type: 'html',
                    html: 'Размеры изображения на которое будет идти ссылка'
                },
                {
                    type: 'hbox',
                    widths: ['15%', '15%', '70%'],
                    label: 'label',
                    children: [
                        {
                            id: 'img_size_w',
                            label: 'Ширина',
                            type: 'text',
                            width: '45px',
                            'default': '0',
                            isChanged: false
                        },
                        {
                            id: 'img_size_h',
                            label: 'Высота',
                            type: 'text',
                            width: '45px',
                            'default': '0',
                            isChanged: false
                        },
                        {
                            type: 'vbox',
                            padding: 0,
                            children: [
                                {
                                    type: 'html',
                                    html: 'Предустановки',
                                    padding: 0
                                },
                                {
                                    type: 'hbox',
                                    children: [
                                        {
                                            type: 'button',
                                            label: 'Оригинал',
                                            title: 'Вставить изображение без изменений',
                                            onClick: onClickSetPredefined,
                                            w: '0',
                                            h: '0'
                                        },
                                        {
                                            type: 'button',
                                            label: '1024x768',
                                            onClick: onClickSetPredefined,
                                            w: 1024,
                                            h: 768
                                        },
                                        {
                                            type: 'button',
                                            label: '1280x1024',
                                            onClick: onClickSetPredefined,
                                            w: 1280,
                                            h: 1024
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                {
                    type: 'html',
                    html: 'Размеры создаваемой миниатюры, для предпросмотра'
                },
                {
                    type: 'hbox',
                    widths: ['15%', '15%', '70%'],
                    label: 'label',
                    children: [
                        {
                            id: 'simg_size_w',
                            label: 'Ширина',
                            type: 'text',
                            width: '45px',
                            'default': '400',
                            isChanged: false,
                            onChange: funcDummyElementUpdate
                        },
                        {
                            id: 'simg_size_h',
                            label: 'Высота',
                            type: 'text',
                            width: '45px',
                            'default': '0',
                            isChanged: false,
                            onChange: funcDummyElementUpdate
                        },
                        {
                            type: 'vbox',
                            padding: 0,
                            children: [
                                {
                                    type: 'html',
                                    html: 'Предустановки',
                                    padding: 0
                                },
                                {
                                    type: 'hbox',
                                    children: [
                                        {
                                            type: 'button',
                                            label: 'Оригинал',
                                            title: 'Вставить изображение без миниатюры',
                                            onClick: onClickSetPredefinedS,
                                            w: '0',
                                            h: '0'
                                        },
                                        {
                                            type: 'button',
                                            label: '400x0',
                                            onClick: onClickSetPredefinedS,
                                            w: '400',
                                            h: '0'
                                        },
                                        {
                                            type: 'button',
                                            label: '500x0',
                                            onClick: onClickSetPredefinedS,
                                            w: '500',
                                            h: '0'
                                        },
                                        {
                                            type: 'button',
                                            label: '640x0',
                                            onClick: onClickSetPredefinedS,
                                            w: '640',
                                            h: '0'
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                {
                    type: 'hbox',
                    align: 'right',
                    children: [
                        {
                            id: 'uploadslot',
                            type: 'file',
                            label: 'Выберите файл',
                            isChanged: false,
                            onChange: funcAddUploadSlot
                        }
                    ]
                }
            ]
        }];

    return {
        // Название окна
        title: 'Загрузка изображения',
        // Размеры окна
        minWidth: 402,
        minHeight: 130,
        onLoad: function (event) {
            dialog = event.sender;
        },
        onShow: funcDummyElementCreate,
        onOk: funcDummyElementRemove,
        onCancel: funcDummyElementRemove,
        contents: fileSelector,
        buttons: [
            CKEDITOR.dialog.okButton
        ]
    };
});
