function AppIndexCtrl($scope) {
    if (checkSession() == true){
        animateLogin();
        location = "#/";
    } else {
        animateLogout();
    }

    jQuery("#login").show();
    var currentBtn = true;
    jQuery("#sltSign").css({ backgroundColor: "#660000", color: "#ffffff", cursor: "default"});
    jQuery("#sltReg").css({ border: "1px solid #660000" });
    // true = sign || false = register
    jQuery(document).ready(function ($) {
        jQuery("#sltSign").on("mouseenter", function () {
            if (currentBtn == false) {
                var style = { backgroundColor: "#e50000", color: "#ffffff", cursor: "pointer" };
                jQuery(this).css(style);
            }
        }).on("mouseleave", function() {
                if (currentBtn == false) {
                    var style = { backgroundColor: "#ffffff", color: "#000000", cursor: "default" };
                    jQuery(this).css(style);
                }
            });

        jQuery("#sltReg").on("mouseenter", function () {
            if (currentBtn == true) {
                var style = { backgroundColor: "#e50000", color: "#ffffff", cursor: "pointer" };
                jQuery(this).css(style);
            }
        }).on("mouseleave", function () {
                if (currentBtn == true) {
                    var style = { backgroundColor: "#ffffff", color: "#000000", cursor: "default" };
                    jQuery(this).css(style);
                }
            });

        jQuery("#sltReg").click(function () {
            currentBtn = false;
            jQuery("#sltReg").css({ backgroundColor: "#660000", color: "#ffffff", border: "none", cursor: "default" });
            jQuery("#sltSign").css({ backgroundColor: "#ffffff", color: "#000000", border: "1px solid #660000", cursor: "default" });
            jQuery("#register").show();
            jQuery("#login").hide();
        });

        jQuery("#sltSign").click(function () {
            currentBtn = true;
            jQuery("#sltSign").css({ backgroundColor: "#660000", color: "#ffffff", border: "none", cursor: "default" });
            jQuery("#sltReg").css({ backgroundColor: "#ffffff", color: "#000000", border: "1px solid #660000", cursor: "default" });
            jQuery("#login").show();
            jQuery("#register").hide();
        });

        jQuery("#cy").on("change", function() {
            if(this.checked) {
                jQuery("#lft").show('fast');
            } else {
                jQuery("#lft").hide('fast');
            }
        });

        jQuery("#frmLogin").submit(function (e) {
            jQuery.ajax({
                type: 'POST',
                url: './php/login.php',
                data: jQuery(this).serialize(),
                success: function (data) {
                    if (data == "100") {
                        jQuery("#msg").empty();
                        jQuery("#msg").append("<p>" + "Logged in Successful!" + "</p>");
                        jQuery("#msg").show().delay(1000).fadeOut();
                        animateLogin();
                        location = "#/";
                    } else {
                        jQuery("#msg").empty();
                        jQuery("#msg").append("<p>" + data + "</p>");
                        jQuery("#msg").show().delay(2500).fadeOut();
                    }
                }
            });
            e.preventDefault();
        });

        jQuery("#frmReg").submit(function (e) {
            jQuery.ajax({
                type: 'POST',
                url: './php/register.php',
                async: false,
                data: jQuery(this).serialize(),
                success: function (data) {
                    if (data == "100") {
                        var adminList = "";
                        jQuery.ajax({
                            type: 'POST',
                            async: false,
                            url: './php/retrieve/getAdmins.php',
                            success: function (data) {
                                if(data == "{ Currently No Admins! }") {
                                    adminList = "{ Currently No Admins! }";
                                } else {
                                    var getData = JSON.parse(data);
                                    for (var i = 0; i < getData['users'].length; i++) {
                                        if (i == getData['users'].length - 1) {
                                            adminList = adminList + getData['users'][i]['username'];
                                        } else {
                                            adminList = adminList + getData['users'][i]['username'] + ", ";
                                        }
                                    }
                                }
                            }
                        });
                        currentBtn = true;
                        jQuery("#sltSign").css({ backgroundColor: "#660000", color: "#ffffff", border: "none", cursor: "default" });
                        jQuery("#sltReg").css({ backgroundColor: "#ffffff", color: "#000000", border: "1px solid #660000", cursor: "default" });
                        jQuery("#login").show();
                        jQuery("#register").hide();
                        jQuery('#blanket').show();
                        jQuery('#msgMid').append('<p style="text-align: center">Registered Successful!</p> <br />');
                        jQuery('#msgMid').append('<p style="text-align: center">Contact one of the following users on league of legends to verify your account!</p> <br/>');
                        jQuery('#msgMid').append('<p style="text-align: center"> ' + adminList + ' </p>');
                        jQuery('#msgMid').show();
                    } else {
                        jQuery("#msg").empty();
                        jQuery("#msg").append("<p>" + data + "</p>");
                        jQuery("#msg").show().delay(2500).fadeOut();
                    }

                }
            });
            e.preventDefault();
        });

        $("#blanket").click(function () {
            $('#blanket').hide();
            $('#msgMid').hide();

            $('#msgMid').empty();
        });

    });
}

