<?php

/**
 * Register your routes here ...
 */
$app->get('/', ["ExampleController", "index"]);

$app->group("/auth", function() {
    $this->group("/login", function() {
        $this->get("", ["Auth\\LoginController", "getLogin"])->setName("auth.login");
        $this->post("", ["Auth\\LoginController", "postLogin"]);
    })->add("Auth\\GuestMiddleware");

    $this->post("/logout", ["Auth\\LoginController", "logout"])
        ->setName("auth.logout")
        ->add("Auth\\UserMiddleware");

    $this->get("/home", ["Auth\\HomeController", "index"])
    ->setName("auth.home")
    ->add("Auth\\UserMiddleware");
});
