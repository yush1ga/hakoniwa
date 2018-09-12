;(function(){

// php including
<?php require VIEWS."Script/Modal.js"?>;

const modal = new Modal('#ModalWithdrawal');

document.getElementById('Withdrawal').addEventListener('click', () => {
    event.preventDefault();
    modal.open();
});
})();
