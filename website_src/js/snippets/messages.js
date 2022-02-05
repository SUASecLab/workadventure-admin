const quillToolbarSettings = [
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote', 'code-block'],
    [{
        'header': 1
    }, {
        'header': 2
    }],
    [{
        'list': 'ordered'
    }, {
        'list': 'bullet'
    }],
    [{
        'script': 'sub'
    }, {
        'script': 'super'
    }],
    [{
        'indent': '-1'
    }, {
        'indent': '+1'
    }],
    [{
        'direction': 'rtl'
    }],
    [{
        'size': ['small', false, 'large', 'huge']
    }],
    [{
        'header': [1, 2, 3, 4, 5, 6, false]
    }],
    [{
        'color': []
    }, {
        'background': []
    }],
    [{
        'font': []
    }],
    [{
        'align': []
    }],
    ['clean'],
    ['link', 'image', 'video']
];

window.onload = function () {
    $("#messages").load("snippets/messages.php", addListener);
}

function addListener() {
    const addMessageButton = document.getElementById("addMessageButton");
    addMessageButton.addEventListener("click", addMessage);
}

function removeMessage(id) {
    $("#messages").load("snippets/messages.php", {
        "action": "removeMessage",
        "id": id
    }, addListener);
}

function addMessage() {
    const message = JSON.stringify(quill.getContents(0, quill.getLength()));

    $("#messages").load("snippets/messages.php", {
        "action": "addMessage",
        "message": message
    }, addListener);
}