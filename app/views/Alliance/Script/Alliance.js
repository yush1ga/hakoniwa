;(function(){
    const f = document.forms.Establishment;
    const t = document.querySelector('#AllianceSample p');
    const m = document.querySelector('#Modal');

    const whoami = f.Whoami;
    const sign = f.AllianceSign;
    const color = f.AllianceColor;
    const name = f.AllianceName;
    const confirmBtn = f.Confirm;

    const update = () => {
        let iam = 0;

        for (const option of whoami.options) {
            if (option.value === whoami.value)
                iam = option.textContent;
        }

        t.innerHTML = `<span style="color:${color.value}">${sign.value}</span>${name.value || 'サンプル'}<br><span style="color:${color.value}">${sign.value}</span> ${iam}`;
    };

    const verify = () => {
        let hasError = false;

        // white list
        if ((new RegExp(color.getAttribute('pattern'))).test(color.value)) {
            color.parentNode.parentNode.classList.remove('has-error');
        } else {
            color.parentNode.parentNode.classList.add('has-error');
            hasError =  true;
        }

        // black list
        if (regexDenyingNameWords.test(name.value)
            || denyingNameWords.some(w => name.value.indexOf(w) !== -1)) {
            name.parentNode.parentNode.classList.add('has-error');
            hasError = true;
        } else {
            name.parentNode.parentNode.classList.remove('has-error');
        }

        return !hasError;
    };

    const modal = {
        cover: document.querySelector('#ModalBackdrop'),
        open: targ => {
            document.body.style.paddingRight = modal.paddingWidth + 'px';
            document.body.classList.add('modal-open');
            modal.cover.style.display = 'block';
            modal.cover.classList.add('in');
            targ.style.display = 'block';
            targ.classList.add('in');
        },
        close: targ => {
            document.body.style.paddingRight = '';
            document.body.classList.remove('modal-open');
            modal.cover.style.display = 'none';
            modal.cover.classList.remove('in');
            targ.style.display = 'none';
            targ.classList.remove('in');
        },
        paddingWidth: 0,
        getPaddingWidth: () => {
            const el = document.createElement('div');
            el.setAttribute('style', 'visibility:hidden;position:absolute;top:0;left:0;width:100vw');
            document.body.appendChild(el);
            const vw = parseInt(window.getComputedStyle(el).width, 10);
            el.style.width = '100%';
            const pc = parseInt(window.getComputedStyle(el).width, 10);
            document.body.removeChild(el);

            return vw - pc;
        }
    }
    modal.paddingWidth = modal.getPaddingWidth();

    const confirm = async () => {
        const body = new FormData(f);
        body.set('Password', btoa(unescape(encodeURIComponent(f.Password.value))));

        const fetchOption = {
            method: 'POST',
            mode: 'same-origin',
            headers: {'Accept': 'application/json'},
            body
        };

        let obj = {};
        try {
            const resp = await fetch(f.action, fetchOption);
            obj = await resp.json();
        } catch (e) {
            console.error(e.stack);
            return;
        }

        //[TODO] PHP側に用意する
        const messageTemplate = {
            admin_only: '<strong>同盟の結成には<abbr title="管理者">GM</abbr>権限が必要です</strong>',
            master_can_not_join_other_alliances: '<strong>他の同盟に参加しているときに別の同盟を立ち上げることはできません</strong>',
            wrong_password: '入力されたパスワードが間違っています',
            no_password: 'パスワードを入力してください',
            not_enough_money: '同盟を結成するための経費が不足しています',
            duplicate_name: 'すでに使われている同盟名です',
            duplicate_sign: 'すでに使われている記章です',
            illegal_name: '同盟名に、利用できない文字・単語が含まれています',
            illegal_color: '色の指定が無効です'
        };
        let message = '';

        Object.keys(obj).forEach(key => {
            if(!obj[key].status)
                if(obj[key].hasOwnProperty('messages')) {
                    for(let msg of obj[key].messages) {
                        message += `<li>${messageTemplate[msg] || msg}</li>`;
                    }
                } else {
                    message += `<li>${messageTemplate[obj[key].message] || obj[key].message}</li>`;
                }
        });

        if(message==='') {
            m.querySelector('button[type="submit"]').disabled = !1;
        }
        message = message === '' ? '<p>以下の内容で登録しますか？</p>' : `<ul>${message}</ul>`;

        m.querySelector('.modal-body').innerHTML = message + `<table class="table table-condensed"><tbody><tr><th>記章</th><td><span style="color:${color.value}">${sign.value}</span></td></tr><tr><th>色</th><td><span style="color:${color.value}">◆</span> ${color.value}</td></tr><tr><th>名前</th><td>${name.value}</td></tr></tbody></table>`;
        modal.open(m);
    };





    update();
    f.addEventListener('change', ()=>{
        if(verify()) update();
    });

    m.querySelector('button[name="cancel"]').addEventListener('click', () => {modal.close(m)});

    confirmBtn.addEventListener('click', confirm);

    f.addEventListener('submit', () => {
        f.Password.value = btoa(unescape(encodeURIComponent(f.Password.value)));
        return true;
    });
})();
