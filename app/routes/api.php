<?php

$this->group("/", function() {
    $this->group("", function() {
        $this->get("search-contacts", ["ChatController", "searchContacts"]);
        $this->post("send-contact-request", ["ChatController", "sendContactRequest"]);
        $this->post("accept-request", ["ChatController", "acceptRequest"]);
        $this->post("read-notification", ["ChatController", "readNotification"]);
        $this->post("send-message/{to_id}", ["ChatController", "sendMessage"]);
        $this->get("conversation/{to_id}", ["ChatController", "getConversation"])->add("Api\\ValidContactMiddleware");
        $this->get("get-messages-by-batch/{to_id}", ["ChatController", "getMessagesByBatch"])->add("Api\\ValidContactMiddleware");
        $this->post("read-message/{to_id}", ["ChatController", "readMessage"])->add("Api\\ValidContactMiddleware");
    })->add("Api\\AuthMiddleware");
}); // use Xhr middleware after the development
