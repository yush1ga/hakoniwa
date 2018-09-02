;(function(){
    const NoticeBoard = document.forms.NoticeFromAdmin;
    const EditTrigger = document.getElementById('js_editNoticeFromAdmin');
    const SubmitTrigger = document.getElementById('js_submitNoticeFromAdmin');

    const NoticeTextarea = NoticeBoard.querySelector('textarea');
    const PasswordOuterDOM = NoticeBoard.querySelector('.form-inline');
    const Password = NoticeBoard.querySelector('input');

    let isTextareaEditable = false;

    let notifyText = NoticeTextarea.value;
    const notifyBgColor = NoticeTextarea.style.backgroundColor;

    const editTriggered = (ev) => {
        NoticeTextarea.readOnly = isTextareaEditable;
        EditTrigger.innerHTML = isTextareaEditable? '編集' : 'やめる';
        PasswordOuterDOM.style.display = isTextareaEditable? 'none' : 'block';
        Password.disabled = isTextareaEditable;
        SubmitTrigger.disabled = isTextareaEditable;
        isTextareaEditable = !isTextareaEditable;
        if (isTextareaEditable) {
            notifyText = NoticeTextarea.value;
            NoticeTextarea.style.backgroundColor = "#fff";
        } else {
            NoticeTextarea.value = notifyText;
            NoticeTextarea.style.backgroundColor = notifyBgColor;
        }
    };

    const presubmit = async ev => {
        NoticeBoard.Pwd.value = btoa(unescape(encodeURIComponent(NoticeBoard.Pwd.value)));

        const body = new FormData(NoticeBoard);
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
                alert('パスワードが間違っています');
                return false;
            } else {
                const el = document.createElement('input');
                el.setAttribute('type', 'hidden');
                el.setAttribute('name', 'mode');
                el.setAttribute('value', 'changeInfo');
                NoticeBoard.appendChild(el);
                NoticeBoard.submit();
            }
        } catch (e) {
            console.error(e);
            ev.preventDefault();
            return false;
        }

        return true;
    };

    EditTrigger.addEventListener('click', editTriggered);
    SubmitTrigger.addEventListener('click', presubmit);
})();
