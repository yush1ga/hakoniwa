;(function(){

    const modal = {
        frame: document.getElementById('Modal'),
        title: document.getElementById('ModalTitle'),
        body: document.getElementById('ModalBody'),
        footer: document.getElementById('ModalFooter'),
        cover: document.getElementById('ModalBackdrop'),
        open: () => {
            document.body.style.paddingRight = modal.paddingWidth + 'px';
            document.body.classList.add('modal-open');
            modal.cover.style.display = 'block';
            modal.cover.classList.add('in');
            modal.frame.style.display = 'block';
            modal.frame.classList.add('in');
        },
        close: () => {
            document.body.style.paddingRight = '';
            document.body.classList.remove('modal-open');
            modal.cover.style.display = 'none';
            modal.cover.classList.remove('in');
            modal.frame.style.display = 'none';
            modal.frame.classList.remove('in');
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
    };
    modal.paddingWidth = modal.getPaddingWidth();



    const presubmit = async ev => {
        ev.preventDefault();
        const f = ev.target;
        const pwd = f[1];
        pwd.value = btoa(unescape(encodeURIComponent(pwd.value)));

        const body = new FormData(f);
        body.append('mode', 'prejoin');

        const request = new Request(f.action, {
            method: 'POST',
            mode: 'same-origin',
            headers: {'Accept': 'application/json'},
            body
        });

        (new Promise(r => r()))
        .then(() => fetch(request))
        .then(resp => resp.json())
        .then(check => {
            if (check.status !== 'true') {
                pwd.value = '';
                const str = check.errors.map(e => {
                    switch(e) {
                        case 'wrong_password':
                            return '・パスワードが間違っています';
                        case 'you_can_only_join_alliance_only_one':
                            return '・複数の同盟には参加できません';
                        case 'master_can_not_join_other_alliances':
                            return '・同盟主は他の同盟に参加できません';
                        case 'budjet_shortage':
                            return '・資金が不足しています'
                    }
                }).join(`\n`);
                alert(`同盟への参加に失敗しました。\n${str}`);
                return false;
            } else {
                const el = document.createElement('input');
                el.setAttribute('type', 'hidden');
                el.setAttribute('name', 'mode');
                el.setAttribute('value', 'join');
                f.appendChild(el);
                f.submit();
            }
        })
        .catch(e => {
            console.error(e);
            ev.preventDefault();
            return false;
        });

        return true;
    };




    document.getElementById('JoinAlliance').addEventListener('click', ()=>{modal.open();});
    modal.footer.querySelector('button[name="cancel"]').addEventListener('click', ()=>{modal.close();});

    document.querySelector('#Modal form').addEventListener('submit', presubmit);

})();