function animateLogin() {
    jQuery(".nlog").hide();
    jQuery(".log").show();

    jQuery.ajax({
        type: 'POST',
        async: false,
        url: './php/retrieve/getCred.php',
        success: function (data) {
            if (data != "null") {
                jQuery("#cred").empty();
                jQuery("#cred").show();
                var cred = JSON.parse(data);
                jQuery("#cred").append("Welcome " + cred['credentials']['name'] + " " + cred['credentials']['surname']);
            }
        }
    });
    isAdmin();
}

function animateLogout(){
    jQuery(".nlog").show();
    jQuery(".log").hide();
    jQuery("#cred").hide();
    jQuery("#cred").empty();
    isAdmin();
}

function signOut() {
    jQuery.ajax({
        type: 'POST',
        url: './php/signout.php',
        success: function () {
            animateLogout();
            location = "#/sign-in";
        }
    });
}

function checkSession() {
    var session = new Boolean(false);
    jQuery.ajax({
        type: 'POST',
        async: false,
        url: './php/validation/checkSession.php',
        success: function (data) {
            if(data == "100"){
                session = true;
            }
        }
    });
    return session;
}



function isAdmin() {
    var isAdmin = checkAdmin();
    if (isAdmin == true) {
        jQuery(".va").show();
    } else {
        jQuery(".va").hide();
    }
}

