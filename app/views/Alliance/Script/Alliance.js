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

        t.innerHTML = `<span style="color:${color.value}">${sign.options[sign.value].textContent}</span>${name.value || 'サンプル'}<br><span style="color:${color.value}">${sign.options[sign.value].textContent}</span> ${iam}`;
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
        open: targ => {
            document.body.style.paddingRight = modal.paddingWidth + 'px';
            document.body.classList.add('modal-open');
            targ.style.display = 'block';
            targ.classList.add('in');
        },
        close: targ => {
            document.body.style.paddingRight = '';
            document.body.classList.remove('modal-open');
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




    update();
    f.addEventListener('change', ()=>{
        if(verify()) update();
    });



    document.forms.ModalConfirm.cancel.addEventListener('click', _ => {modal.close(m)});

    confirmBtn.addEventListener('click', async ev => {
        const body = new FormData(f);
        body.set('Password', btoa(unescape(encodeURIComponent(f.Password.value))));

        const fetchOption = {
            method: 'POST',
            mode: 'same-origin',
            headers: {
                'Accept': 'application/json'
            },
            body
        };

        let json = {};

        try {
            const resp = await fetch(f.action, fetchOption);
            json = await resp.json();
        } catch (e) {
            console.error(e.stack);
            return;
        }

        Object.keys(json).forEach(key => {
            if(!json[key].status)
            console.log(`${key} => ${json[key].message || json[key].messages}`)
            modal.open(m);

        });

        console.dir(json);
    });









})();
