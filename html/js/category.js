function handleClickAction(pid) {
    // change values
    var form = document.getElementById("product_form"+"_"+pid);
    form.submit();
}

if (typeof(Storage) !== "undefined") {
    // Code for localStorage/sessionStorage.
    
} else {
    // Sorry! No Web Storage support..
    alert("Sorry, shopping cart not available this browser.")
}