
var bulle_visible=false; // La variable i nous dit si la bulle est visible ou non
var rack=null; // La variable rack est le rack sélectionné
var clickedCell_prev = null
var clickedCell_server_prev = null
var text= ""; // La variable i nous dit si la bulle est visible ou non
var count = 0;

$(document).ready(function() {
                
    $("#page-rack-present").addClass("hide-block");
    $("#page-rack-create").addClass("hide-block");
    $("#page-server-create").addClass("hide-block");
    $("#page-rack-info").addClass("hide-block");
    $("#page-server-info").addClass("hide-block");
    
    $("#rack").addClass("hide-block");                    
    $("#room:has(td)").mouseover(function(e) {
        $(this).css("cursor", "pointer");
    });
    $("#room:has(td)").click(function(e) {
        var clickedCell = $(e.target).closest("td");
        $("#page-server-info").addClass("hide-block");
        $("#page-server-create").addClass("hide-block");
        $("#page-rack-info").addClass('hide-block');

        if (clickedCell.hasClass("rack-empty_highlight")) {
            clickedCell.removeClass("rack-empty_highlight");
            clickedCell.addClass("rack-empty");
            $("#page-rack-create").addClass("hide-block");
        }
        else if (clickedCell.hasClass("rack-present_highlight")) {
            clickedCell.removeClass("rack-present_highlight");
            clickedCell.addClass("rack-present");
            $("#page-rack-present").addClass("hide-block");
        }
        else {
            if (clickedCell_prev != null) {

                if (clickedCell_prev.hasClass("rack-present_highlight")) {
                    clickedCell_prev.removeClass("rack-present_highlight");
                    clickedCell_prev.addClass("rack-present");
                }
                if (clickedCell_prev.hasClass("rack-empty_highlight")) {
                    clickedCell_prev.removeClass("rack-empty_highlight");
                    clickedCell_prev.addClass("rack-empty");
                }
            }
            $("#rack").addClass("hide-block");;
            if (clickedCell.hasClass("rack-empty")) {
                clickedCell.removeClass("rack-empty");
                clickedCell.addClass("rack-empty_highlight");
                $("#rack-create-row_id").val(clickedCell.parent().index());
                $("#rack-create-num_id").val(clickedCell.index()); 
                $("#page-rack-present").addClass("hide-block");
                $("#page-rack-create").removeClass("hide-block");
            }
            else {
                clickedCell.removeClass("rack-present");
                clickedCell.addClass("rack-present_highlight");
                $("#rack").removeClass("hide-block");
                $("#page-rack-present").removeClass("hide-block");
                $("#page-rack-create").addClass("hide-block");
                $("#page-rack-info").removeClass("hide-block");
                $("#page-server-info").addClass("hide-block");
    
                get_server_info(1,clickedCell.parent().index(),clickedCell.index());
            }
        }
        clickedCell_prev = clickedCell;
     });

//JQuery functions

function add_server(room_id, row_id, num_id, slot_id) {
    $('input#submit-server-info').off('click');
    $('input#submit-server-info').click( function() {

        name            = $('input#server-name').val();
        descritpion     = $('input#server-description').val();
        height          = $('input#server-height').val();
        brand           = "";
        model           = "";
        serial_num      = "";

        $.post("add_server_info.php",
        {
                    room_id:        room_id,
                    row_id:         row_id,
                    num_id:         num_id,
                    slot_id:        slot_id,
                    name:           name,
                    descritpion:    descritpion,
                    height:         height,
                    brand:          brand,
                    model:          model,
                    serial_num:     serial_num,
        },
        function(data, status){
            var add_server_status = jQuery.parseJSON(data);
            if (add_server_status) {
                get_server_info(room_id, row_id, num_id);
            }
        }
        );
    });
}

function get_server_info(room_id, row_id, num_id) {

                $("#table-rack-present").children().remove();
        
                var row_to_slot = [];
                var row_to_key = [];
                var rack_info = null;        
                
                $.post("get_rack_info.php",
                {
                    room_id: room_id,
                    row_id: row_id,
                    num_id: num_id
                },
                function(data, status){
                    rack_info = jQuery.parseJSON(data);
                    var count = 0;
                    for (key in rack_info) {
                       for(var i = rack_info[key]['height']; i > 0; i--) {
                                
                                row_to_slot[count] = i;
                                row_to_key[count] = key;
                                
                                count++;       
                                if (i in rack_info[key]['servers_info']) {
                                   $("#table-rack-present").append($('<tr height="15" ><td width="150" class="server-present" onmouseover="montre(\'' + server_info_to_string(rack_info[key]['servers_info'][i]) + '\') ;" onmouseout="cache() ;"> S' + i + ': ' + rack_info[key]['servers_info'][i]['name'] + '</td></tr>'));
                                }
                                else {
                                   $("#table-rack-present").append($('<tr height="15" ><td width="150" class="server-empty" > S' + i + ': </td></tr>')); 
                                }
                        }
                        display_rack_info(rack_info[key],$("#page-rack-info"));  
                    }
                    $("#page-rack-info").removeClass('hide-block');
                    $("#table-rack-present:has(td)").mouseover(function(e) {
                        $(this).css("cursor", "pointer");
                    });
                    $("#table-rack-present:has(td)").off('click');
                    $("#table-rack-present:has(td)").click(function(e) {
                        var clickedCell_server = $(e.target).closest("td");
                        count = count + 1;
                        if (clickedCell_server.hasClass("server-empty_highlight")) {
                            clickedCell_server.removeClass("server-empty_highlight");
                            clickedCell_server.addClass("server-empty");
                            $("#page-server-create").addClass("hide-block");
                            $("#page-server-info").addClass("hide-block");
                        }
                        else if (clickedCell_server.hasClass("server-present_highlight")) {
                            clickedCell_server.removeClass("server-present_highlight");
                            clickedCell_server.addClass("server-present");
                            $("#page-server-present").addClass("hide-block");
                            $("#page-server-info").addClass("hide-block");
                        }
                        else { 
                            if (clickedCell_server_prev != null) {
                                if (clickedCell_server_prev.hasClass("server-present_highlight")) {
                                    clickedCell_server_prev.removeClass("server-present_highlight");
                                    clickedCell_server_prev.addClass("server-present");
                                }
                                if (clickedCell_server_prev.hasClass("server-empty_highlight")) {
                                    clickedCell_server_prev.removeClass("server-empty_highlight");
                                    clickedCell_server_prev.addClass("server-empty");
                                }
                            }
                            if (clickedCell_server.hasClass("server-empty")) {
                                clickedCell_server.removeClass("server-empty");
                                clickedCell_server.addClass("server-empty_highlight");
                                $("#page-server-present").addClass("hide-block");
                                $("#page-server-info").addClass("hide-block");
                                $("#page-server-create").removeClass("hide-block");
                                add_server(room_id, row_id, num_id, row_to_slot[clickedCell_server.parent().index()]);
                            }
                            else {
                                clickedCell_server.removeClass("server-present");
                                clickedCell_server.addClass("server-present_highlight");
                                $("#page-server-present").removeClass("hide-block");
                                $("#page-server-create").addClass("hide-block");
                                var server_info = rack_info[row_to_key[clickedCell_server.parent().index()]]['servers_info'][row_to_slot[clickedCell_server.parent().index()]];
                                display_server_info(server_info,$("#page-server-info"));
                                $("#page-server-info").removeClass("hide-block");
                            }
                        }
                    clickedCell_server_prev = clickedCell_server;
                    });
                    
                });
}

function display_server_info(server_info,page_server_info) {
    
    var server_info_key = ['name', 'description', 'height'];
    var str = `<h2> Server information </h2>
    <form>
        <table>`;
        for (i = 0; i < server_info_key.length; i++) {
            var key = server_info_key[i];
            str = str.concat(`<tr>
            <td width=100>
            `+key.charAt(0).toUpperCase()+key.slice(1)+`:
            </td>
            <td width=100>
            <input id="update-server-field-`+key+`" type="text" name="`+key+`" value="`+server_info[key]+`"> 
            </td>
            <td width=100>
            <input id="update-server-`+key+`" type="button"  class="btn btn-xs" name="action" value="Update">
            </tr>`);
        }
        str = str.concat('</table></form>');
        page_server_info.html(str);

        for (i = 0; i < server_info_key.length; i++) {
            let key = server_info_key[i];
            $('input#update-server-'+key).addClass('hide-block');
            $('input#update-server-'+key).off('click');
            $('input#update-server-'+key).click( function() {
                $.post("update_server_info.php",
                    {
                        id:        server_info['id'],
                        field:     key,
                        val:       $('input#update-server-field-'+key).val()
                    },
                    function(data, status){
                        var update_server_status = jQuery.parseJSON(data);
                        if (update_server_status) {
                            server_info[key] = $('input#update-server-field-'+key).val();
							$('input#update-server-'+key).addClass('hide-block');
                        }
                    }
                );
            });
            $('input#update-server-field-'+key).change( function () {
				if ($('input#update-server-field-'+key).val() != server_info[key]) {
					$('input#update-server-'+key).removeClass('hide-block');
				}
				else {
					$('input#update-server-'+key).addClass('hide-block');
				} 
			});
        }
}

function display_rack_info(rack_info,page_rack_info) {
    var rack_info_key = ['name', 'description', 'height'];
    var str = `<h2> Rack information </h2>
    <form>
        <table>`;
        for (i = 0; i < rack_info_key.length; i++) {
            var key = rack_info_key[i];
            str = str.concat(`<tr>
            <td width=100>
            `+key.charAt(0).toUpperCase()+key.slice(1)+`:
            </td>
            <td width=100>
            <input id="update-rack-field-`+key+`" type="text" name="`+key+`" value="`+rack_info[key]+`"> 
            </td>
            <td width=100>
            <input id="update-rack-`+key+`" type="button"  class="btn btn-xs" name="action" value="Update">
            </tr>`);
        }
        str = str.concat('</table></form>');
        page_rack_info.html(str);

        for (i = 0; i < rack_info_key.length; i++) {
            let key = rack_info_key[i];
            $('input#update-rack-'+key).addClass('hide-block');
			$('input#update-rack-'+key).off('click');
            $('input#update-rack-'+key).click( function() {
                $.post("update_rack_info.php",
                    {
                        id:        rack_info['id'],
                        field:     key,
                        val:       $('input#update-rack-field-'+key).val()
                    },
                    function(data, status){
                        var update_rack_status = jQuery.parseJSON(data);
                        if (update_rack_status) {
                            rack_info[key] = $('input#update-rack-field-'+key).val();
							$('input#update-rack-'+key).addClass('hide-block');
						}
                    }
                );
            });
            $('input#update-rack-field-'+key).change( function () {
				if ($('input#update-rack-field-'+key).val() != rack_info[key]) {
					$('input#update-rack-'+key).removeClass('hide-block');
				}
				else {
					$('input#update-rack-'+key).addClass('hide-block');
				} 
			});
        }
}

});

