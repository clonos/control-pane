function check_IP_MASK(item1, item2) {
//var ip_mask=/(([12][0-5][0-9]*\.)*[0-9.]*\/[0-9]{2})/;
//var ip_mask=/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;

//var ip_mask=/^(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}$/;
var ip_mask=/^(25[0-5]|2[0-4]\d|[01]?\d{1,2})(?:\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3}$/;

var item_value = document.getElementById(item1).value;
var item_value_UP = item_value.toUpperCase();
var item_correct = item2;

    if (item_value_UP == "DHCP") {
        document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-ok\"></span>';
        document.getElementById(item_correct).className = 'label label-success';
        document.getElementById(item1).value = item_value_UP;
    } else {
        if (item_value.search(ip_mask) == 0) {
////    if (item_value.match(ip_mask) == 0) {
            if (xajax_check_ip(item_value_UP)) {
                document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-ok\"></span>';
                document.getElementById(item_correct).className = 'label label-success';
                document.getElementById(item1).value = item_value_UP;
            } else {
                document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-remove\"></span>';
                document.getElementById(item_correct).className = 'label label-danger';
            }
        } else {
            document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-remove\"></span>';
            document.getElementById(item_correct).className = 'label label-danger';
        }
    }
}

function CountPass(item1, item2) {
var item_correct = item2;

    if (document.getElementById(item1).value.length >= 8) {
        document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-ok\"></span>';
        document.getElementById(item_correct).className = 'label label-success';
    } else if (document.getElementById(item1).value.length < 8) {
        document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-remove\"></span>';
        document.getElementById(item_correct).className = 'label label-danger';
    }
}

function CorrectPass(item1, item2, item3) {
var item_pass_value = document.getElementById(item2).value;
var item_pass_length = document.getElementById(item2).value.length
var item_correct = item3;

    if (item_pass_length >= 8) {
        if (document.getElementById(item1).value == item_pass_value) {
            document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-ok\"></span>';
            document.getElementById(item_correct).className = 'label label-success';
        } else {
            document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-remove\"></span>';
            document.getElementById(item_correct).className = 'label label-danger';
        }
    } else {
        document.getElementById(item_correct).innerHTML = '<span class=\"glyphicon glyphicon-remove\"></span>';
        document.getElementById(item_correct).className = 'label label-danger';
    }
}

String.prototype.trimRight=function()
// убирает все пробелы в конце строки
{
  var r=/\s+$/g;
  return this.replace(r,'');
}

String.prototype.trimLeft=function()
// убирает все пробелы в начале строки
{
  var r=/^\s+/g;
  return this.replace(r,'');
}

String.prototype.trim=function()
// убирает все пробелы в начале и в конце строки
{
  return this.trimRight().trimLeft();
}

String.prototype.trimMiddle=function()
// убирает все пробелы в начале и в конце строки
// помимо этого заменяет несколько подряд
// идущих пробелов внутри строки на один пробел
{
  var r=/\s\s+/g;
  return this.trim().replace(r,' ');
}

String.prototype.trimAll=function()
// убирает все пробелы в строке s
{
  var r=/\s+/g;
  return this.replace(r,'');
}
