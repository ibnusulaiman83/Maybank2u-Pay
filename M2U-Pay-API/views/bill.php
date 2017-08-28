<?php

if ($bill_expired){
    exit('Bills are expired');
}
?>
<form method="POST" action="<?php echo $data['actionUrl']; ?>" id="form-payment" target="_blank" >
    <input type="hidden" name="q" value="<?php echo $data['encryptedString']; ?>" id="q">
    <input type="hidden" name="i" value="OT" id="i">
    <input type="submit" value="Click Here to Pay">
</form>
<br>
<p>ID: <span id="urlid"><?php echo $bill['URLId']; ?></span></p>
<p>Validation: <span id="validation-hash"><?php echo $validation_hash; ?></span></p>
<p>Status: <span id="status-string">None</span></p>
<span id="system-url" style="display: none;"><?php echo SYSTEM_URL; ?></span>
<span id="redirect-url" style="display: none;"><?php echo $bill['RedirectUrl']; ?></span>
<p><button type="button" id="cancel-button">Click Here to Cancel</button></p>
<p>Completed Payment?</p> <button type="button" id="recheck">Click here to continue</button>

<form method="POST" action="<?php echo $bill['RedirectUrl']; ?>" id="form-paid">
    <input type="hidden" id="order_info" name="order_info" value="">
    <input type="hidden" id="payment_status" name="payment_status" value="">
    <input type="hidden" id="payment_amount" name="payment_amount" value="">
    <input type="hidden" id="maybank_refid" name="maybank_refid" value="">
    <input type="hidden" id="maybank_trndatetime" name="maybank_trndatetime" value="">
    <input type="hidden" id="hash_validation" name="hash_validation" value="">
</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="<?php echo SYSTEM_URL . 'views/bill_script.js'; ?>"></script>