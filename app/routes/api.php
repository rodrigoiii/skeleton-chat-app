<?php

$this->group("/", function() {
    $this->get("search-contacts", ["ChatController", "searchContacts"]);
    $this->post("send-contact-request", ["ChatController", "sendContactRequest"]);
    $this->post("accept-request", ["ChatController", "acceptRequest"]);
    $this->post("read-notification", ["ChatController", "readNotification"]);
    $this->post("send-message/{to_id}", ["ChatController", "sendMessage"])->add("ValidContactMiddleware");
    $this->get("conversation/{to_id}", ["ChatController", "getConversation"])->add("ValidContactMiddleware");
}); // use Xhr middleware after the development
