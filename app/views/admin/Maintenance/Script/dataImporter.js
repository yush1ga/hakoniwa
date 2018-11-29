// import * as _ from 'lodash';
// import axios from 'axios';
$(document).ready(()=>{
    $('#ImportZip').on('change', e => {
        const file = e.target.files[0];
        if (!(/^.+\.zip$/i.test(file.name) && e.target.files[0].size < $max_size.r.file)) {
            return !1;
        }
        $('#PostZip').attr('disabled', false);
    });
    $('#PostZip').on('click', e => {
        e.preventDefault();
        const formData = new FormData(document.DataImporter);
        const config = {timeout: 8000};

        axios.post(document.DataImporter.action, formData, config)
            .then(resp => {
                console.dir(resp);
            })
            .catch(err => {
                console.dir(err);
            })

    });
});
