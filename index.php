<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <meta charset="utf-8">
    <title>League of Legends Ranking Board</title>
    <link rel="stylesheet" href="css/main1024.css">
    <script src="scripts/jquery-2.1.1.min.js"></script>
    <script src="scripts/angular.min.js"></script>
    <script src="scripts/angular-route.min.js"></script>
    <script src="scripts/main.js"></script>
    <script src="scripts/app.js"></script>
</head>
<body>
<header>
    <table style="float: right;">
        <tr>
            <td>
                <div id='menuBar'>
                    <ul>
                        <li> <a href='#/'>Ranking Board</a> </li>
                        <li class="nlog"> <a href="#/sign-in">Sign In</a> </li>
                        <li class="log"> <a href='#/settings'>Settings</a> </li>
                        <li class="log"> <a href='#/' onclick='signOut();'>Sign Out</a> </li>
                        <li class="va" style="display: hidden;"> <a href='#/admin'>Admin</a></li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr style="height: 30px;"><td></td></tr>
        <tr>
            <td>
                <div id="cred"></div>
            </td>
        </tr>
    </table>
    <div id="logo" />

    <div class="clear"></div>
</header>
<main ng-view>
</main>
<div id="msg"></div>
<div id="hover"></div>
</body>
</html>
