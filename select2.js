// resources/js/select2.js
import 'select2/dist/css/select2.min.css';
import 'select2';

$(document).ready(function() {
    $('#nationality').select2({
        placeholder: "Select Nationality",
        allowClear: true
    });
});