function getrackinfo(str) {
    if (str.length == 0) { 
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "gethint.php?q=" + str, true);
        xmlhttp.send();
    }
}

function GetId(id)
{
return document.getElementById(id);
}
 
function move(e) {
  if(bulle_visible) {  // Si la bulle est visible, on calcul en temps reel sa position ideale
    if (navigator.appName!="Microsoft Internet Explorer") { // Si on est pas sous IE
        GetId("curseur").style.left=e.pageX + 5+"px";
        GetId("curseur").style.top=e.pageY + 10+"px";
    }
    else { // Modif proposé par TeDeum, merci à  lui
    if(document.documentElement.clientWidth>0) {
        GetId("curseur").style.left=20+event.x+document.documentElement.scrollLeft+"px";
        GetId("curseur").style.top=10+event.y+document.documentElement.scrollTop+"px";
    } else {
        GetId("curseur").style.left=20+event.x+document.body.scrollLeft+"px";
        GetId("curseur").style.top=10+event.y+document.body.scrollTop+"px";
         }
    }
  }
}
 
function montre(text) {
  if(bulle_visible==false) {
  GetId("curseur").style.visibility="visible"; // Si il est cacher (la verif n'est qu'une securité) on le rend visible.
  GetId("curseur").innerHTML = text; // on copie notre texte dans l'élément html
  bulle_visible=true;
  }
}

function cache() {
if(bulle_visible==true) {
GetId("curseur").style.visibility="hidden"; // Si la bulle est visible on la cache
bulle_visible=false;
}
}
document.onmousemove=move; // dès que la souris bouge, on appelle la fonction move pour mettre à jour la position de la bulle.

function server_info_to_string(server_info) {
    str = 'Name: ' + server_info['name'] + "<br>";
    str =  str.concat('Description: ' + server_info['description'] + "<br>");
    return str;
}

function rack_info_to_string(rack_info) {
    str = 'Name: ' + server_info['name'] + " ";
    str =  str.concat('Description: ' + server_info['description']);
    return str;
}
