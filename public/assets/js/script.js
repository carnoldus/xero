//  Function makes a get request to back up the associated data set, and
//  receives a backup status object once complete
var onBtnClick = function (label, request) {

    var idRoot = label.toLowerCase();

    $('#' + idRoot + 'BkpBtn').prop("disabled", true);
    $('#' + idRoot + 'BkpBtn').html('Backup ' + label + ' <span class="glyphicon glyphicon-refresh spinning"></span>');

    $.get('?request=' + request).done(function (data) {
        if(data.success == true){
            $('#' + idRoot + 'BkpBtn').prop("disabled", false);
            $('#' + idRoot + 'BkpBtn').html('Backup ' + label);
            $('#' + idRoot + 'BkpBadge').html(data.updated);
            $('#' + idRoot + 'BkpContainer').removeClass('hidden');
        }
    });

};

$('#accountsBkpBtn').on('click', function () {
    onBtnClick('Accounts', 'AccountsAll');
});

$('#vendorsBkpBtn').on('click', function () {
    onBtnClick('Vendors', 'ContactsOnlyVendors')
});