function AppCtrl($scope) {
    if (checkSession() == false){
        animateLogout()
    } else {
        animateLogin();
    }

    var list;

    // Get ranking board
    jQuery.ajax({
        type: 'POST',
        async: true,
        url: './php/retrieve/getPlayers.php',
        beforeSend: function() {
            jQuery('#appMenu').append(jQuery('<div id="loader"></div>'));
        },
        success: function (data) {
            jQuery('#appMenu').empty();
            if(data != "null") {
                var counter = 1;
                var html = '<table id="board"> <tr> <th># Rank</th> <th>Name & Surname</th> <th>Summoner Name</th> <th>Looking for Team</th> <th>Tier</th> <th>Rank</th> <th>League Points</th> </tr>';
                list = JSON.parse(data);

                //var colorArray = ['red', 'yellow', 'green', 'cyan', 'blue', 'purple', 'white', 'orange'];
                var colorArray = ['white', 'yellow', 'red', 'orange'];
                var colorCounter = 0;

                for (var i = 0; i < list['users'].length; i++) {
                    var id = i;
                    var Name = list['users'][i]['name'] + " " + list['users'][i]['surname'];
                    var sumName = list['users'][i]['summonerName'];

                    var tier = list['users'][i]['tier'];
                    if(tier == "Challenger"){
                        tier = '<img src="./images/challenger_tier.png" class="tier" title="Challenger Tier" alt="Challenger Tier" width="80" height="60">';
                    }else if (tier == "Diamond") {
                        tier = '<img src="./images/diamond_tier.png" class="tier" title="Diamond Tier" alt="Diamond Tier" width="80" height="60">';
                    } else if (tier == "Platinum") {
                        tier = '<img src="./images/platinum_tier.png" class="tier" title="Platinum Tier" alt="Platinum Tier" width="70" height="50">';
                    } else if (tier == "Gold") {
                        tier = '<img src="./images/gold_tier.png" class="tier" title="Gold Tier" alt="Gold Tier" width="80" height="60">';
                    } else if (tier == "Silver"){
                        tier = '<img src="./images/silver_tier.png" class="tier" title="Silver Tier" alt="Silver Tier" width="80" height="60">';
                    } else if (tier == "Bronze"){
                        tier = '<img src="./images/bronze_tier.png" class="tier" title="Bronze Tier" alt="Bronze Tier" width="80" height="60">';
                    } else {
                        // Wood
                        tier = '<img src="./images/wood_tier.png" class="tier" title="Unranked" alt="Unranked"  width="50" height="50">';
                    }

                    var rank = list['users'][i]['rank'];
                    if (rank == 1){
                        rank = '<div title="1">I</div>';
                    } else if(rank == 2){
                        rank = '<div title="2">II</div>';
                    } else if (rank == 3){
                        rank = '<div title="3">III</div>';
                    } else if (rank == 4){
                        rank = '<div title="4">IV</div>';
                    } else {
                        rank = '<div title="5">V</div>';
                    }

                    var lp = list['users'][i]['lp'];

                    var top = list['users'][i]['top'];
                    var middle = list['users'][i]['middle'];
                    var marksman = list['users'][i]['marksman'];
                    var jungle = list['users'][i]['jungle'];
                    var support = list['users'][i]['support'];

                    var lft = false;
                    if (top == "1" || middle == "1" || marksman == "1" || jungle == "1" || support == "1") lft = true;

                    var titleArray = [];
                    if (top == "1") {
                        titleArray.push('Top');
                    }
                    if (middle == "1"){
                        titleArray.push('Middle');
                    }
                    if (marksman == "1"){
                        titleArray.push('Marksman');
                    }
                    if(jungle == "1") {
                        titleArray.push('Jungle');
                    }
                    if(support == "1"){
                        titleArray.push('Support');
                    }
                    var titleString = "";
                    for (var m = 0; m < titleArray.length; m++){
                        if (m == titleArray.length-1){
                            titleString = titleString + titleArray[m];
                        } else {
                            titleString = titleString + titleArray[m] + ", ";
                        }
                    }

                    var userClass = '';
                    var lftSymbol = '';
                    if (lft == true) {
                        userClass = 'class="has ' + colorArray[colorCounter] + '"' ;
                        lftSymbol = '<div title="' + titleString + '">&#x2713</div>';
                    } else {
                        userClass = 'class="' + colorArray[colorCounter] + '"' ;
                    }

                    colorCounter ++;
                    if(colorCounter == colorArray.length) {
                        colorCounter = 0;
                    }

                    html = html + '<tr id="' + id + '" ' + userClass + ' title="' + titleString + '" >' +
                        '<td> ' + counter + ' </td>' +
                        '<td> ' + Name + ' </td>' +
                        '<td> ' + sumName + ' </td>' +
                        '<td> ' + lftSymbol + ' </td>' +
                        '<td> ' + tier + ' </td>' +
                        '<td> ' + rank + ' </td>' +
                        '<td> ' + lp + ' </td>' +
                        '</tr>';

                    counter++;
                }
                html = html + '</table>';
                jQuery('#appMenu').append(html);
            }
        }
    });

    jQuery(document).on({
        mouseenter: function () {
            if(jQuery(this).hasClass("has")){
                jQuery("#hover").empty();
                var id = jQuery(this).attr('id');

                var top = list['users'][id]['top'];
                var middle = list['users'][id]['middle'];
                var marksman = list['users'][id]['marksman'];
                var jungle = list['users'][id]['jungle'];
                var support = list['users'][id]['support'];

                var titleArray = [];
                if (top == "1") {
                    titleArray.push('Top');
                }
                if (middle == "1"){
                    titleArray.push('Middle');
                }
                if (marksman == "1"){
                    titleArray.push('Marksman');
                }
                if(jungle == "1") {
                    titleArray.push('Jungle');
                }
                if(support == "1"){
                    titleArray.push('Support');
                }

                var titleString = "";
                for (var m = 0; m < titleArray.length; m++){
                    if (m == titleArray.length-1){
                        titleString = titleString + titleArray[m];
                    } else {
                        titleString = titleString + titleArray[m] + ", ";
                    }
                }

                jQuery("#hover").append("Preferred Roles in Team: " + titleString);
                jQuery("#hover").show();
            }
        },
        mouseleave: function () {
            if(jQuery(this).hasClass("has")){
                jQuery("#hover").hide();
                jQuery("#hover").empty();
            }
        }
    }, "tr");
}

