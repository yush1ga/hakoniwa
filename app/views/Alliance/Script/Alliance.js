;(function(){
    const f = document.forms.Establishment;
    const t = document.querySelector('#AllianceSample p');

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

        t.innerHTML = `<span style="color:${color.value}">${sign.options[sign.value].textContent}</span> ${name.value || 'サンプル'}<br><span style="color:${color.value}">${sign.options[sign.value].textContent}</span> ${iam}`;
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


    update();
    f.addEventListener('change', ()=>{
        if(verify()) update();
    });

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
            console.dir(e);
        }

        console.dir(json);
    });









})();
