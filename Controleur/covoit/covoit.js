$(document).ready(function() {
    var $select = $(".select-numbers"); // Renomme la classe correctement
    for (let i = 1; i <= 100; i++) {
        $select.append($('<option></option>').val(i).text(i));
    }
});