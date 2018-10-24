class Modal {
    constructor(query) {
        this.modal = document.querySelector(query);
        this.cover = document.getElementById('ModalBackdrop');
        this.paddingWidth = this.getPaddingWidth();

        this.setEvents();
    }

    open() {
        document.body.style.paddingRight = modal.paddingWidth + 'px';
        document.body.classList.add('modal-open');
        this.cover.style.display = 'block';
        this.cover.classList.add('in');
        this.modal.style.display = 'block';
        this.modal.classList.add('in');
    }

    close() {
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');
        this.cover.classList.remove('in');
        this.cover.style.display = 'none';
        this.modal.style.display = 'none';
        this.modal.classList.remove('in');
    }

    getPaddingWidth() {
        const el = document.createElement('div');
        el.setAttribute('style', 'visibility:hidden;position:absolute;top:0;left:0;width:100vw');
        document.body.appendChild(el);
        const vw = parseInt(window.getComputedStyle(el).width, 10);
        el.style.width = '100%';
        const pc = parseInt(window.getComputedStyle(el).width, 10);
        document.body.removeChild(el);

        return vw - pc;
    }

    setEvents() {
        [...this.modal.querySelectorAll('[aria-label="Close"]')].forEach(el => {
            el.addEventListener('click', ()=>{this.close()});
        })
    }
}