function accountSettingMenuClick($id){
    // 1 = details
    // 2 = security

    jQuery("#AD").css("text-decoration", "none");
    jQuery("#AS").css("text-decoration", "none");

    var cred;
    jQuery.ajax({
        type: 'POST',
        async: false,
        url: './php/retrieve/getCred.php',
        success: function (data) {
            if (data != "null") {
                cred = JSON.parse(data);
            }
        }
    });

    if($id == 1) {
        // Update Details Section
        jQuery("#AD").css("text-decoration", "underline");
        var adHTML;
        if(cred == "null") {
            adHTML = "<p>Error! Contact Administrator</p>";
        } else {
            var extraHTML;
            var lft = false;
            var tlChecked = "";
            var mlChecked = "";
            var sChecked = "";
            var mChecked = "";
            var jChecked = "";
            if(cred['credentials']['top'] == 1 || cred['credentials']['middle'] == 1 || cred['credentials']['marksman'] == 1 || cred['credentials']['support'] == 1 || cred['credentials']['jungle'] == 1) {
                lft = true;
                if (cred['credentials']['top'] == 1){
                    tlChecked = "checked";
                }
                if (cred['credentials']['middle'] == 1) {
                    mlChecked = "checked";
                }
                if(cred['credentials']['marksman'] == 1) {
                    sChecked = "checked";
                }
                if(cred['credentials']['support'] == 1) {
                    mChecked = "checked";
                }
                if(cred['credentials']['jungle'] == 1){
                    jChecked = "checked";
                }
            }
            if(lft == false) {
                extraHTML = '<tr> <td>Looking For Team: </td> <td><input name="lft" class="lftCheckbox" type="checkbox">  </td> </tr>' +
                    '<tr id="lft" style="display: none;">' + '<th style="vertical-align: top; padding-top: 8px;">Select Lane: </th>' +
                    '<td> <table style="text-align: right; width: 100%; padding-top: 8px;">' +
                    '<tr> <td>Top Lane <input type="checkbox" name="tl" /> </td> </tr>' +
                    '<tr> <td>Middle Lane <input type="checkbox" name="ml" /> </td> </tr>' +
                    '<tr> <td>Support <input type="checkbox" name="s" /> </td> </tr>' +
                    '<tr> <td>Marksman <input type="checkbox" name="m" /> </td> </tr>' +
                    '<tr> <td>Jungle <input type="checkbox" name="j" /> </td> </tr>' +
                    '</table> </td> </tr> </table>';

            } else {

                extraHTML = '<tr> <td>Looking For Team: </td> <td><input name="lft" class="lftCheckbox" type="checkbox" checked ></td> </tr>' +
                    '<tr id="lft" style="display: none;">' + '<th style="vertical-align: top; padding-top: 8px;">Select Lane: </th>' +
                    '<td> <table style="text-align: right; width: 100%; padding-top: 8px;">' +
                    '<tr> <td>Top Lane <input type="checkbox" name="tl" ' + tlChecked + ' /> </td> </tr>' +
                    '<tr> <td>Middle Lane <input type="checkbox" name="ml" ' + mlChecked + ' /> </td> </tr>' +
                    '<tr> <td>Support <input type="checkbox" name="s" ' + sChecked + ' /> </td> </tr>' +
                    '<tr> <td>Marksman <input type="checkbox" name="m" ' + mChecked + ' /> </td> </tr>' +
                    '<tr> <td>Jungle <input type="checkbox" name="j" ' + jChecked + ' /> </td> </tr>' +
                    '</table> </td> </tr> </table>';
            }

            adHTML = "<form id='frmUpdateDet' action='./php/updateCred.php' method='POST'><table class='table'>" +
                "<tr> <td>Name: </td> <td><input class='mainInput' name='name' type='text' value='" + cred['credentials']['name'] + "' placeholder='" + cred['credentials']['name'] + "'/></td> </tr>" +
                "<tr> <td>Surname: </td> <td><input class='mainInput' name='surname' type='text' value='" + cred['credentials']['surname'] + "' placeholder='" + cred['credentials']['surname'] + "' /></td> </tr>" +
                extraHTML +
                "<table class='table'><tr><td><input type='submit' value='Update' class='mainBtn'/></td></tr></table>  </form>";
        }
        jQuery("#content").append(adHTML);
    } else if ($id == 2) {
        // Update Password Section
        jQuery("#AS").css("text-decoration", "underline");
        var asHTML;
        if(cred == "null") {
            asHTML = "<p>Error! Contact Administrator</p>";
        } else {
            asHTML = "<form id='frmUpdatePass' action='./php/updateCred.php' method='POST'><table class='table'>" +
                "<tr> <td>Old Password: </td> <td><input class='mainInput' name='oldPass' type='password' placeholder='Current Password' required/></td> </tr>" +
                "<tr> <td>New Password: </td> <td><input class='mainInput' name='newPass' type='password' placeholder='New Password' required onchange='form.rPass.pattern = this.value;'/></td> </tr>" +
                "<tr> <td>Re-type New Password: </td> <td><input class='mainInput' id='rPass' type='password' placeholder='Re-type New Password' /></td> </tr>" +
                "</table> <table class='table'><tr><td><input type='submit' value='Update' class='mainBtn'/></td></tr></table>  </form>";
        }
        jQuery("#content").append(asHTML);
    } else if ($id == 3) {
        // Create Team Section
        jQuery("#AD").css("text-decoration", "underline");
        var ctHTML;
        if(cred == "null") {
            ctHTML = "<p>Error! Contact Administrator</p>";
        } else if (cred['credentials']['isOwner'] == "1") {
            ctHTML = "<p>Error! Contact Administrator</p>";
        } else {
            asHTML = "</tr></table>  </form>";
        }
    }
}



