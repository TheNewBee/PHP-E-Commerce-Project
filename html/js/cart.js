if (typeof(Storage) !== "undefined") {
    $(document).ready(function() {
        popCart();
        displayCartSum();
        displayPagination();
        if(!localStorage.getItem('items')){
            localStorage.setItem('items', 0);
            localStorage.setItem('price', 0);
        }
        // Event handling of page buttons (Add to Cart) of list of products
        $(document).on('click', ".ProductList button", function(e) {
            e.preventDefault();
            var pid = $(this).attr("value");
            var storing = localStorage.getItem(pid);
            if (storing) {
                localStorage.setItem(pid, parseInt(storing) + 1);
            } else {
                localStorage.setItem(pid, 1);
            }
            var newAmount = parseInt(localStorage.getItem('items')) + 1;
            var oriSumPrice = parseInt(localStorage.getItem('price'));
            var newQuant = localStorage.getItem(pid);
            var quant = '#' + pid + '_quant';
            var price = '#' + pid + "_price";

            localStorage.setItem('items', newAmount);
            ajaxGetAllbyPid(pid, 1).done(function(res) {
                localStorage.setItem('price', oriSumPrice + parseInt(res['price']));
                newPrice = localStorage.getItem('price');

                if ($(quant).length) {
                    if (!isNaN(newQuant)) {
                        $(quant).html(newQuant);
                        $(quant).fadeOut(250).fadeIn(250);
                        $(price).html('$' + newQuant * parseInt(res['price']));
                        $(price).fadeOut(250).fadeIn(250);
                        updateEvent(newAmount, newPrice);
                    }
                } else {
                    updateCart(res);
                    updateEvent(newAmount, newPrice);
                }
            });
        })
        // Event handling of cart buttons (+,-) of list of products
        $(document).on('click', '#cartItems button', function(e) {
            e.preventDefault();
            id = $(this).attr("id");
            var i = 0,
            pid = '';
            while ($(this).attr("id")[i] != '_') {
                pid += $(this).attr("id")[i];
                i++;
            }
            
            var oriQuant = parseInt(localStorage.getItem(pid));
            var oriSumAmount = parseInt(localStorage.getItem('items'));
            var oriSumPrice = parseInt(localStorage.getItem('price'));
            var id_plus = pid + "_plus"
            var id_minus = pid + "_minus";
            var quant = '#' + pid + '_quant';
            var price = '#' + pid + "_price";
            var plus = false, checkvalid = false;

            var storing = localStorage.getItem(pid);
            var stored = 0;
            if (id == id_plus) {
                plus = true;
                $(quant).html(++oriQuant);
                ++oriSumAmount;
                stored = parseInt(storing) + 1;
            } else if (id == id_minus && oriQuant - 1 >= 0) {
                checkvalid = true;
                $(quant).html(--oriQuant);
                --oriSumAmount;
                stored = parseInt(storing) - 1;
            }
            localStorage.setItem(pid, stored);
            localStorage.setItem('items', oriSumAmount);
            ajaxGetAllbyPid(pid, oriQuant).done(function(res) {
                newPrice = 0;
                if (plus) {
                    localStorage.setItem('price', oriSumPrice + parseInt(res['price']));
                    newPrice = localStorage.getItem('price');
                    if (!isNaN(oriQuant)) {
                        $(quant).html(oriQuant);
                        $(quant).fadeOut(250).fadeIn(250);
                        $(price).html('$' + oriQuant * parseInt(res['price']));
                        $(price).fadeOut(250).fadeIn(250);
                        updateEvent(oriSumAmount, newPrice);
                    }
                } else {
                    if (oriSumPrice - parseInt(res['price']) >= 0 && checkvalid) {
                        localStorage.setItem('price', oriSumPrice - parseInt(res['price']));
                        $(price).html('$' + oriQuant * parseInt(res['price']));
                        $(price).fadeOut(250).fadeIn(250);
                        updateEvent(oriSumAmount, newPrice);
                    }
                    newPrice = localStorage.getItem('price');
                    updateEvent(oriSumAmount, newPrice);
                }
            })
        })
    });
} else {
    // Sorry! No Web Storage support..
    alert("Sorry, shopping cart not available this browser.");
}

