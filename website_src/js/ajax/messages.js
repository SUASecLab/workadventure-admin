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
    $("#messages").load("snippets/messages.php");
}

function removeMessage(id) {
    $("#messages").load("snippets/messages.php",
    {
        "message_id": id
    });
}

function addMessage() {
    const message = document.getElementById("message").value;

    $("#messages").load("snippets/messages.php",
    {
        "message": message
    });
}