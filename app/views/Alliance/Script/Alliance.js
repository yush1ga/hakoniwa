;(function(d){
    const f = document.forms[0];
    const t = document.querySelector('#AllianceSample p');

    const whoami = f.querySelector('#Whoami');
    const sign = f.querySelector('#AllianceSign');
    const color = f.querySelector('#AllianceColor');
    const name = f.querySelector('#AllianceName');

    const update = () => {
        let iam = 0;

        for (const option of whoami.options) {
            if (option.value === whoami.value)
                iam = option.textContent;
        }

        t.innerHTML = `<span style="color:${color.value}">${sign.options[sign.value].textContent}</span> ${name.value}<br><span style="color:${color.value}">${sign.options[sign.value].textContent}</span> ${iam}`;
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
        if (name.value === ''
            || regexDenyingNameWords.test(name.value)
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
})();
