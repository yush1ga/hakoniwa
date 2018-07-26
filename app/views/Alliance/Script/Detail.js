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



    document.getElementById('JoinAlliance').addEventListener('click', ()=>{

        modal.open();
    });

    modal.footer.querySelector('button[name="cancel"]').addEventListener('click', ()=>{modal.close();});

})();
