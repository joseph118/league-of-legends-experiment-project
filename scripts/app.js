var mainController = angular.module('app', ['ngRoute']);

mainController.config([
    '$routeProvider', function ($routeProvider) {
        $routeProvider.when('/', {
            templateUrl: 'pages/application.html',
            controller: 'AppCtrl'
        }).when('/sign-in', {
            templateUrl: 'pages/main.html',
            controller: 'AppIndexCtrl'
        }).when('/settings', {
            templateUrl: 'pages/accountsettings.html',
            controller: 'AppSettingsCtrl'
        }).when('/admin', {
                templateUrl: 'pages/admin.html',
                controller: 'AppAdminSettingsCtrl'
        }).otherwise({
            redirectTo: '/'
        });
    }
]);
