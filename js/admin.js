
function validateInput(name, message) {
    var input = document.getElementById(name);

    if(input.value == '') {
        alert(message);
        input.focus();
        return false;
    }

    return true;
}