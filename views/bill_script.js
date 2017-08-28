
document.getElementById('form-payment').submit();

//setInterval(sendToServer, '10');

$(document).on("click", "#recheck", function (evt)
{
    $.ajax({
        type: 'POST',
        url: $('#system-url').text() + 'callback',
        datatype: "jsonp",
        timeout: 10000,
        data: {
            'do': 'check-status',
            'urlid': $('#urlid').text(),
            'validation': $('#validation-hash').text()
        },
        success: function (result) {
            if (result.payment_status === 'true') {
                $('#order_info').attr('value', result.order_info);
                $('#order_info').val(result.order_info);
                $('#payment_status').attr('value', result.payment_status);
                $('#payment_status').val(result.payment_status);
                $('#payment_amount').attr('value', result.payment_amount);
                $('#payment_amount').val(result.payment_amount);
                $('#maybank_refid').attr('value', result.maybank_refid);
                $('#maybank_refid').val(result.maybank_refid);
                $('#maybank_trndatetime').attr('value', result.maybank_trndatetime);
                $('#maybank_trndatetime').val(result.maybank_trndatetime);
                $('#hash_validation').attr('value', result.hash_validation);
                $('#hash_validation').val(result.hash_validation);
                document.getElementById('form-paid').submit();
            } else {
                $('#status-string').text('Not Paid');
                return false;
            }

        },

        error: function (xhr, status, error) {
            $('#status-string').text('Connection Error');
        }
    });
});

$(document).on("click", "#cancel-button", function (evt)
{
    $.ajax({
        type: 'POST',
        url: $('#system-url').text() + 'callback',
        datatype: "jsonp",
        timeout: 10000,
        data: {
            'do': 'check-status',
            'urlid': $('#urlid').text(),
            'validation': $('#validation-hash').text()
        },
        success: function (result) {
            if (result.payment_status === 'false') {
                $('#order_info').attr('value', result.order_info);
                $('#order_info').val(result.order_info);
                $('#payment_status').attr('value', result.payment_status);
                $('#payment_status').val(result.payment_status);
                $('#hash_validation').attr('value', result.hash_validation);
                $('#hash_validation').val(result.hash_validation);
                document.getElementById('form-paid').submit();
            } else {
                $('#status-string').text('Payment Has Been Made. Cannot Cancel');
                return false;
            }

        },

        error: function (xhr, status, error) {
            $('#status-string').text('Connection Error');
        }
    });
});