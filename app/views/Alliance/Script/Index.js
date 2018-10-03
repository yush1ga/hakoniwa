;(function(){

// php including
<?php require VIEWS."Script/Modal.js"?>;
const modal = new Modal('#ModalWithdrawal');
const f = document.getElementById('fWithdrawal');

const whoami = f.Whoami;
const pwd = f.Pwd;
const al = f.Alliance;
const targetAl = f.querySelector('[name="jsAllianceName"]');

const setAllianceForWithdrawal = () => {
    targetAl.innerHTML = `${al.options[al.selectedIndex].textContent}`;
    targetAl.style.color = al.options[al.selectedIndex].getAttribute('data-c');
};



document.getElementById('Withdrawal').addEventListener('click', () => {
    event.preventDefault();
    modal.open();
});
setAllianceForWithdrawal();
al.addEventListener('changed', setAllianceForWithdrawal);
})();
