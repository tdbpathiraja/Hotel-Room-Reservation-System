// Disable right-click
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
}, false);

// Disable F12 key and Ctrl+Shift+I
document.onkeydown = function(e) {
    if(e.keyCode == 123) {
        return false;
    }
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
        return false;
    }
}