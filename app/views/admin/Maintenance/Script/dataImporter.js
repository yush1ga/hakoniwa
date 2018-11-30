// import * as _ from 'lodash';
// import axios from 'axios';
$(document).ready(()=>{
    const axiosConf = {timeout: 8000};
    const getDate = s => {
        const d = new Date(s*1000);
        return new Intl.DateTimeFormat('ja-JP', {
            year: "numeric",
            month: "numeric",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            timeZoneName: "short"
        }).format(d);
    };



    const onApprove = event => {};
    const onDeny = event => {
        console.log('ondeny');
        event.preventDefault();
        axios.post($baseDir+'/api/delete/temp.php', {dir: guest.dir}, axiosConf)
            .then(resp => {
                console.dir(resp);
            })
            .catch(err => {
                console.dir(err);
            });
    };




    const guest = {};



    $('#ImportZip').on('change', e => {
        const file = e.target.files[0];
        if (!(/^.+\.zip$/i.test(file.name) && e.target.files[0].size < $max_size.r.file)) {
            return !1;
        }
        $('#PostZip').attr('disabled', false);
    });
    $('#PostZip').on('click', e => {
        e.preventDefault();
        $('#PostZip').attr('disabled', true);
        const formData = new FormData(document.DataImporter);


        axios.post(document.DataImporter.action, formData, axiosConf)
            .then(resp => {
                const data = resp.data;
                const _$Check = $('#jsCheck');
                _$Check.find('.content').eq(0).show();
                _$Check.find('.content').eq(1).hide();
                guest.dir = data.dir;

                if (data.hasOwnProperty('error')) {
                    _$Check.find('.header').text('【問題が発生しました】');
                    _$Check.find('.content').eq(1).find('p').html(data.error);
                    _$Check.find('.content').eq(0).hide();
                    _$Check.find('.content').eq(1).show();
                } else {
                    _$Check.find('.header').text('確認');
                    $('#jsGameTitle').text(data.gameTitle);
                    $('#jsBackupTurn').text(data.backupTurn);
                    $('#jsBackupDate').text(getDate(data.backupDate));
                    $('#jsZippedDate').text(getDate(data.zippedDate));
                    $('#jsRestoreTo').text(data.restoreTo);
                    $('#jsCheck').attr('disabled', false);
                }
                    _$Check.modal('show');
            })
            .catch(err => {
                _$Check.find('.header').text('【問題が発生しました】');
                _$Check.find('.content').eq(1).find('p').html('予期しないエラーが発生したため、動作に失敗しました。<br>数分時間をおき、ページを再読み込みのうえ、再度お試しください。<br>引き続き失敗する場合は、お手数ですが、サーバ管理者またはシステム開発者までお問い合わせください。');
                _$Check.find('.content').eq(0).hide();
                _$Check.find('.content').eq(1).show();
                _$Check.modal('show');
            });

    });
    $('#jsCheck').on('approve', onApprove);
    $('#jsCheck').on('deny', onDeny);
    $('.ui.dimmer.modals').on('click', onDeny);
});