function AppSettingsCtrl($scope){
    jQuery(document).ready(function (jQuery) {
        jQuery("#AD").click(function () {
            jQuery("#content").empty();
            accountSettingMenuClick(1);
        });

        jQuery("#AS").click(function () {
            jQuery("#content").empty();
            accountSettingMenuClick(2);
        });

        jQuery(document).on('submit', '#frmUpdateDet', function (e) {
            jQuery.ajax({
                type: 'POST',
                url: './php/updateCred.php',
                data: jQuery(this).serialize(),
                async: false,
                success: function (data) {
                    jQuery("#msg").empty();
                    jQuery("#msg").append("<p>" + data + "</p>");
                    jQuery("#msg").show().delay(800).fadeOut();
                }
            });
            e.preventDefault();
        });

        jQuery(document).on('submit','#frmUpdatePass', function (e) {
            jQuery.ajax({
                type: 'POST',
                url: './php/updateCred.php',
                data: jQuery(this).serialize(),
                async: false,
                success: function (data) {
                    jQuery("#msg").empty();
                    jQuery("#msg").append("<p>" + data + "</p>");
                    jQuery("#msg").show().delay(800).fadeOut();
                }
            });
            e.preventDefault();
        });

        jQuery('#content').on('change', 'input:checkbox', function() {
            if(jQuery('.lftCheckbox').is(':checked')){
                jQuery('#lft').show();
            } else {
                jQuery('#lft').hide();
            }
        });
    });
    if (checkSession() == false){
        location = "#/";
    }

    accountSettingMenuClick(1);

    if(jQuery('.lftCheckbox').is(':checked')){
        jQuery('#lft').show();
    }

}

function checkAdmin(){
    var session = new Boolean(false);
    jQuery.ajax({
        type: 'POST',
        async: false,
        url: './php/validation/checkAdmin.php',
        success: function (data) {
            if(data == "100"){
                session = true;
            }
        }
    });
    return session;
}

