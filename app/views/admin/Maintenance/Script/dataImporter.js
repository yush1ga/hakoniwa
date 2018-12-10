// import * as _ from 'lodash';
// import axios from 'axios';
$(document).ready(()=>{
    const axiosConf = {
        headers: {
            Accept: 'application/json, text/plain',
            "Content-Type": 'application/json;charset=utf-8'
        },
        timeout: 8000
    };
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
    const getReadableByte = b => {
        let si = 0;
        const units = ['', 'Ki', 'Mi', 'Gib'];
        for (; b > 1024; si++) {
            b /= 1024;
        }

        return b.toFixed(1) + units[si] + 'B';
    }



    const onApprove = event => {
        $('#jsCheck .ui.button').attr('disabled', true);
        axios.post($baseDir+'/api/upload/zip_restore/', guest, axiosConf)
            .then(r => {
                console.dir(r);
            })
            .catch(e => {
                console.dir(e);
            });
    };
    const onDeny = event => {
        $('#jsCheck .ui.button').attr('disabled', true);
        axios.post($baseDir+'/api/delete/', guest, axiosConf)
            .then()
            .catch(err => {
                console.dir(err);
            });
    };




    const guest = {};



    $('#ImportZip').on('change', e => {
        const file = e.target.files[0] || {};
        if (!('name' in file) || !(/^.+\.zip$/i.test(file.name))) {
            $('#PostZip').attr('disabled', true);

            return !1;
        }
        if (file.size >= $max_size.r.file) {
            alert(`サーバーのアップロード限界ファイルサイズ (${getReadableByte($max_size.r.file)}) を超えているため、ファイル“${file.name} (${getReadableByte(file.size)})”は利用できません。`);
            $('#PostZip').attr('disabled', true);

            return !1;
        }
        $('#PostZip').attr('disabled', false);
    });



    $('#PostZip').on('click', e => {
        e.preventDefault();
        $('#PostZip').attr('disabled', true);
        const formData = new FormData(document.DataImporter);
        const _$Check = $('#jsCheck');

        axios.post(document.DataImporter.action, formData, axiosConf)
            .then(resp => {
                const data = resp.data;
                _$Check.find('.content').eq(0).show();
                _$Check.find('.content').eq(1).hide();
                guest.dir = data.dir;
                guest.restoreTo = data.restoreTo;

                if (data.hasOwnProperty('error')) {
                    _$Check.children('.header').text('【問題が発生しました】');
                    _$Check.find('.content').eq(1).find('p').html(data.error);
                    _$Check.find('.content').eq(0).hide();
                    _$Check.find('.content').eq(1).show();
                } else {
                    _$Check.children('.header').text('確認');
                    $('#jsGameTitle').text(data.gameTitle);
                    $('#jsBackupTurn').text(data.backupTurn);
                    $('#jsBackupDate').text(getDate(data.backupDate));
                    $('#jsZippedDate').text(getDate(data.zippedDate));
                    $('#jsRestoreTo').text(data.restoreTo);
                    _$Check.find('button.ok').attr('disable', false);
                }
                _$Check.find('button.cancel').attr('disable', false);
            })
            .catch(err => {
                _$Check.children('.header').text('【問題が発生しました】');
                _$Check.find('.content').eq(1).find('p').html('何らかのエラーが発生したため、作業に失敗しました。<br>数分ほど時間をおき、ページを再読み込みしたうえ、再度お試しください。<br>引き続き失敗する場合は、お手数ですが、サーバ管理者またはシステム開発者までお問い合わせください。');
                _$Check.find('.content').eq(0).hide();
                _$Check.find('.content').eq(1).show();
            })
            .then(data => {
                _$Check.modal({
                    closable: false,
                    onApprove: onApprove,
                    onDeny: onDeny
                })
                .modal('show');
            });

    });
});
