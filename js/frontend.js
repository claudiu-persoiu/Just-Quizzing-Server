function redirect(location) {
    window.location = location;
}

var Menu = (function () {

    var menuElement;
    var menuIsOpen = false;

    var getMenuElements = function () {
        if (!menuElement) {
            menuElement = document.getElementById('side-menu');
        }
    }

    var toggleMenu = function () {
        if(menuIsOpen) {
            menuElement.style.display = 'none';
        } else {
            menuElement.style.display = 'block';
        }
        menuIsOpen = !menuIsOpen;
    }

    return {
        toggle: function () {
            getMenuElements();
            toggleMenu();
        }
    }
})();