const LoginUI = `
<form id="login" class="needs-validation" method="post">
    <div class="d-flex flex-column align-items-center justify-content-center gap-3 bg-light rounded-3 p-5">
        <h4 class="mb-3">ورود به پنل</h4>
        <div style="min-width: 250px">
            <label for="username" class="form-label">نام ‌کاربری</label>
            <input type="text" minLength="3" maxLength="32" class="form-control w-100" id="username" placeholder=""
                   required>
                <div class="invalid-feedback"></div>
        </div>
        <div style="min-width: 250px">
            <label for="password" class="form-label">رمزعبور</label>
            <input type="password" minLength="6" maxLength="32" class="form-control w-100" id="password"
                   placeholder="" required>
                <div class="invalid-feedback"></div>
        </div>
        <div style="min-width: 250px">
            <button type="submit" class="btn btn-primary w-100 btn-sm">ورود</button>
        </div>
    </div>
</form>`

const loadLogin = () => {
    app.html(LoginUI)

    const login = app.find('#login')
    login.submit(function (e) {
        e.preventDefault()

        let username = login.find('#username').val()
        let password = login.find('#password').val()

        $.post({
            url: Webserver('auth/login'),
            data: {username, password},
            success: function (data) {
                if (data?._token !== null) {
                    localStorage.setItem('_token', data._token)
                    token = data._token

                    loadUser()

                    loadMessages(data)
                }
            },
            error: function (response) {
                loadMessages(JSON.parse(response.responseText))
            }
        })
    })
}
