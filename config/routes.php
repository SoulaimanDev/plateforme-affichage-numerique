<?php

$router->add('GET', '/', 'DashboardController', 'index');

// Routes d'authentification
$router->add('GET', '/login', 'AuthController', 'login');
$router->add('POST', '/authenticate', 'AuthController', 'authenticate');
$router->add('GET', '/logout', 'AuthController', 'logout');

// Routes de réinitialisation de mot de passe
$router->add('GET', '/forgot-password', 'PasswordResetController', 'forgot');
$router->add('POST', '/send-reset', 'PasswordResetController', 'sendReset');
$router->add('GET', '/reset-password', 'PasswordResetController', 'reset');
$router->add('POST', '/update-password', 'PasswordResetController', 'updatePassword');



// Routes API
$router->add('GET', '/api/notifications', 'ApiController', 'notifications');
$router->add('POST', '/api/notifications/read', 'ApiController', 'markNotificationRead');
$router->add('GET', '/api/activity', 'ApiController', 'activity');
$router->add('GET', '/api/stats/diffusions', 'ApiController', 'getDiffusionStats');
$router->add('GET', '/api/export', 'ApiController', 'exportStats');

// Routes utilisateurs (admin seulement)
$router->add('GET', '/users', 'UserController', 'index');
$router->add('GET', '/users/create', 'UserController', 'create');
$router->add('POST', '/users/store', 'UserController', 'store');
$router->add('POST', '/users/update', 'UserController', 'update');
$router->add('POST', '/users/toggle-status', 'UserController', 'toggleStatus');
$router->add('POST', '/users/delete', 'UserController', 'delete');

// Routes protégées
// Routes protégées - Contenus
$router->add('GET', '/contents', 'ContentController', 'index');
$router->add('POST', '/contents/store', 'ContentController', 'store');
$router->add('POST', '/contents/update', 'ContentController', 'update');
$router->add('POST', '/contents/delete', 'ContentController', 'delete');
$router->add('POST', '/contents/copy', 'ContentController', 'copy');

// Routes protégées - Écrans
$router->add('GET', '/screens', 'ScreenController', 'index');
$router->add('POST', '/screens/store', 'ScreenController', 'store');
$router->add('POST', '/screens/update', 'ScreenController', 'update');
$router->add('POST', '/screens/delete', 'ScreenController', 'delete');

// Routes protégées - Zones
$router->add('GET', '/zones', 'ZoneController', 'index');
$router->add('POST', '/zones/store', 'ZoneController', 'store');
$router->add('POST', '/zones/update', 'ZoneController', 'update');
$router->add('POST', '/zones/delete', 'ZoneController', 'delete');

$router->add('GET', '/audiences', 'AudienceController', 'index');
$router->add('POST', '/audiences/store', 'AudienceController', 'store');
$router->add('POST', '/audiences/update', 'AudienceController', 'update');
$router->add('POST', '/audiences/delete', 'AudienceController', 'delete');
$router->add('GET', '/schedules', 'ScheduleController', 'index');
$router->add('POST', '/schedules/store', 'ScheduleController', 'store');
$router->add('POST', '/schedules/update', 'ScheduleController', 'update');
$router->add('POST', '/schedules/delete', 'ScheduleController', 'delete');
$router->add('GET', '/playlists', 'PlaylistController', 'index');
$router->add('POST', '/playlists/store', 'PlaylistController', 'store');
$router->add('POST', '/playlists/update', 'PlaylistController', 'update');
$router->add('POST', '/playlists/delete', 'PlaylistController', 'delete');
$router->add('GET', '/player/{key}', 'PlayerController', 'show');
$router->add('GET', '/player/{key}/json', 'PlayerController', 'json');
$router->add('GET', '/playlists/manage', 'PlaylistController', 'manage');
$router->add('POST', '/playlists/add-content', 'PlaylistController', 'addContent');
$router->add('POST', '/playlists/remove-content', 'PlaylistController', 'removeContent');
