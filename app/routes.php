<?php

/**
 * Register your routes here ...
 */
$app->get('/', ["ChatController", "index"]);
    // ->add("Auth\\UserMiddleware");

$app->group("/auth", function() {
    $this->group("/login", function() {
        $this->get("", ["Auth\\LoginController", "getLogin"])->setName("auth.login");
        $this->post("", ["Auth\\LoginController", "postLogin"]);
    })->add("Auth\\GuestMiddleware");

    $this->post("/logout", ["Auth\\LoginController", "logout"])
        ->setName("auth.logout")
        ->add("Auth\\UserMiddleware");

    $this->group('/account-settings', function() {
        $this->get('', ["Auth\\AccountSettingsController", "getAccountSettings"])->setName('auth.account-settings');
        $this->post('', ["Auth\\AccountSettingsController", "postAccountSettings"]);
    })->add("Auth\\UserMiddleware");

    $this->get("/home", ["Auth\\HomeController", "index"])
    ->setName("auth.home")
    ->add("Auth\\UserMiddleware");
});
