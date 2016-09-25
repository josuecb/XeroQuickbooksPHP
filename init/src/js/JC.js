var timer = null;
var extension_root = '/quickbooks'; // change to your root extension for example localhost/example_folder/repository_files
// your extension root is '/example_folder'

$(function () {
    // Runnable uses dynamic URLs so we need to detect our current //
    // URL to set the grantUrl value   ########################### //
    /*######*/
    var parser = document.createElement('a');
    /*#########*/
    /*######*/
    parser.href = document.url;
    /*########################*/
    // end runnable specific code snipit ##########################//
    intuit.ipp.anywhere.setup({
        menuProxy: '',
        grantUrl: 'http://' + parser.hostname + extension_root + '/oauth.php?start=t'
        // outside runnable you can point directly to the oauth.php page
    });
});

function Disconnect() {
    window.location.href = 'http://' + parser.hostname + extension_root + 'Disconnect.php';
    console.log("button pressed");
}

function Reconnect() {
    window.location.href = 'http://' + parser.hostname + extension_root + '/Reconnect.php';
}

function openWindow() {
    var popup = window.open(extension_root + '/init/src/api/xero-sdk/public.php', null, "height=600,width=450,status=yes");
    timer = setInterval(function () {
        checkChild(popup);
        console.log(popup);
    }, 1000);


    var xhr = new XMLHttpRequest();
    var fileForm = new FormData();
    fileForm.append('authenticate', '1');

    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            var response = readBody(xhr);
            if (response.length > 0 && response.length < 100) {
                popup.location.href = response;
            } else {
                window.close();
            }
        }
    };
    //initiate request
    xhr.open('post', extension_root + '/init/src/api/xero-sdk/public.php', true);
    xhr.send(fileForm);
}

function checkChild(child) {
    if (child.closed) {
        window.location.href = extension_root + '/success';
        clearInterval(timer);
    } else {
        console.log("Child is alive");
    }
}

function readBody(xhr) {
    var data;
    if (!xhr.responseType || xhr.responseType === "text") {
        data = xhr.responseText;
    } else if (xhr.responseType === "document") {
        data = xhr.responseXML;
    } else {
        data = xhr.response;
    }
    return data;
}

function setValue(value) {
    document.getElementById('value').value = value;
}