//Pagination
function displayPagination() {
    pageSize = 3;
    var pageCount = $(".product").length / pageSize;
    if(!document.getElementById('page_'+Math.ceil(pageCount))){
        for (var i = 0; i < pageCount; i++) {
                $("#pagin").append('<li><a id="page_'+ (i + 1) +'" href="#">' + (i + 1) + '</a></li> ');
        }
        $("#pagin li").first().find("a").addClass("current")
        showPage = function(page) {
            $(".product").hide();
            $(".product").each(function(n) {
                if (n >= pageSize * (page - 1) && n < pageSize * page) {
                    $(this).show();
                }
            });
        }
        showPage(1);
        $("#pagin li a").click(function() {
            $("#pagin li a").removeClass("current");
            $(this).addClass("current");
            showPage(parseInt($(this).text()))
        });
    }
}

function updateEvent(newAmount, newPrice) {
    $('#cart').fadeOut(250).fadeIn(250);

    $('#sumItems').html('<h5><u>' + newAmount + '</h5></u>');
    $('#sumItems').fadeOut(250).fadeIn(250);

    $('#sumPrices').html('<h5><u>$' + newPrice + '</h5></u>');
    $('#sumPrices').fadeOut(250).fadeIn(250);
}

// Get product info by Pid from Database
function ajaxGetAllbyPid(pid, quant) {
    return $.ajax({
        type: 'POST',
        url: '../ajax_submission.php',
        data: { 'pid': pid, 'quant': quant }
    });
}

function displayCartSum() {        
    if(!document.getElementById('sumItems')){
        var quant, price;
        if (localStorage.getItem('items') > 0 && localStorage.getItem('price') > 0 && !isNaN(localStorage.getItem('items')) && !isNaN(localStorage.getItem('price'))) {
            quant = localStorage.getItem('items');
            price = localStorage.getItem('price');
        } else {
            quant = 0;
            price = 0;
        }
        $('#cartItems ul').append('<li class="list-group-item"><div class="Column"><h5><u>Sum: </u></h5></div><div id="sumItems" class="Column"><h5><u>' + quant + '</h5></u></div><div class=" Column"></div><div class=" Column"></div></div><div id="sumPrices" class="Column"><h5><u>$' + price + '</h5></u></div></li>');
    }
}

function updateCart(data) {
    if(!document.getElementById(data['pid'] + '_quant')){
        $('#cartItems ul').append('<li class="list-group-item"><div class="Column">' + data['name'] + '</div>' + '<div id="' + data['pid'] + '_quant"class="Column">' + data['quant'] + '</div><div class=" Column"><button type="button" id="' + data['pid'] + '_plus" class="btn btn-outline-primary">+</button></div><div class=" Column"><button type="button" id="' + data['pid'] + '_minus" class="btn btn-outline-primary">-</button></div></div><div id="' + data['pid'] + '_price" class=" Column">$' + data['price'] + '</div></li>');
    }
}

function popCart() {
    var quants = [],
        pids = Object.keys(localStorage),
        i = 0;

    while (i < pids.length) {
        quantity = localStorage.getItem(pids[i]);
        if(quantity == '0'){
            localStorage.removeItem(pids[i]);
        }else{
            quants.push(quantity);
        }
        i++;
    }
    $.ajax({
        type: 'POST',
        url: '/php/shopping_cart.php',
        data: {
            'items': pids,
            'quants': quants
        }
    }).done(function(res) {
        var totalPirce = 0,
            totalItems = 0;
        
        for (var j = 0; j < pids.length; j++) {
            if (!isNaN(pids[j]) && !isNaN(quants[j]) && quants[j] != '0') {
               ajaxGetAllbyPid(pids[j], quants[j]).done(function(data) {
                    if(!document.getElementById(data['pid'] + '_quant')){
                        $('#cartItems ul').append('<li class="list-group-item"><div class="Column">' + data['name'] + '</div>' + '<div id="' + data['pid'] + '_quant"class="Column">' + data['quant'] + '</div><div class=" Column"><button type="button" id="' + data['pid'] + '_plus" class="btn btn-outline-primary">+</button></div><div class=" Column"><button type="button" id="' + data['pid'] + '_minus" class="btn btn-outline-primary">-</button></div></div><div id="' + data['pid'] + '_price" class=" Column">$' + parseInt(data['price']) * parseInt(data['quant']) + '</div></li>');
                    }
                    totalPirce += parseInt(data['price']) * parseInt(data['quant']);
                    totalItems += parseInt(data['quant']);
                    if(j == pids.length-1){
                        localStorage.setItem('price', totalPirce);
                        localStorage.setItem('items', totalItems);
                    }                        
            })
            }
        }
    });
    return;
};