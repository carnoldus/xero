//  Function makes a get request to back up the associated data set, and
//  receives a backup status object once complete
var onBtnClick = function (label, request) {

    var idRoot = label.toLowerCase();

    //  Set the spinner a-spinnin'
    $('#' + idRoot + 'BkpBtn').prop("disabled", true);
    $('#' + idRoot + 'BkpBtn').html('Backup ' + label + ' <span class="glyphicon glyphicon-refresh spinning"></span>');

    //  Get request for data
    $.get('?request=' + request).done(function (result) {

        $('#' + idRoot + 'BkpBtn').prop("disabled", false);
        $('#' + idRoot + 'BkpBtn').html('Backup ' + label);

        var backupContainer = $('#' + idRoot + 'BkpContainer');
        var infoBox = $('#infoBox');

        if(result.success == true){

            $('#' + idRoot + 'BkpBadge').html(result.updated);
            $('#' + idRoot + 'BkpBtnContainer').removeClass('hidden');

            backupContainer.removeClass('alert-info');
            backupContainer.removeClass('alert-danger');
            backupContainer.addClass('alert-success');

            infoBox.removeClass('alert-danger');
            infoBox.addClass('alert-success');
            infoBox.html('<strong>Backup Successful</strong> for ' + label);
            infoBox.css('visibility', 'visible');

        }else{

            backupContainer.removeClass('alert-info');
            backupContainer.removeClass('alert-success');
            backupContainer.addClass('alert-danger');

            infoBox.removeClass('alert-success');
            infoBox.addClass('alert-danger');
            infoBox.html('<strong>Backup Failure</strong> with notice: <em>' + result.notice + '</em>');
            infoBox.css('visibility', 'visible');

        }
    });

};

//  Attach functionality to buttons
$('#accountsBkpBtn').on('click', function () {
    onBtnClick('Accounts', 'AccountsAll');
});

$('#vendorsBkpBtn').on('click', function () {
    onBtnClick('Vendors', 'ContactsOnlyVendors');
});
