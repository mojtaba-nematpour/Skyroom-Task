const UserListUI = {
    page: `
    <div class="w-100 d-flex flex-column align-items-center justify-content-center gap-3 bg-light rounded-3 p-5">
        <div class="d-flex align-items-center justify-content-center flex-row"><button id="add" type="button" class="mx-3 btn btn-sm btn-primary">افزودن کاربر</button><h4>لیست کاربران</h4></div>
        <table id="users" class="table table-responsive">
            <thead>
                <tr>
                    <th class="w-auto">شناسه</th>
                    <th class="w-auto">نام</th>
                    <th class="w-auto">نام خانوادگی</th>
                    <th class="w-auto">ایمیل</th>
                    <th class="w-auto">شماره همراه</th>
                    <th class="w-auto">عملیات</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>`,
    form: (title = 'افزودن کاربر') => { return `
    <div class="w-100 d-flex flex-column align-items-center justify-content-center gap-3 bg-light rounded-3 p-5">
        <div class="d-flex align-items-center justify-content-center flex-row"><button id="back" type="button" class="mx-3 btn btn-sm btn-primary">بازگشت</button><h4>${title}</h4></div>
        <form id="form" class="needs-validation" method="post">
            <div class="d-flex flex-column align-items-center justify-content-center gap-3 bg-light rounded-3 p-5">
                <div style="min-width: 250px">
                    <label for="firstname" class="form-label">نام</label>
                    <input type="text" minLength="3" maxLength="32" class="form-control w-100" id="firstname" placeholder=""
                           required>
                        <div class="invalid-feedback"></div>
                </div>
                <div style="min-width: 250px">
                    <label for="lastname" class="form-label">نام خانوادگی</label>
                    <input type="text" minLength="3" maxLength="32" class="form-control w-100" id="lastname" placeholder=""
                           required>
                        <div class="invalid-feedback"></div>
                </div>
                <div style="min-width: 250px">
                    <label for="email" class="form-label">ایمیل</label>
                    <input type="email" minLength="5" maxLength="255" class="form-control w-100" id="email" placeholder=""
                           required>
                        <div class="invalid-feedback"></div>
                </div>
                <div style="min-width: 250px">
                    <label for="mobile" class="form-label">شماره</label>
                    <input type="text" minLength="11" maxLength="11" class="form-control w-100" id="mobile" placeholder=""
                           required>
                        <div class="invalid-feedback"></div>
                </div>
                <div style="min-width: 250px">
                    <button type="submit" class="btn btn-primary w-100 btn-sm">ذخیره</button>
                </div>
            </div>
        </form>
    </div>`},
    rows: (rows) => {
        let trs = ''
        for (const row of rows) {
            trs += `<tr>${UserListUI.cols(row)}</tr>`
        }

        return trs
    },
    cols: (columns) => {
        let tds = ''
        for (const column in columns) {
            tds += `<td>${columns[column]}</td>`
        }

        tds += `<td><button type="button" data-user-edit="${columns.id}" class="w-100 m-1 btn btn-sm btn-warning">ویرایش</button><button type="button" data-user-remove="${columns.id}" class="w-100 m-1 btn btn-sm btn-danger">حذف</button></td>`

        return tds
    }
}

const loadUser = () => {
    app.html(UserListUI.page)

    app.find('#add').click(function () {
        app.html(UserListUI.form)

        app.find('#back').click(function () {
            loadUser()
        })

        const form = app.find('#form')
        form.submit(function (e) {
            e.preventDefault()

            let firstname = form.find('#firstname').val()
            let lastname = form.find('#lastname').val()
            let email = form.find('#email').val()
            let mobile = form.find('#mobile').val()

            $.post({
                url: Webserver('users'),
                headers: {"Authorization": `Bearer ${token}`},
                data: {firstname, lastname, email, mobile},
                success: function (data) {
                    loadUser()
                    loadMessages(data)
                },
                error: function (response) {
                    loadMessages(JSON.parse(response.responseText))
                }
            })
        })
    })

    $.get({
        url: Webserver('users'),
        headers: {"Authorization": `Bearer ${token}`},
        success: function (response) {
            app.find('#users').find('tbody').html(UserListUI.rows(response.data))

            app.find('#users').find('[data-user-edit]').click(function (e) {
                const id = e.target.getAttribute('data-user-edit')

                const children = e.target.parentElement.parentElement.children

                let firstname = children.item(1).innerText
                let lastname = children.item(2).innerText
                let email = children.item(3).innerText
                let mobile = children.item(4).innerText

                app.html(UserListUI.form('ویرایش کاربر'))

                const form = app.find('#form')

                form.find('#firstname').val(firstname)
                form.find('#lastname').val(lastname)
                form.find('#email').val(email)
                form.find('#mobile').val(mobile)

                app.find('#back').click(function () {
                    loadUser()
                })

                form.submit(function (e) {
                    e.preventDefault()

                    let firstname = form.find('#firstname').val()
                    let lastname = form.find('#lastname').val()
                    let email = form.find('#email').val()
                    let mobile = form.find('#mobile').val()

                    $.post({
                        url: Webserver(`users/${id}`),
                        headers: {"Authorization": `Bearer ${token}`},
                        data: {firstname, lastname, email, mobile},
                        success: function (data) {
                            loadUser()
                            loadMessages(data)
                        },
                        error: function (response) {
                            loadMessages(JSON.parse(response.responseText))
                        }
                    })
                })
            })

            app.find('#users').find('[data-user-remove]').click(function (e) {
                const id = e.target.getAttribute('data-user-remove')

                $.ajax({
                    url: Webserver(`users/${id}`),
                    type: 'DELETE',
                    headers: {"Authorization": `Bearer ${token}`},
                    success: function (response) {
                        e.target.parentElement.parentElement.remove()
                        loadMessages(response)
                    }
                })
            })
        }
    })
}
