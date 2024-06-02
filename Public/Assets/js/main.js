let token = localStorage.getItem('_token')

const app = $('#app')

const Webserver = (endpoint) => {
    return 'http://127.0.0.1:8000/v1/' + endpoint
}

const messages = (messages, type) => {
    $.each(messages, function(key, value) {
        const box = $(`<div class="col-12 col-sm-6 my-1"></div>`)
        const message = $(`<div class="w-100 rounded-3 bg-${type} text-white py-2 px-3"></div>`).html(value);

        box.append(message)

        app.append(box);

        box.fadeIn();

        setTimeout(function() {
            box.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    });
}

const loadMessages = (data)  => {
    if (typeof data !== "object") {
        return;
    }

    if (typeof data.validations === "object") {
        messages(data.validations, 'danger')
    }

    if (typeof data.errors === "object") {
        messages(data.errors, 'danger')
    }

    if (typeof data.error === "object") {
        messages(data.error, 'danger')
    }

    if (typeof data.messages === "object") {
        messages(data.messages, 'primary')
    }
}
