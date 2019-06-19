<?php

$this->group("/", function() {
    $this->get("search-contacts", ["ChatController", "searchContacts"]);
    $this->post("send-contact-request", ["ChatController", "sendContactRequest"]);
    $this->post("accept-request", ["ChatController", "acceptRequest"]);
}); // use Xhr middleware after the development