function AppAdminSettingsCtrl($scope){
    if (checkSession() == false){
        animateLogout()
    } else {
        animateLogin();
    }
    if (checkSession() == false){
        location = "#/";
    } else {
        if(checkAdmin() == false){
            location = "#/";
        }
    }

    // Get Updates
    getUpdate();

    // Get List
    getList();

}

function getList() {
    jQuery.ajax({
        type: 'POST',
        async: false,
        url: './php/retrieve/getUnverifiedPlayers.php',
        success: function (data) {
            if(data == "null"){
                jQuery("#list").append("No Data Found!");
            } else if(data == "1"){
                location = "#/";
            } else {
                var getArray = JSON.parse(data);
                var html = '<table id="board"> <tr> <th>Username</th> <th>Name</th> <th>Surname</th> <th>Summoner Name</th> <th></th> </tr>';

                for (var i = 0; i < getArray['data'].length; i++) {
                    var username = getArray['data'][i]['username'];
                    var name = getArray['data'][i]['name'];
                    var surname = getArray['data'][i]['surname'];
                    var sn = getArray['data'][i]['summonerName'];
                    html = html + '<tr>' +
                        '<td> ' + username + ' </td>' +
                        '<td> ' + name + ' </td>' +
                        '<td> ' + surname + ' </td>' +
                        '<td> ' + sn + ' </td>' +
                        '<td> ' + '<input type="button" value="Verify" onclick="verifyUser(\'' + username + '\')" />' + ' <input type="button" value="Delete" onclick="deleteUser(\'' + username + '\')" />' + ' </td>' +
                        '</tr>';
                }
                html = html + '</table>';
                jQuery('#list').append(html);
            }
        }
    });
}

function getUpdate() {
    jQuery('#update').append('<input type="submit" class="updateList" value="Update List" onclick="updateVList();" /> <br />');
    jQuery.ajax({
        type: 'POST',
        async: false,
        url: './php/retrieve/getUpdate.php',
        success: function (data) {
            if(data == "null"){
                jQuery("#update").append("No Data Found!");
            } else if(data == "1"){
                location = "#/";
            } else {
                var getArray = JSON.parse(data);
                var html = '<table id="board"> <tr> <th>Time</th> <th>Output</th> </tr>';

                for (var i = 0; i < getArray['data'].length; i++) {
                    var time = getArray['data'][i]['time'];
                    var output = getArray['data'][i]['output'];

                    html = html + '<tr>' +
                        '<td> ' + time + ' </td>' +
                        '<td> ' + output + ' </td>' +
                        '</tr>';
                }
                html = html + '</table>';
                jQuery('#update').append(html);
            }
        }
    });
}

function updateVList(){
    jQuery.ajax({
        type: 'POST',
        async: false,
        url: './php/cronjob/updateVerifiedList.php',
        success: function (data) {
            alert(data);
        }
    });
}

function verifyUser(username) {
    jQuery.ajax({
        type: 'POST',
        async: false,
        data: { username: username },
        url: './php/admin/verify.php',
        success: function (data) {
            if(data == "100"){
                jQuery("#msg").empty();
                jQuery("#msg").append("<p>" + "User Added!" + "</p>");
                jQuery("#msg").show().delay(5000).fadeOut();
                jQuery('#list').empty();
                getList();
            } else {
                jQuery("#msg").empty();
                jQuery("#msg").append("<p>" + data + "</p>");
                jQuery("#msg").show().delay(5000).fadeOut();
            }
        }
    });
}

function deleteUser(username) {
    jQuery.ajax({
        type: 'POST',
        async: false,
        data: { username: username },
        url: './php/admin/delete.php',
        success: function (data) {
            if(data == "100"){
                jQuery("#msg").empty();
                jQuery("#msg").append("<p>" + "User Deleted!" + "</p>");
                jQuery("#msg").show().delay(5000).fadeOut();
                jQuery('#list').empty();
                getList();
            } else {
                jQuery("#msg").empty();
                jQuery("#msg").append("<p>" + data + "</p>");
                jQuery("#msg").show().delay(5000).fadeOut();
            }
        }
    });
}
