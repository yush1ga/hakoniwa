;(function(){
    const NoticeBoard = document.forms.NoticeFromAdmin;
    const EditTrigger = document.getElementById('js_editNoticeFromAdmin');
    const SubmitTrigger = document.getElementById('js_submitNoticeFromAdmin');

    const PasswordOuterDOM = NoticeBoard.querySelector('.form-inline');
    const Password = NoticeBoard.querySelector('input');

    let isTextareaEditable = false;

    const editTriggered = (ev) => {
        NoticeBoard.querySelector('textarea').readOnly = isTextareaEditable;
        EditTrigger.innerHTML = isTextareaEditable? '編集' : 'やめる';
        PasswordOuterDOM.style.display = isTextareaEditable? 'none' : 'block';
        Password.disabled = isTextareaEditable;
        SubmitTrigger.disabled = isTextareaEditable;
        isTextareaEditable = !isTextareaEditable;
    };

    const submit = async (ev) => {
        NoticeBoard.Pwd.value = btoa(unescape(encodeURIComponent(NoticeBoard.Pwd.value)));
        let body = new FormData(NoticeBoard);
        body.append('PreCheck', 'true');
        body.append('mode', 'changeInfo');

        const request = new Request(NoticeBoard.action, {
            method: 'POST',
            mode: 'same-origin',
            headers: {'Accept': 'application/json'},
            body
        });

        let check = "";
        try {
            const resp = await fetch(request);
            check = await resp.text();
            if (check === 'false') {
                NoticeBoard.Pwd.value = '';
                ev.preventDefault();

                return false;
            }
        } catch (e) {
            console.error(e);
            ev.preventDefault();
            return false;
        } finally {
            alert('パスワードが間違っています');
            return false;
        }

        return true;

    };

    EditTrigger.addEventListener('click', editTriggered);
    NoticeBoard.addEventListener('submit', submit);


})();
